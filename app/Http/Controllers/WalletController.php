<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Models\PayoutMethod;
use App\Models\Project;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WithdrawalRequest;
use App\Notifications\WithdrawalRequestedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user->role === User::ROLE_FREELANCER, 403);

        $released = $this->releasedTotal($user);
        $reserved = $this->reservedTotal($user);
        $available = max(0, $released - $reserved);

        $pendingPayments = Transaction::query()
            ->with(['project', 'milestone'])
            ->where('payee_id', $user->id)
            ->where('type', Transaction::TYPE_ESCROW_HOLD)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->whereHas('project', fn ($query) => $query->where('status', Project::STATUS_IN_PROGRESS))
            ->whereDoesntHave('milestone', fn ($query) => $query->where('status', Milestone::STATUS_PAID))
            ->latest()
            ->get();

        return view('wallet.index', [
            'user' => $user,
            'released' => $released,
            'reserved' => $reserved,
            'available' => $available,
            'pendingPayments' => $pendingPayments,
            'payoutMethods' => $user->payoutMethods()->latest()->get(),
            'withdrawals' => $user->withdrawalRequests()->with('payoutMethod')->latest()->paginate(10),
            'paymentHistory' => Transaction::query()
                ->with(['project', 'milestone'])
                ->where('payee_id', $user->id)
                ->where('type', Transaction::TYPE_ESCROW_RELEASE)
                ->where('status', Transaction::STATUS_COMPLETED)
                ->latest()
                ->take(10)
                ->get(),
        ]);
    }

    public function storePayoutMethod(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user->role === User::ROLE_FREELANCER, 403);

        $data = $request->validate([
            'type' => ['required', 'in:bank,mobile_wallet'],
            'account_name' => ['required', 'string', 'max:120'],
            'bank_name' => ['required', 'string', 'max:120'],
            'account_number' => ['required', 'string', 'min:4', 'max:64', 'regex:/^[A-Za-z0-9 -]+$/'],
            'routing_number' => ['nullable', 'string', 'max:64', 'regex:/^[A-Za-z0-9 -]+$/'],
            'country' => ['required', 'string', 'size:2'],
            'currency' => ['required', 'string', 'size:3'],
        ]);

        $normalizedAccount = preg_replace('/[\s-]+/', '', $data['account_number']);

        DB::transaction(function () use ($user, $data, $normalizedAccount) {
            $user->payoutMethods()->update(['is_default' => false]);
            $user->payoutMethods()->create([
                ...$data,
                'account_number' => $normalizedAccount,
                'routing_number' => filled($data['routing_number'] ?? null)
                    ? preg_replace('/[\s-]+/', '', $data['routing_number'])
                    : null,
                'account_last_four' => substr($normalizedAccount, -4),
                'country' => strtoupper($data['country']),
                'currency' => strtoupper($data['currency']),
                'is_default' => true,
            ]);
        });

        return back()->with('status', 'payout-method-added');
    }

    public function destroyPayoutMethod(PayoutMethod $payoutMethod): RedirectResponse
    {
        abort_unless($payoutMethod->user_id === Auth::id(), 403);

        if ($payoutMethod->withdrawals()->exists()) {
            return back()->withErrors(['payout_method' => 'This method is attached to withdrawal history and cannot be deleted.']);
        }

        $payoutMethod->delete();

        return back()->with('status', 'payout-method-removed');
    }

    public function requestWithdrawal(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user->role === User::ROLE_FREELANCER, 403);

        $data = $request->validate([
            'payout_method_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'min:10'],
        ]);

        $withdrawal = DB::transaction(function () use ($user, $data) {
            User::query()->lockForUpdate()->findOrFail($user->id);
            $method = $user->payoutMethods()->findOrFail($data['payout_method_id']);
            $available = max(0, $this->releasedTotal($user) - $this->reservedTotal($user));

            if ((float) $data['amount'] > $available) {
                throw ValidationException::withMessages([
                    'amount' => 'The requested amount exceeds your available balance of $'.number_format($available, 2).'.',
                ]);
            }

            return $user->withdrawalRequests()->create([
                'payout_method_id' => $method->id,
                'amount' => $data['amount'],
                'status' => WithdrawalRequest::STATUS_PENDING,
            ]);
        });

        $withdrawal->load('user');
        User::query()
            ->where('role', User::ROLE_ADMIN)
            ->each(fn (User $admin) => $admin->notify(new WithdrawalRequestedNotification($withdrawal)));

        return back()->with('status', 'withdrawal-requested');
    }

    private function releasedTotal(User $user): float
    {
        return (float) Transaction::query()
            ->where('payee_id', $user->id)
            ->where('type', Transaction::TYPE_ESCROW_RELEASE)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->sum('amount');
    }

    private function reservedTotal(User $user): float
    {
        return (float) WithdrawalRequest::query()
            ->where('user_id', $user->id)
            ->whereIn('status', [
                WithdrawalRequest::STATUS_PENDING,
                WithdrawalRequest::STATUS_APPROVED,
                WithdrawalRequest::STATUS_PAID,
            ])
            ->sum('amount');
    }
}
