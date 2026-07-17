<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMilestoneRequest;
use App\Http\Requests\UpdateMilestoneRequest;
use App\Models\Milestone;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MilestoneController extends Controller
{
    /**
     * List milestones for a project (client + assigned freelancer).
     */
    public function index(Project $project): View
    {
        $this->authorize('viewForProject', [Milestone::class, $project]);

        $project->load('milestones', 'client', 'freelancer');

        return view('milestones.index', compact('project'));
    }

    public function create(Project $project): View
    {
        $this->authorize('manageForProject', [Milestone::class, $project]);

        return view('milestones.create', compact('project'));
    }

    public function store(StoreMilestoneRequest $request, Project $project): RedirectResponse
    {
        $data = $request->validated();
        $data['order_index'] = $data['order_index']
            ?? ((int) $project->milestones()->max('order_index') + 1);
        $data['status'] = Milestone::STATUS_PENDING;

        $project->milestones()->create($data);

        return redirect()
            ->route('client.projects.milestones.index', $project)
            ->with('status', 'milestone-created');
    }

    public function edit(Project $project, Milestone $milestone): View
    {
        $this->authorize('update', $milestone);

        return view('milestones.edit', compact('project', 'milestone'));
    }

    public function update(UpdateMilestoneRequest $request, Project $project, Milestone $milestone): RedirectResponse
    {
        abort_unless($milestone->status === Milestone::STATUS_PENDING, 403);

        $milestone->update($request->validated());

        return redirect()
            ->route('client.projects.milestones.index', $project)
            ->with('status', 'milestone-updated');
    }

    public function destroy(Project $project, Milestone $milestone): RedirectResponse
    {
        $this->authorize('delete', $milestone);
        abort_unless($milestone->status === Milestone::STATUS_PENDING, 403);

        $milestone->delete();

        return redirect()
            ->route('client.projects.milestones.index', $project)
            ->with('status', 'milestone-deleted');
    }
}
