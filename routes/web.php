<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return redirect(Auth::user()->dashboardRoute());
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', 'role:client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user()->load('profile');

        return view('client.dashboard', [
            'user' => $user,
            'projectCount' => $user->projects()->count(),
        ]);
    })->name('dashboard');

    Route::resource('projects', ProjectController::class)->except(['show']);
});

Route::middleware(['auth', 'verified', 'role:freelancer'])->prefix('freelancer')->group(function () {
    Route::get('/dashboard', function () {
        return view('freelancer.dashboard', [
            'user' => Auth::user()->load('profile'),
        ]);
    })->name('freelancer.dashboard');
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
