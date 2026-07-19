<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FreelancerDirectoryController extends Controller
{
    public function __invoke(Request $request): View
    {
        $freelancers = User::query()
            ->where('role', User::ROLE_FREELANCER)
            ->where('is_active', true)
            ->with('profile')
            ->withCount('receivedReviews')
            ->withAvg('receivedReviews', 'rating')
            ->when($request->string('q')->isNotEmpty(), fn ($query) => $query
                ->where(fn ($search) => $search
                    ->where('name', 'ilike', '%'.$request->string('q').'%')
                    ->orWhereHas('profile', fn ($profile) => $profile
                        ->where('skills', 'ilike', '%'.$request->string('q').'%'))))
            ->orderByDesc('received_reviews_avg_rating')
            ->paginate(12)
            ->withQueryString();

        return view('freelancers.index', compact('freelancers'));
    }
}
