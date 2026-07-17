<?php

namespace App\Policies;

use App\Models\Milestone;
use App\Models\Project;
use App\Models\User;

class MilestonePolicy
{
    /**
     * Both the owning client and the assigned freelancer can view milestones.
     */
    public function viewForProject(User $user, Project $project): bool
    {
        return $project->client_id === $user->id
            || $project->freelancer_id === $user->id;
    }

    /**
     * Only the owning client of an in-progress project may manage milestones.
     */
    public function manageForProject(User $user, Project $project): bool
    {
        return $user->role === User::ROLE_CLIENT
            && $project->client_id === $user->id
            && $project->status === Project::STATUS_IN_PROGRESS;
    }

    /**
     * Only the owning client may update a specific milestone (project in progress).
     */
    public function update(User $user, Milestone $milestone): bool
    {
        return $this->manageForProject($user, $milestone->project);
    }

    /**
     * Only the owning client may delete a specific milestone (project in progress).
     */
    public function delete(User $user, Milestone $milestone): bool
    {
        return $this->manageForProject($user, $milestone->project);
    }

    /**
     * Assigned freelancer may start work on a pending milestone.
     */
    public function start(User $user, Milestone $milestone): bool
    {
        $project = $milestone->project;

        return $user->role === User::ROLE_FREELANCER
            && $project->freelancer_id === $user->id
            && $project->status === Project::STATUS_IN_PROGRESS
            && $milestone->canBeStarted();
    }

    /**
     * Assigned freelancer may submit an in-progress milestone.
     */
    public function submit(User $user, Milestone $milestone): bool
    {
        $project = $milestone->project;

        return $user->role === User::ROLE_FREELANCER
            && $project->freelancer_id === $user->id
            && $project->status === Project::STATUS_IN_PROGRESS
            && $milestone->canBeSubmitted();
    }

    /**
     * Owning client may approve or request changes on a submitted milestone.
     */
    public function review(User $user, Milestone $milestone): bool
    {
        $project = $milestone->project;

        return $user->role === User::ROLE_CLIENT
            && $project->client_id === $user->id
            && $project->status === Project::STATUS_IN_PROGRESS
            && $milestone->canBeReviewed();
    }
}
