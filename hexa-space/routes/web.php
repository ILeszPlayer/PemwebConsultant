<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CounselingServiceController;
use App\Http\Controllers\CounselingSessionController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DailyJournalController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::permanentRedirect('/layanan', '/services');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/services', [CounselingServiceController::class, 'index'])->name('services.index');

    Route::get('/sessions', [CounselingSessionController::class, 'index'])->name('sessions.index');
    Route::post('/sessions', [CounselingSessionController::class, 'store'])->name('sessions.store');
    Route::get('/sessions/{session}', [CounselingSessionController::class, 'show'])->name('sessions.show');
    Route::post('/sessions/{session}/chat', [ChatController::class, 'store'])->name('sessions.chat');
    Route::patch('/sessions/{session}/finish', [ChatController::class, 'finish'])->name('sessions.finish');

    Route::get('/doctor/sessions/{session}', [CounselingSessionController::class, 'doctorShow'])->name('doctor.sessions.show');
    Route::patch('/doctor/sessions/{session}/notes', [CounselingSessionController::class, 'updateNotes'])->name('doctor.sessions.notes');

    Route::post('/journal', [DailyJournalController::class, 'store'])->name('journal.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
