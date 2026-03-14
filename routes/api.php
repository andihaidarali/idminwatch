<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GeojsonController;

/*
|--------------------------------------------------------------------------
| GeoJSON API Routes (Public)
|--------------------------------------------------------------------------
| Endpoints return GeoJSON FeatureCollections for MapLibre consumption.
| No authentication required — public read-only access.
*/

Route::prefix('geojson')->group(function () {
    Route::get('/tambang', [GeojsonController::class, 'tambang']);
    Route::get('/hutan', [GeojsonController::class, 'hutan']);
    Route::get('/overlap', [GeojsonController::class, 'overlap']);
});

// Detail per-tambang (for map popup)
Route::get('/tambang/{id}/detail', [GeojsonController::class, 'detailTambang']);
Route::get('/tambang-list', [GeojsonController::class, 'tambangList']);

// Dashboard statistics
Route::get('/statistik', [GeojsonController::class, 'statistik']);
Route::get('/filter-options', [GeojsonController::class, 'filterOptions']);
