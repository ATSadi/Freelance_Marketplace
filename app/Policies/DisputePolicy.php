<?php

namespace App\Policies;

use App\Models\Dispute;
use App\Models\Project;
use App\Models\User;

class DisputePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [User::ROLE_CLIENT, User::ROLE_FREELANCER, User::ROLE_ADMIN], true);
    }

    public function create(User $user, Project $project): bool
    {
        if ($project->status !== Project::STATUS_IN_PROGRESS) {
            return false;
        }

        return $project->client_id === $user->id || $project->freelancer_id === $user->id;
    }

    public function view(User $user, Dispute $dispute): bool
    {
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        return $dispute->opened_by === $user->id
            || $dispute->against_user_id === $user->id
            || $dispute->project->client_id === $user->id
            || $dispute->project->freelancer_id === $user->id;
    }

    public function moderate(User $user, Dispute $dispute): bool
    {
        return $user->role === User::ROLE_ADMIN && $dispute->isOpen();
    }
}
