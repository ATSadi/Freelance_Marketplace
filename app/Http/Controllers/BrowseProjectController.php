<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrowseProjectController extends Controller
{
    /**
     * List open projects available for freelancers to bid on.
     */
    public function index(Request $request): View
    {
        $categories = Project::query()
            ->where('status', Project::STATUS_OPEN)
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $projects = Project::query()
            ->where('status', Project::STATUS_OPEN)
            ->with('client.profile')
            ->withExists(['savedBy as is_saved' => fn ($query) => $query->where('users.id', $request->user()->id)])
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->where(function ($inner) use ($term) {
                    $inner->where('title', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhere('category', 'like', $term);
                });
            })
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->string('category')))
            ->when($request->filled('budget_min'), fn ($query) => $query->where('budget_max', '>=', (float) $request->input('budget_min')))
            ->when($request->filled('budget_max'), fn ($query) => $query->where('budget_min', '<=', (float) $request->input('budget_max')))
            ->when($request->input('sort') === 'budget_high', fn ($query) => $query->orderByDesc('budget_max'))
            ->when($request->input('sort') === 'budget_low', fn ($query) => $query->orderBy('budget_min'))
            ->when($request->input('sort') === 'deadline', fn ($query) => $query->orderBy('deadline'))
            ->when(! $request->filled('sort') || $request->input('sort') === 'newest', fn ($query) => $query->latest())
            ->paginate(12)
            ->withQueryString();

        return view('projects.browse', [
            'projects' => $projects,
            'categories' => $categories,
            'filters' => $request->only(['q', 'category', 'budget_min', 'budget_max', 'sort']),
        ]);
    }
}
