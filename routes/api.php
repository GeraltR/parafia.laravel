<?php

use App\Http\Controllers\ThemeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
            return $request->user();
        })
        ->middleware('auth:sanctum');

Route::get('/theme', [ThemeController::class, 'show']);
Route::put('/theme', [ThemeController::class, 'update']);