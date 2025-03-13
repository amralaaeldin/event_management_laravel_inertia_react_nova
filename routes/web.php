<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'role:user'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/calendar', [EventController::class, 'index'])->name('calendar.index');
    Route::post('/events/{event}/attend', [EventController::class, 'attend'])->name('events.attend');
    Route::post('/events/{event}/attend', [EventController::class, 'attend'])
        ->middleware([
            'ensurePublished',
            'ensureNotPast',
            'ensureFullAndWaitlistCapacity',
            'ensureNotAttending',
            'ensureNotOverlapping',
        ])
        ->name('events.attend');

    Route::post('/events/{event}/unattend', [EventController::class, 'unattend'])->name('events.unattend');
});

require __DIR__ . '/auth.php';
