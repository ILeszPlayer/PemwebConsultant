<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CounselingServiceController;
use App\Http\Controllers\CounselingSessionController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DailyJournalController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\SosController;
use App\Http\Controllers\MessageFeedbackController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::permanentRedirect('/layanan', '/services');

// Dashboard - accessible by all roles (redirects internally)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Pasien-only routes (user role)
Route::middleware(['auth', 'verified', 'pasien'])->group(function () {
    Route::get('/services', [CounselingServiceController::class, 'index'])->name('services.index');

    Route::get('/sessions', [CounselingSessionController::class, 'index'])->name('sessions.index');
    Route::post('/sessions', [CounselingSessionController::class, 'store'])->name('sessions.store');
    Route::get('/sessions/{session}', [CounselingSessionController::class, 'show'])->name('sessions.show');
    Route::post('/sessions/{session}/chat', [ChatController::class, 'store'])->name('sessions.chat');
    Route::patch('/sessions/{session}/finish', [ChatController::class, 'finish'])->name('sessions.finish');
    Route::patch('/sessions/{session}/title', [CounselingSessionController::class, 'updateTitle'])->name('sessions.updateTitle');
    Route::get('/sessions/{session}/export', [ChatController::class, 'export'])->name('sessions.export');

    Route::delete('/chat-messages/{message}', [ChatController::class, 'destroyMessage'])->name('chat-messages.destroy');
    Route::post('/chat-messages/{message}/feedback', [MessageFeedbackController::class, 'toggle'])->name('chat-messages.feedback');

    Route::post('/journal', [DailyJournalController::class, 'store'])->name('journal.store');
    Route::get('/journal', [DailyJournalController::class, 'index'])->name('journal.index');
    Route::delete('/journal/{journal}', [DailyJournalController::class, 'destroy'])->name('journal.destroy');

    Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');

    Route::get('/sos', [SosController::class, 'index'])->name('sos.index');
    Route::get('/sos/breathing', [SosController::class, 'breathing'])->name('sos.breathing');
    Route::get('/sos/grounding', [SosController::class, 'grounding'])->name('sos.grounding');
});

// Doctor-only routes
Route::middleware(['auth', 'verified', 'doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/sessions/{session}', [CounselingSessionController::class, 'doctorShow'])->name('sessions.show');
    Route::patch('/sessions/{session}/notes', [CounselingSessionController::class, 'updateNotes'])->name('sessions.notes');
    Route::get('/sessions/{session}/export', [ChatController::class, 'export'])->name('sessions.export');
});

// Profile - accessible by all authenticated
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/avatar/remove', [ProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/change-password', [AdminUserController::class, 'changePassword'])->name('users.changePassword');
    Route::patch('/users/{user}/ban', [AdminUserController::class, 'toggleBan'])->name('users.toggleBan');

    Route::get('/articles', [AdminArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/create', [AdminArticleController::class, 'create'])->name('articles.create');
    Route::post('/articles', [AdminArticleController::class, 'store'])->name('articles.store');
    Route::get('/articles/{article}/edit', [AdminArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/articles/{article}', [AdminArticleController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{article}', [AdminArticleController::class, 'destroy'])->name('articles.destroy');

    Route::get('/services', [AdminServiceController::class, 'index'])->name('services.index');
    Route::get('/services/create', [AdminServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [AdminServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{service}/edit', [AdminServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{service}', [AdminServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [AdminServiceController::class, 'destroy'])->name('services.destroy');
});

require __DIR__.'/auth.php';
