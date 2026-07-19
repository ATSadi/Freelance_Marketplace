<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse
    {
        abort_unless($project->status === Project::STATUS_COMPLETED, 422);
        abort_unless(in_array(auth()->id(), [$project->client_id, $project->freelancer_id], true), 403);

        $revieweeId = auth()->id() === $project->client_id
            ? $project->freelancer_id
            : $project->client_id;

        abort_unless($revieweeId, 422);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        Review::updateOrCreate(
            ['project_id' => $project->id, 'reviewer_id' => auth()->id()],
            [...$data, 'reviewee_id' => $revieweeId]
        );

        return back()->with('status', 'review-submitted');
    }
}
