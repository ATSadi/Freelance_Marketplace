<?php

use App\Http\Controllers\BrowseProjectController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProposalController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    /** @var \App\Models\User $user */
    $user = Auth::user();

    return redirect($user->dashboardRoute());
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', 'role:client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', function () {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load('profile');

        return view('client.dashboard', [
            'user' => $user,
            'projectCount' => $user->projects()->count(),
            'activeProjects' => $user->projects()
                ->where('status', \App\Models\Project::STATUS_IN_PROGRESS)
                ->with('freelancer')
                ->latest()
                ->get(),
        ]);
    })->name('dashboard');

    Route::resource('projects', ProjectController::class)->except(['show']);

    Route::resource('projects.milestones', MilestoneController::class)
        ->except(['show'])
        ->scoped();

    Route::post('/proposals/{proposal}/accept', [ProposalController::class, 'accept'])->name('proposals.accept');
    Route::post('/proposals/{proposal}/reject', [ProposalController::class, 'reject'])->name('proposals.reject');
});

Route::middleware(['auth', 'verified', 'role:freelancer'])->prefix('freelancer')->name('freelancer.')->group(function () {
    Route::get('/dashboard', function () {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load('profile');

        return view('freelancer.dashboard', [
            'user' => $user,
            'proposalCount' => $user->proposals()->count(),
            'activeProjects' => $user->assignedProjects()
                ->where('status', \App\Models\Project::STATUS_IN_PROGRESS)
                ->with('client')
                ->latest()
                ->get(),
        ]);
    })->name('dashboard');

    Route::get('/projects/browse', [BrowseProjectController::class, 'index'])->name('projects.browse');
    Route::get('/proposals', [ProposalController::class, 'index'])->name('proposals.index');
    Route::post('/projects/{project}/proposals', [ProposalController::class, 'store'])->name('proposals.store');
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/work', [ProfileController::class, 'updateWorkProfile'])->name('profile.work.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
