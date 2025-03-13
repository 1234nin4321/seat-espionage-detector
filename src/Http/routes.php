<?php

use Illuminate\Support\Facades\Route;
use YourVendor\Seat\EspionageDetector\Http\Controllers\ScreeningController;

Route::group([
    'prefix' => 'espionage-detector',
    'middleware' => ['web', 'auth', 'can:global.superuser'],
], function () {
    Route::get('/', [ScreeningController::class, 'index'])->name('espionage-detector.index');
    Route::post('/save-entities', [ScreeningController::class, 'saveEntities'])->name('espionage-detector.save-entities');
    Route::post('/run-check', [ScreeningController::class, 'runCheck'])->name('espionage-detector.run-check');
    Route::get('/results/{character}', [ScreeningController::class, 'results'])->name('espionage-detector.results');
});