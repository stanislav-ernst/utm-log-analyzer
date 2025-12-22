<?php

use App\Http\Controllers\AnalysisController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/analysis-runs');

Route::get('/analysis-runs', [AnalysisController::class, 'index'])
    ->name('analysis-runs.index');

Route::get('/analysis-runs/{analysisRun}', [AnalysisController::class, 'show'])
    ->name('analysis-runs.show')
    ->whereNumber('analysisRun');
