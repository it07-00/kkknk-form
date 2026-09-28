<?php

use App\Http\Controllers\GhgReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GhgReportController::class, 'index'])->name('form.index');
Route::post('/api/submissions', [GhgReportController::class, 'store'])->name('form.submit');
Route::get('/submissions/{submission:code}/excel', [GhgReportController::class, 'downloadExcel'])
    ->middleware(['signed', 'throttle:30,1'])
    ->name('submissions.excel');
