<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Verification Routes
Route::post('/verification/send', [App\Http\Controllers\VerificationController::class, 'sendCode'])->name('verification.send');
Route::post('/verification/verify', [App\Http\Controllers\VerificationController::class, 'verifyCode'])->name('verification.verify');

Route::middleware(['auth', 'status'])->group(function () {
    // Alumni Dashboard
    Route::get('/dashboard', [App\Http\Controllers\AlumniDashboardController::class, 'index'])->name('dashboard');

    // Admin Routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'index'])->name('dashboard');
        Route::get('/pre-registration', [App\Http\Controllers\Admin\PreRegistrationController::class, 'index'])->name('pre-registration.index');
        Route::resource('alumni', App\Http\Controllers\Admin\AlumniController::class);
        Route::resource('courses', App\Http\Controllers\Admin\CourseController::class);
        Route::get('news_events/gallery-photos', [App\Http\Controllers\Admin\NewsEventController::class, 'getGalleryPhotos'])->name('news_events.gallery_photos');
        Route::get('news_events/{news_event}/broadcast', [App\Http\Controllers\Admin\NewsEventController::class, 'broadcastForm'])->name('news_events.broadcast.form');
        Route::post('news_events/{news_event}/broadcast', [App\Http\Controllers\Admin\NewsEventController::class, 'broadcast'])->name('news_events.broadcast');
        Route::resource('news_events', App\Http\Controllers\Admin\NewsEventController::class);
        Route::resource('gallery', App\Http\Controllers\Admin\GalleryController::class);
        Route::post('gallery/{gallery}/upload', [App\Http\Controllers\Admin\GalleryController::class, 'uploadPhotos'])->name('gallery.upload');
        Route::delete('gallery/photo/{photo}', [App\Http\Controllers\Admin\GalleryController::class, 'deletePhoto'])->name('gallery.photo.destroy');
        Route::patch('gallery/photo/{photo}', [App\Http\Controllers\Admin\GalleryController::class, 'updatePhotoCaption'])->name('gallery.photo.update');
        Route::resource('memos', App\Http\Controllers\Admin\ChedMemoController::class);
        Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/generate', [App\Http\Controllers\Admin\ReportController::class, 'generate'])->name('reports.generate');
        Route::resource('chat-management', App\Http\Controllers\Admin\ChatManagementController::class);
        Route::post('chat-management/{group}/message', [App\Http\Controllers\Admin\ChatManagementController::class, 'storeMessage'])->name('chat-management.store-message');
        Route::delete('chat-management/message/{message}', [App\Http\Controllers\Admin\ChatManagementController::class, 'deleteMessage'])->name('chat-management.delete-message');

        // System Evaluations
        Route::resource('evaluations', App\Http\Controllers\Admin\EvaluationController::class);

        // Admin Profile
        Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile/update', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::post('/profile/avatar', [App\Http\Controllers\Admin\ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Alumni Profile Routes
    // Alumni Profile Routes
    Route::get('/my-profile', [App\Http\Controllers\AlumniProfileController::class, 'edit'])->name('alumni.profile.edit');
    Route::put('/my-profile', [App\Http\Controllers\AlumniProfileController::class, 'update'])->name('alumni.profile.update');

    // Employment History Routes
    Route::post('/employment', [App\Http\Controllers\Alumni\EmploymentHistoryController::class, 'store'])->name('alumni.employment.store');
    Route::put('/employment/{id}', [App\Http\Controllers\Alumni\EmploymentHistoryController::class, 'update'])->name('alumni.employment.update');
    Route::delete('/employment/{id}', [App\Http\Controllers\Alumni\EmploymentHistoryController::class, 'destroy'])->name('alumni.employment.destroy');

    // Alumni News Routes
    Route::get('/news', [App\Http\Controllers\Alumni\NewsEventController::class, 'index'])->name('alumni.news.index');
    Route::get('/news/{news_event}', [App\Http\Controllers\Alumni\NewsEventController::class, 'show'])->name('alumni.news.show');

    // Alumni Gallery Routes
    Route::get('/gallery', [App\Http\Controllers\Alumni\GalleryController::class, 'index'])->name('alumni.gallery.index');
    Route::get('/gallery/{album}', [App\Http\Controllers\Alumni\GalleryController::class, 'show'])->name('alumni.gallery.show');

    // Alumni Memo Routes
    Route::get('/memos', [App\Http\Controllers\Alumni\ChedMemoController::class, 'index'])->name('alumni.memos.index');

    // Alumni Evaluation Routes
    Route::get('/evaluations', [App\Http\Controllers\Alumni\EvaluationController::class, 'index'])->name('alumni.evaluations.index');
    Route::get('/evaluations/{evaluation}', [App\Http\Controllers\Alumni\EvaluationController::class, 'show'])->name('alumni.evaluations.show');
    Route::post('/evaluations/{evaluation}', [App\Http\Controllers\Alumni\EvaluationController::class, 'store'])->name('alumni.evaluations.store');

    // Group Chat Routes
    Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/groups', [App\Http\Controllers\ChatController::class, 'getGroups'])->name('chat.groups');
    Route::get('/chat/groups/{group}/messages', [App\Http\Controllers\ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/groups/{group}/messages', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.messages.store');
});

require __DIR__ . '/auth.php';
