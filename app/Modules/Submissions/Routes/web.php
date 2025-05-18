<?php

use App\Modules\Submissions\Controllers\SubmissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('submission')->name('submission.')->group(function () {
    Route::get('/', [SubmissionController::class, 'create'])->name('create');
    Route::post('/', [SubmissionController::class, 'store'])->name('store');
    Route::get('/status/{id}', [SubmissionController::class, 'getSubmissionStatus'])->name('status');
});