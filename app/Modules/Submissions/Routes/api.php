<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Submissions\Controllers\SubmissionController;

Route::middleware('auth:api')->prefix('submissions')->group(function () {
    Route::post('/', [SubmissionController::class, 'store']);
});
