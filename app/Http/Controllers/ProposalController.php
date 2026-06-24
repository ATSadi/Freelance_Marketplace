<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProposalRequest;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
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

    private function authUser(): User
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(403);
        }

        return $user;
    }
}
