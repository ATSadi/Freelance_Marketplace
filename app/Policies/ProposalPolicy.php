<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;

class ProposalPolicy
{
    /**
     * Only the owning client of an open project may accept/reject a proposal.
     */
    public function manage(User $user, Proposal $proposal): bool
    {
        $project = $proposal->project;

        return $user->role === User::ROLE_CLIENT
            && $project !== null
            && $project->client_id === $user->id
            && $project->status === Project::STATUS_OPEN
            && $proposal->status === Proposal::STATUS_PENDING;
    }
}
