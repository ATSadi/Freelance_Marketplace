<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Models\Project;
use App\Models\Transaction;
use App\Models\User;
use App\Services\EscrowService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if($user->role === User::ROLE_ADMIN, 403);

        $tab = in_array($request->string('tab')->toString(), ['active', 'completed', 'cancelled'], true)
            ? $request->string('tab')->toString()
            : 'active';

        $baseQuery = Project::query()->whereNotNull('freelancer_id');
        $this->scopeForUser($baseQuery, $user);

        $status = match ($tab) {
            'completed' => Project::STATUS_COMPLETED,
            'cancelled' => Project::STATUS_CANCELLED,
            default => Project::STATUS_IN_PROGRESS,
        };

        $orders = (clone $baseQuery)
            ->where('status', $status)
            ->with(['client', 'freelancer', 'milestones', 'acceptedProposal'])
            ->latest()
            ->paginate(9)
            ->withQueryString();

        $counts = [
            'active' => (clone $baseQuery)->where('status', Project::STATUS_IN_PROGRESS)->count(),
            'completed' => (clone $baseQuery)->where('status', Project::STATUS_COMPLETED)->count(),
            'cancelled' => (clone $baseQuery)->where('status', Project::STATUS_CANCELLED)->count(),
        ];

        return view('orders.index', compact('orders', 'counts', 'tab', 'user'));
    }

    public function show(Project $project): View
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless(in_array($user->id, [$project->client_id, $project->freelancer_id], true), 403);
        abort_if($project->freelancer_id === null, 404);

        $project->load([
            'client.profile',
            'freelancer.profile',
            'milestones',
            'acceptedProposal',
            'reviews.reviewer',
            'disputes' => fn ($query) => $query->latest(),
        ]);

        $released = (float) $project->transactions()
            ->where('type', Transaction::TYPE_ESCROW_RELEASE)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->sum('amount');
        $funded = (float) $project->transactions()
            ->where('type', Transaction::TYPE_ESCROW_HOLD)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->sum('amount');
        $completedMilestones = $project->milestones->filter(
            fn (Milestone $milestone) => $milestone->isCompleted()
        )->count();
        $progress = $project->milestones->isEmpty()
            ? 0
            : (int) round($completedMilestones / $project->milestones->count() * 100);

        return view('orders.show', compact('project', 'user', 'released', 'funded', 'progress'));
    }

    public function cancel(Project $project): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless(in_array($user->id, [$project->client_id, $project->freelancer_id], true), 403);
        abort_unless($project->status === Project::STATUS_IN_PROGRESS, 422);

        $project->update(['status' => Project::STATUS_CANCELLED]);
        app(EscrowService::class)->refundOpenHolds($project->fresh(['milestones']));

        return redirect()
            ->route('orders.index', ['tab' => 'cancelled'])
            ->with('status', 'order-cancelled');
    }

    private function scopeForUser(Builder $query, User $user): void
    {
        $column = $user->role === User::ROLE_CLIENT ? 'client_id' : 'freelancer_id';
        $query->where($column, $user->id);
    }
}
