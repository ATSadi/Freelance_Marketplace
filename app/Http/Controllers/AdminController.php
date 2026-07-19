<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\StripePayment;
use App\Models\Transaction;
use App\Models\User;
use App\Services\EscrowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function users(Request $request): View
    {
        $users = User::query()
            ->withCount(['projects', 'proposals', 'receivedReviews'])
            ->when($request->string('q')->isNotEmpty(), fn ($query) => $query
                ->where(fn ($search) => $search
                    ->where('name', 'ilike', '%'.$request->string('q').'%')
                    ->orWhere('email', 'ilike', '%'.$request->string('q').'%')))
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->string('role')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function toggleUser(User $user): RedirectResponse
    {
        abort_if($user->is(auth()->user()), 422, 'You cannot suspend your own account.');

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('status', $user->is_active ? 'user-activated' : 'user-suspended');
    }

    public function projects(Request $request): View
    {
        $projects = Project::query()
            ->with(['client', 'freelancer'])
            ->withCount(['proposals', 'milestones', 'disputes'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->string('q')->isNotEmpty(), fn ($query) => $query
                ->where('title', 'ilike', '%'.$request->string('q').'%'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.projects', compact('projects'));
    }

    public function updateProject(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:open,in_progress,completed,cancelled'],
        ]);

        $previous = $project->status;
        $project->update($data);

        if ($previous !== Project::STATUS_CANCELLED && $data['status'] === Project::STATUS_CANCELLED) {
            app(EscrowService::class)->refundOpenHolds($project->fresh(['milestones']));
        }

        return back()->with('status', 'project-moderated');
    }

    public function payments(): View
    {
        $transactions = Transaction::query()
            ->with(['project', 'milestone', 'payer', 'payee'])
            ->latest()
            ->paginate(20);

        return view('admin.payments', [
            'transactions' => $transactions,
            'stripePayments' => StripePayment::query()->with(['milestone.project', 'user'])->latest()->take(20)->get(),
            'releasedTotal' => Transaction::query()
                ->where('type', Transaction::TYPE_ESCROW_RELEASE)
                ->where('status', Transaction::STATUS_COMPLETED)
                ->sum('amount'),
        ]);
    }
}
