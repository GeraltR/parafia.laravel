<?php

use App\Http\Controllers\ContactAddressController;
use App\Http\Controllers\EventItemController;
use App\Http\Controllers\FooterController;
use App\Http\Controllers\HeroController;
use App\Http\Controllers\InfoExtraController;
use App\Http\Controllers\NavbarController;
use App\Http\Controllers\NewsItemController;
use App\Http\Controllers\ShortActionItemController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\ThemeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
            return $request->user();
        })
        ->middleware('auth:sanctum');

Route::get('/theme', [ThemeController::class, 'show']);
Route::put('/theme', [ThemeController::class, 'update']);

Route::get('/navbar', [NavbarController::class, 'show']);
Route::get('/hero', [HeroController::class, 'show']);
Route::get('/short-actions', [ShortActionItemController::class, 'index']);
Route::get('/events', [EventItemController::class, 'index']);
Route::get('/news', [NewsItemController::class, 'index']);
Route::get('/info-extra', [InfoExtraController::class, 'show']);
Route::get('/footer', [FooterController::class, 'show']);
Route::get('/social', [SocialController::class, 'index']);
Route::get('/contact-addresses', [ContactAddressController::class, 'show']);