<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Models\Project;
use App\Models\StripePayment;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TransactionController extends Controller
{
    /**
     * Show transaction history for the authenticated client or freelancer.
     */
    public function index(): View
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(403);
        }

        $transactions = Transaction::query()
            ->with(['project', 'milestone', 'payer', 'payee'])
            ->where(function ($query) use ($user) {
                $query->where('payer_id', $user->id)
                    ->orWhere('payee_id', $user->id);
            })
            ->latest()
            ->paginate(15);

        $totalHeld = (float) Transaction::query()
            ->where('payer_id', $user->id)
            ->where('type', Transaction::TYPE_ESCROW_HOLD)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->sum('amount')
            - (float) Transaction::query()
                ->where('payer_id', $user->id)
                ->whereIn('type', [Transaction::TYPE_ESCROW_RELEASE, Transaction::TYPE_REFUND])
                ->where('status', Transaction::STATUS_COMPLETED)
                ->sum('amount');

        $totalReleased = (float) Transaction::query()
            ->where('payer_id', $user->id)
            ->where('type', Transaction::TYPE_ESCROW_RELEASE)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->sum('amount');

        $totalEarned = (float) Transaction::query()
            ->where('payee_id', $user->id)
            ->where('type', Transaction::TYPE_ESCROW_RELEASE)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->sum('amount');

        $fundableMilestones = collect();
        $stripePayments = collect();

        if ($user->role === User::ROLE_CLIENT) {
            $fundableMilestones = Milestone::query()
                ->with(['project.freelancer'])
                ->whereHas('project', fn ($query) => $query
                    ->where('client_id', $user->id)
                    ->where('status', Project::STATUS_IN_PROGRESS))
                ->where('status', '!=', Milestone::STATUS_PAID)
                ->whereDoesntHave('stripePayments', fn ($query) => $query
                    ->where('status', StripePayment::STATUS_PAID))
                ->orderBy('due_date')
                ->get();

            $stripePayments = StripePayment::query()
                ->with('milestone.project')
                ->where('user_id', $user->id)
                ->latest()
                ->take(10)
                ->get();
        }

        return view('transactions.index', [
            'transactions' => $transactions,
            'totalHeld' => max(0, $totalHeld),
            'totalReleased' => $totalReleased,
            'totalEarned' => $totalEarned,
            'fundableMilestones' => $fundableMilestones,
            'stripePayments' => $stripePayments,
            'stripeConfigured' => filled(config('services.stripe.secret')),
            'user' => $user,
        ]);
    }
}
