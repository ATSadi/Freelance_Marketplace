<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProposalRequest;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use App\Notifications\ProposalAcceptedNotification;
use App\Notifications\ProposalSubmittedNotification;
use App\Services\EscrowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProposalController extends Controller
{
    /**
     * List proposals submitted by the authenticated freelancer.
     */
    public function index(): View
    {
        $proposals = $this->authUser()
            ->proposals()
            ->with('project.client')
            ->latest()
            ->get();

        return view('proposals.index', compact('proposals'));
    }

    /**
     * Store a new proposal for an open project.
     */
    public function store(StoreProposalRequest $request, Project $project): RedirectResponse
    {
        $proposal = $this->authUser()->proposals()->create([
            'project_id' => $project->id,
            ...$request->safe()->only(['cover_letter', 'proposed_amount', 'proposed_duration_days']),
            'status' => Proposal::STATUS_PENDING,
        ]);

        $proposal->load(['freelancer', 'project']);
        $project->client->notify(new ProposalSubmittedNotification($proposal));

        return redirect()
            ->route('freelancer.proposals.index')
            ->with('status', 'proposal-submitted');
    }

    /**
     * Accept a proposal: assign the freelancer, reject the rest, and start the project.
     */
    public function accept(Proposal $proposal): RedirectResponse
    {
        $this->authorize('manage', $proposal);

        $project = $proposal->project;
        $escrow = app(EscrowService::class);

        DB::transaction(function () use ($proposal, $project, $escrow): void {
            // Reject every other proposal on the same project.
            Proposal::query()
                ->where('project_id', $project->id)
                ->where('id', '!=', $proposal->id)
                ->update(['status' => Proposal::STATUS_REJECTED]);

            $proposal->update(['status' => Proposal::STATUS_ACCEPTED]);

            $project->update([
                'status' => Project::STATUS_IN_PROGRESS,
                'freelancer_id' => $proposal->freelancer_id,
            ]);

            // Seed a starter delivery plan so Orders is never empty after hire.
            if ($project->milestones()->doesntExist()) {
                $amount = (float) $proposal->proposed_amount;
                $first = round($amount * 0.4, 2);
                $second = round($amount * 0.35, 2);
                $third = round($amount - $first - $second, 2);

                foreach ([
                    [1, 'Discovery & kickoff', 'Align on scope, deliverables, and communication cadence.', $first, 7],
                    [2, 'Main delivery', 'Complete the core work package for client review.', $second, 21],
                    [3, 'Revisions & handoff', 'Apply feedback and deliver final assets / launch support.', $third, 30],
                ] as [$index, $title, $description, $milestoneAmount, $days]) {
                    if ($milestoneAmount <= 0) {
                        continue;
                    }

                    $milestone = $project->milestones()->create([
                        'title' => $title,
                        'description' => $description,
                        'amount' => $milestoneAmount,
                        'due_date' => now()->addDays($days),
                        'order_index' => $index,
                        'status' => Milestone::STATUS_PENDING,
                    ]);

                    $escrow->hold($milestone);
                }
            }
        });

        $proposal->load(['freelancer', 'project']);
        $proposal->freelancer->notify(new ProposalAcceptedNotification($proposal));

        return redirect()
            ->route('orders.show', $project)
            ->with('status', 'proposal-accepted');
    }

    /**
     * Reject a single pending proposal.
     */
    public function reject(Proposal $proposal): RedirectResponse
    {
        $this->authorize('manage', $proposal);

        $proposal->update(['status' => Proposal::STATUS_REJECTED]);

        return redirect()
            ->route('projects.show', $proposal->project)
            ->with('status', 'proposal-rejected');
    }

    private function authUser(): User
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(403);
        }

        return $user;
    }
}
