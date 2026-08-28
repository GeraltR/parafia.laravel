<?php

use App\Http\Controllers\AssociationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactAddressController;
use App\Http\Controllers\ContentTopicController;
use App\Http\Controllers\EventItemController;
use App\Http\Controllers\FontController;
use App\Http\Controllers\FooterController;
use App\Http\Controllers\HeroController;
use App\Http\Controllers\InfoExtraController;
use App\Http\Controllers\MassAndPastorController;
use App\Http\Controllers\NavbarController;
use App\Http\Controllers\NewsItemController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ShortActionController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})
    ->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
    ->middleware('throttle:5,1');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])
    ->middleware('throttle:5,1');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/users', [UserController::class, 'index']);
    Route::put('/users/{user}/password', [UserController::class, 'updatePassword']);
    Route::post('/users', [UserController::class, 'store'])->middleware('can-write:management');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('can-write:management');
});

Route::get('/theme', [ThemeController::class, 'show']);
Route::put('/theme', [ThemeController::class, 'update'])
    ->middleware(['auth:sanctum', 'can-write:site']);

Route::get('/navbar', [NavbarController::class, 'show']);
Route::put('/navbar', [NavbarController::class, 'update'])
    ->middleware(['auth:sanctum', 'can-write:site']);
Route::get('/hero', [HeroController::class, 'show']);
Route::put('/hero', [HeroController::class, 'update'])
    ->middleware(['auth:sanctum', 'can-write:site']);
Route::post('/hero/background-image', [HeroController::class, 'uploadBackgroundImage'])
    ->middleware(['auth:sanctum', 'can-write:site']);
Route::get('/short-actions', [ShortActionController::class, 'show']);
Route::put('/short-actions', [ShortActionController::class, 'update'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::post('/short-actions/upload-icon', [ShortActionController::class, 'uploadIcon'])
    ->middleware(['auth:sanctum', 'can-write:content']);

Route::get('/mass-and-pastor', [MassAndPastorController::class, 'show']);
Route::put('/mass-and-pastor', [MassAndPastorController::class, 'update'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::post('/mass-and-pastor/upload-photo', [MassAndPastorController::class, 'uploadPhoto'])
    ->middleware(['auth:sanctum', 'can-write:content']);

Route::get('/associations', [AssociationController::class, 'show']);
Route::put('/associations', [AssociationController::class, 'update'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::post('/associations/upload-image', [AssociationController::class, 'uploadImage'])
    ->middleware(['auth:sanctum', 'can-write:content']);

Route::get('/events', [EventItemController::class, 'index']);
Route::get('/news', [NewsItemController::class, 'index']);
Route::get('/info-extra', [InfoExtraController::class, 'show']);
Route::get('/footer', [FooterController::class, 'show']);
Route::put('/footer', [FooterController::class, 'update'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::get('/social', [SocialController::class, 'index']);
Route::put('/social', [SocialController::class, 'update'])
    ->middleware(['auth:sanctum', 'can-write:site']);
Route::get('/fonts', [FontController::class, 'index']);
Route::get('/contact-addresses', [ContactAddressController::class, 'show']);
Route::put('/contact-addresses', [ContactAddressController::class, 'update'])
    ->middleware(['auth:sanctum', 'can-write:site']);

Route::get('/content-topics', [ContentTopicController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/content-topics/manage', [ContentTopicController::class, 'manage']);
});
Route::post('/content-topics', [ContentTopicController::class, 'store'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::post('/content-topics/upload-image', [ContentTopicController::class, 'uploadImage'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::put('/content-topics/{contentTopic}', [ContentTopicController::class, 'update'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::delete('/content-topics/{contentTopic}', [ContentTopicController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'can-write:content']);
