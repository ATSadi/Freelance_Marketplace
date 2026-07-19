<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SavedProjectController extends Controller
{
    public function index(): View
    {
        abort_unless(Auth::user()->role === User::ROLE_FREELANCER, 403);

        $projects = Auth::user()
            ->savedProjects()
            ->with('client')
            ->where('status', Project::STATUS_OPEN)
            ->latest('saved_projects.created_at')
            ->paginate(12);

        return view('projects.saved', compact('projects'));
    }

    public function toggle(Project $project): RedirectResponse
    {
        abort_unless(Auth::user()->role === User::ROLE_FREELANCER, 403);

        Auth::user()->savedProjects()->toggle($project->id);

        return back()->with('status', 'saved-project-updated');
    }
}
