<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\View\View;

class BrowseProjectController extends Controller
{
    /**
     * List open projects available for freelancers to bid on.
     */
    public function index(): View
    {
        $projects = Project::query()
            ->where('status', Project::STATUS_OPEN)
            ->with('client.profile')
            ->latest()
            ->get();

        return view('projects.browse', compact('projects'));
    }
}
