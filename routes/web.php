<?php

use App\Http\Controllers\Admin\AdminAuthenticatedSessionController;
use App\Http\Controllers\Admin\GhgSubmissionController as AdminGhgSubmissionController;
use App\Http\Controllers\GhgReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GhgReportController::class, 'index'])->name('form.index');
Route::post('/api/submissions', [GhgReportController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('form.submit');
Route::get('/submissions/{submission:code}/excel', [GhgReportController::class, 'downloadExcel'])
    ->middleware(['signed', 'throttle:30,1'])
    ->name('submissions.excel');
Route::get('/submissions/{submission:code}/mitigation-report', [GhgReportController::class, 'downloadMitigationReport'])
    ->middleware(['signed', 'throttle:30,1'])
    ->name('submissions.mitigation-report');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [AdminAuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('/login', [AdminAuthenticatedSessionController::class, 'store'])
            ->middleware('throttle:10,1')
            ->name('login.store');
    });

    Route::middleware(['auth', 'admin'])->group(function (): void {
        Route::redirect('/', '/admin/submissions')->name('dashboard');
        Route::post('/logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('logout');
        Route::get('/submissions', [AdminGhgSubmissionController::class, 'index'])->name('submissions.index');
        Route::get('/submissions/{submission:code}', [AdminGhgSubmissionController::class, 'show'])->name('submissions.show');
        Route::patch('/submissions/{submission:code}/status', [AdminGhgSubmissionController::class, 'updateStatus'])
            ->name('submissions.status.update');
        Route::get('/submissions/{submission:code}/excel', [AdminGhgSubmissionController::class, 'downloadExcel'])
            ->middleware('throttle:30,1')
            ->name('submissions.excel.download');
        Route::get('/submissions/{submission:code}/report', [AdminGhgSubmissionController::class, 'downloadReport'])
            ->middleware('throttle:30,1')
            ->name('submissions.report.download');
    });
});
