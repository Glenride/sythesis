<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DossierController;
use App\Http\Controllers\Api\MarketRadarController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SourceController;

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('dossiers', DossierController::class)->except(['create', 'edit']);

    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports/{report}', [ReportController::class, 'show']);
    Route::patch('/reports/{report}', [ReportController::class, 'update']);
    Route::patch('/reports/{report}/assign', [ReportController::class, 'assign']);
    Route::post('/reports/{report}/refine', [ReportController::class, 'refine']);
    Route::get('/reports/{report}/history', [ReportController::class, 'history']);

    Route::get('/sources/{source}/preview', [SourceController::class, 'preview']);

    Route::post('/synthesis/market-radar', [MarketRadarController::class, 'store']);
});
