<?php

use App\Http\Controllers\Admin\GhgSubmissionDownloadController;
use App\Http\Controllers\GhgReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GhgReportController::class, 'index'])->name('form.index');
Route::post('/api/submissions', [GhgReportController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('form.submit');
Route::get('/submissions/{submission:code}/mitigation-report', [GhgReportController::class, 'downloadMitigationReport'])
    ->middleware(['signed', 'throttle:30,1'])
    ->name('submissions.mitigation-report');

Route::prefix('admin/ghg-submissions')->name('admin.ghg-submissions.')->group(function (): void {
    Route::middleware(['auth', 'admin'])->group(function (): void {
        Route::get('/{submission:code}/excel', [GhgSubmissionDownloadController::class, 'excel'])
            ->middleware('throttle:30,1')
            ->name('excel.download');
        Route::get('/{submission:code}/report', [GhgSubmissionDownloadController::class, 'report'])
            ->middleware('throttle:30,1')
            ->name('report.download');
    });
});
