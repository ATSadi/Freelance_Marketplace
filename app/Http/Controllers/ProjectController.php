<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * List the authenticated client's posted projects.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Project::class);

        $projects = $this->authUser()
            ->projects()
            ->latest()
            ->get();

        return view('projects.index', compact('projects'));
    }

    public function create(): View
    {
        $this->authorize('create', Project::class);

        return view('projects.create');
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $project = $this->authUser()->projects()->create($request->validated());

        return redirect()
            ->route('projects.show', $project)
            ->with('status', 'project-created');
    }

    /**
     * Show project details (viewable by any authenticated user).
     */
    public function show(Project $project): View
    {
        $this->authorize('view', $project);

        $project->load('client.profile');

        return view('projects.show', compact('project'));
    }

    public function edit(Project $project): View
    {
        $this->authorize('update', $project);

        return view('projects.edit', compact('project'));
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $project->update($request->validated());

        return redirect()
            ->route('projects.show', $project)
            ->with('status', 'project-updated');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()
            ->route('client.projects.index')
            ->with('status', 'project-deleted');
    }

    /**
     * Get the authenticated user with a concrete type for static analysis.
     */
    private function authUser(): User
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(403);
        }

        return $user;
    }
}
