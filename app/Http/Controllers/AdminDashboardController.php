<?php

namespace App\Http\Controllers;

use App\Models\Dispute;
use App\Models\Project;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WithdrawalRequest;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'userCount' => User::query()->where('role', '!=', User::ROLE_ADMIN)->count(),
            'clientCount' => User::query()->where('role', User::ROLE_CLIENT)->count(),
            'freelancerCount' => User::query()->where('role', User::ROLE_FREELANCER)->count(),
            'projectCount' => Project::query()->count(),
            'openProjects' => Project::query()->where('status', Project::STATUS_OPEN)->count(),
            'activeProjects' => Project::query()->where('status', Project::STATUS_IN_PROGRESS)->count(),
            'completedProjects' => Project::query()->where('status', Project::STATUS_COMPLETED)->count(),
            'openDisputes' => Dispute::query()->whereIn('status', [Dispute::STATUS_OPEN, Dispute::STATUS_UNDER_REVIEW])->count(),
            'pendingWithdrawals' => WithdrawalRequest::query()->where('status', WithdrawalRequest::STATUS_PENDING)->count(),
            'pendingWithdrawalTotal' => (float) WithdrawalRequest::query()->where('status', WithdrawalRequest::STATUS_PENDING)->sum('amount'),
            'totalEscrowReleased' => (float) Transaction::query()
                ->where('type', Transaction::TYPE_ESCROW_RELEASE)
                ->where('status', Transaction::STATUS_COMPLETED)
                ->sum('amount'),
            'recentDisputes' => Dispute::query()
                ->with(['project', 'opener'])
                ->latest()
                ->take(5)
                ->get(),
            'recentProjects' => Project::query()
                ->with(['client', 'freelancer'])
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}
