<?php

namespace App\Http\Controllers;

use App\Models\WithdrawalRequest;
use App\Notifications\WithdrawalStatusNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminWithdrawalController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $withdrawals = WithdrawalRequest::query()
            ->with(['user', 'payoutMethod', 'processor'])
            ->when(in_array($status, ['pending', 'approved', 'rejected', 'paid'], true), fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.withdrawals', [
            'withdrawals' => $withdrawals,
            'status' => $status,
            'pendingTotal' => WithdrawalRequest::query()->where('status', WithdrawalRequest::STATUS_PENDING)->sum('amount'),
            'approvedTotal' => WithdrawalRequest::query()->where('status', WithdrawalRequest::STATUS_APPROVED)->sum('amount'),
            'paidTotal' => WithdrawalRequest::query()->where('status', WithdrawalRequest::STATUS_PAID)->sum('amount'),
        ]);
    }

    public function update(Request $request, WithdrawalRequest $withdrawal): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected,paid'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $allowed = match ($withdrawal->status) {
            WithdrawalRequest::STATUS_PENDING => [
                WithdrawalRequest::STATUS_APPROVED,
                WithdrawalRequest::STATUS_REJECTED,
            ],
            WithdrawalRequest::STATUS_APPROVED => [
                WithdrawalRequest::STATUS_PAID,
                WithdrawalRequest::STATUS_REJECTED,
            ],
            default => [],
        };

        if (! in_array($data['status'], $allowed, true)) {
            throw ValidationException::withMessages([
                'status' => 'This withdrawal cannot move from '.$withdrawal->status.' to '.$data['status'].'.',
            ]);
        }

        $withdrawal->update([
            ...$data,
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);
        $withdrawal->user->notify(new WithdrawalStatusNotification($withdrawal));

        return back()->with('status', 'withdrawal-updated');
    }
}
