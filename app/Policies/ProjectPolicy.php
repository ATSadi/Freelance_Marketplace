<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Any authenticated user may view project details.
     */
    public function view(?User $user, Project $project): bool
    {
        return $user !== null;
    }

    /**
     * Only clients may list their own projects via the client area.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === User::ROLE_CLIENT;
    }

    public function create(User $user): bool
    {
        return $user->role === User::ROLE_CLIENT;
    }

    /**
     * Only the owning client may update their project.
     */
    public function update(User $user, Project $project): bool
    {
        return $user->role === User::ROLE_CLIENT && $project->client_id === $user->id;
    }

    /**
     * Only the owning client may delete their project.
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->role === User::ROLE_CLIENT && $project->client_id === $user->id;
    }
}
