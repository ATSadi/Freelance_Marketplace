<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Models\StripePayment;
use App\Services\EscrowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\Response;
use UnexpectedValueException;

class StripePaymentController extends Controller
{
    public function checkout(Milestone $milestone): RedirectResponse
    {
        abort_unless(auth()->id() === $milestone->project->client_id, 403);

        if (! config('services.stripe.secret')) {
            return back()->withErrors(['stripe' => 'Stripe test keys are not configured. Add STRIPE_KEY and STRIPE_SECRET to .env.']);
        }

        $payment = StripePayment::firstOrCreate(
            ['milestone_id' => $milestone->id, 'user_id' => auth()->id(), 'status' => StripePayment::STATUS_PENDING],
            ['amount' => (int) round((float) $milestone->amount * 100), 'currency' => 'usd']
        );

        $stripe = new StripeClient(config('services.stripe.secret'));
        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'customer_email' => auth()->user()->email,
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => $payment->currency,
                    'unit_amount' => $payment->amount,
                    'product_data' => [
                        'name' => 'Milestone funding: '.$milestone->title,
                        'description' => $milestone->project->title,
                    ],
                ],
            ]],
            'metadata' => ['stripe_payment_id' => (string) $payment->id],
            'success_url' => route('stripe.success', $payment).'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('projects.show', $milestone->project).'?payment=cancelled',
        ]);

        $payment->update(['stripe_session_id' => $session->id]);

        return redirect()->away($session->url);
    }

    public function success(Request $request, StripePayment $payment): RedirectResponse
    {
        abort_unless($payment->user_id === auth()->id(), 403);

        if ($request->filled('session_id') && config('services.stripe.secret')) {
            $stripe = new StripeClient(config('services.stripe.secret'));
            $session = $stripe->checkout->sessions->retrieve($request->string('session_id')->toString());

            if ($session->payment_status === 'paid') {
                $this->markPaid($payment, $session->payment_intent);
            }
        }

        return redirect()
            ->route('projects.show', $payment->milestone->project)
            ->with('status', 'stripe-funded');
    }

    public function webhook(Request $request): Response
    {
        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature', ''),
                config('services.stripe.webhook_secret', '')
            );
        } catch (UnexpectedValueException|SignatureVerificationException $exception) {
            Log::warning('Rejected Stripe webhook', ['message' => $exception->getMessage()]);

            return response('Invalid webhook', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $payment = StripePayment::find($session->metadata->stripe_payment_id ?? null);

            if ($payment && $session->payment_status === 'paid') {
                $this->markPaid($payment, $session->payment_intent);
            }
        }

        return response('ok');
    }

    private function markPaid(StripePayment $payment, ?string $paymentIntent): void
    {
        $payment->update([
            'status' => StripePayment::STATUS_PAID,
            'stripe_payment_intent_id' => $paymentIntent,
        ]);

        // Keep the mock escrow ledger aligned with a successful Stripe funding event.
        $milestone = $payment->milestone()->with('project')->first();
        if ($milestone) {
            app(EscrowService::class)->hold($milestone);
        }
    }
}
