<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProposalRequest;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
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
        $this->authUser()->proposals()->create([
            'project_id' => $project->id,
            ...$request->safe()->only(['cover_letter', 'proposed_amount', 'proposed_duration_days']),
            'status' => Proposal::STATUS_PENDING,
        ]);

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

        DB::transaction(function () use ($proposal, $project): void {
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
        });

        return redirect()
            ->route('projects.show', $project)
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
