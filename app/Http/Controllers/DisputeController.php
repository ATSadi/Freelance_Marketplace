<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResolveDisputeRequest;
use App\Http\Requests\StoreDisputeRequest;
use App\Models\Dispute;
use App\Models\Project;
use App\Models\User;
use App\Notifications\DisputeOpenedNotification;
use App\Services\EscrowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DisputeController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Dispute::class);

        /** @var User $user */
        $user = Auth::user();

        $query = Dispute::query()->with(['project', 'opener', 'againstUser', 'milestone']);

        if ($user->role === User::ROLE_ADMIN) {
            $disputes = $query->latest()->paginate(20);
        } else {
            $disputes = $query
                ->where(function ($q) use ($user) {
                    $q->where('opened_by', $user->id)
                        ->orWhere('against_user_id', $user->id);
                })
                ->latest()
                ->paginate(15);
        }

        return view('disputes.index', compact('disputes', 'user'));
    }

    public function create(Project $project): View
    {
        $this->authorize('create', [Dispute::class, $project]);

        $project->load('milestones');

        return view('disputes.create', compact('project'));
    }

    public function store(StoreDisputeRequest $request, Project $project): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $againstId = $project->client_id === $user->id
            ? $project->freelancer_id
            : $project->client_id;

        abort_unless($againstId, 422);

        if ($request->filled('milestone_id')) {
            abort_unless(
                $project->milestones()->where('id', $request->input('milestone_id'))->exists(),
                422
            );
        }

        $dispute = $project->disputes()->create([
            'milestone_id' => $request->validated('milestone_id'),
            'opened_by' => $user->id,
            'against_user_id' => $againstId,
            'reason' => $request->validated('reason'),
            'description' => $request->validated('description'),
            'status' => Dispute::STATUS_OPEN,
        ]);

        $dispute->load(['project', 'againstUser']);
        $dispute->againstUser->notify(new DisputeOpenedNotification($dispute));

        User::query()
            ->where('role', User::ROLE_ADMIN)
            ->each(fn (User $admin) => $admin->notify(new DisputeOpenedNotification($dispute)));

        return redirect()
            ->route('disputes.index')
            ->with('status', 'dispute-opened');
    }

    public function show(Dispute $dispute): View
    {
        $this->authorize('view', $dispute);

        $dispute->load(['project', 'milestone', 'opener', 'againstUser', 'resolver']);

        return view('disputes.show', compact('dispute'));
    }

    public function review(Dispute $dispute): RedirectResponse
    {
        $this->authorize('moderate', $dispute);

        $dispute->update(['status' => Dispute::STATUS_UNDER_REVIEW]);

        return redirect()
            ->route('disputes.show', $dispute)
            ->with('status', 'dispute-under-review');
    }

    public function resolve(ResolveDisputeRequest $request, Dispute $dispute): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $action = $request->validated('financial_action');
        $escrow = app(EscrowService::class);

        DB::transaction(function () use ($request, $dispute, $user, $action, $escrow): void {
            $dispute->update([
                'status' => $request->validated('status'),
                'admin_notes' => $request->validated('admin_notes'),
                'resolved_by' => $user->id,
                'resolved_at' => now(),
            ]);

            $project = $dispute->project()->with('milestones')->firstOrFail();
            $milestone = $dispute->milestone;

            match ($action) {
                'release' => $milestone ? $escrow->release($milestone) : null,
                'refund' => $milestone
                    ? $escrow->refund($milestone, 'Escrow refunded after dispute resolution')
                    : $escrow->refundOpenHolds($project),
                'cancel_project' => tap($escrow->refundOpenHolds($project), function () use ($project): void {
                    $project->update(['status' => Project::STATUS_CANCELLED]);
                }),
                default => null,
            };
        });

        return redirect()
            ->route('admin.disputes.index')
            ->with('status', 'dispute-resolved');
    }
}
