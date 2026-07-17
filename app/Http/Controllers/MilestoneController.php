<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewMilestoneRequest;
use App\Http\Requests\StoreMilestoneRequest;
use App\Http\Requests\SubmitMilestoneRequest;
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

    /**
     * Freelancer starts work on a pending milestone.
     */
    public function start(Project $project, Milestone $milestone): RedirectResponse
    {
        $this->authorize('start', $milestone);

        $milestone->update([
            'status' => Milestone::STATUS_IN_PROGRESS,
            'started_at' => now(),
            'client_feedback' => null,
        ]);

        return redirect()
            ->route('projects.show', $project)
            ->with('status', 'milestone-started');
    }

    /**
     * Freelancer submits completed work for client review.
     */
    public function submit(SubmitMilestoneRequest $request, Project $project, Milestone $milestone): RedirectResponse
    {
        $milestone->update([
            'status' => Milestone::STATUS_SUBMITTED,
            'submission_notes' => $request->validated('submission_notes'),
            'submitted_at' => now(),
            'client_feedback' => null,
        ]);

        return redirect()
            ->route('projects.show', $project)
            ->with('status', 'milestone-submitted');
    }

    /**
     * Client approves a submitted milestone.
     */
    public function approve(ReviewMilestoneRequest $request, Project $project, Milestone $milestone): RedirectResponse
    {
        $milestone->update([
            'status' => Milestone::STATUS_APPROVED,
            'client_feedback' => $request->validated('client_feedback'),
            'approved_at' => now(),
        ]);

        $this->completeProjectIfReady($project);

        return redirect()
            ->route('projects.show', $project)
            ->with('status', 'milestone-approved');
    }

    /**
     * Client requests changes; milestone returns to in progress.
     */
    public function requestChanges(ReviewMilestoneRequest $request, Project $project, Milestone $milestone): RedirectResponse
    {
        $milestone->update([
            'status' => Milestone::STATUS_IN_PROGRESS,
            'client_feedback' => $request->validated('client_feedback'),
            'submitted_at' => null,
        ]);

        return redirect()
            ->route('projects.show', $project)
            ->with('status', 'milestone-changes-requested');
    }

    /**
     * Mark the project completed once every milestone is approved or paid.
     */
    private function completeProjectIfReady(Project $project): void
    {
        $project->load('milestones');

        if ($project->milestones->isEmpty()) {
            return;
        }

        $allDone = $project->milestones->every(fn (Milestone $m) => $m->isCompleted());

        if ($allDone) {
            $project->update(['status' => Project::STATUS_COMPLETED]);
        }
    }
}
