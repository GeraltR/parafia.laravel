<?php

use App\Http\Controllers\AssociationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactAddressController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\ContentImageController;
use App\Http\Controllers\EventItemController;
use App\Http\Controllers\FontController;
use App\Http\Controllers\FooterController;
use App\Http\Controllers\HeroController;
use App\Http\Controllers\InfoItemController;
use App\Http\Controllers\LiturgiaTopicController;
use App\Http\Controllers\MassAndPastorController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MassIntentionController;
use App\Http\Controllers\NavbarController;
use App\Http\Controllers\NewsItemController;
use App\Http\Controllers\PageViewController;
use App\Http\Controllers\ParafiaTopicController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\SakramentyTopicController;
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
    Route::put('/users/{user}', [UserController::class, 'update'])->middleware('can-write:management');
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
    ->middleware(['auth:sanctum', 'can-write:site']);
Route::post('/short-actions/upload-icon', [ShortActionController::class, 'uploadIcon'])
    ->middleware(['auth:sanctum', 'can-write:site']);

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
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/events/manage', [EventItemController::class, 'manage']);
});
Route::post('/events', [EventItemController::class, 'store'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::put('/events/{eventItem}', [EventItemController::class, 'update'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::delete('/events/{eventItem}', [EventItemController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'can-write:content']);

Route::get('/news', [NewsItemController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/news/manage', [NewsItemController::class, 'manage']);
});
Route::post('/news', [NewsItemController::class, 'store'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::post('/news/upload-image', [NewsItemController::class, 'uploadImage'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::put('/news/{newsItem}', [NewsItemController::class, 'update'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::delete('/news/{newsItem}', [NewsItemController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'can-write:content']);

Route::get('/mass-intentions', [MassIntentionController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/mass-intentions/manage', [MassIntentionController::class, 'manage']);
    Route::get('/mass-intentions/print', [MassIntentionController::class, 'printList']);
});
Route::post('/mass-intentions', [MassIntentionController::class, 'store'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::put('/mass-intentions/config', [MassIntentionController::class, 'updateConfig'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::put('/mass-intentions/{massIntention}', [MassIntentionController::class, 'update'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::delete('/mass-intentions/{massIntention}', [MassIntentionController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'can-write:content']);

Route::get('/informacje', [InfoItemController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/informacje/manage', [InfoItemController::class, 'manage']);
});
Route::post('/informacje', [InfoItemController::class, 'store'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::post('/informacje/upload-image', [InfoItemController::class, 'uploadImage'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::put('/informacje/{infoItem}', [InfoItemController::class, 'update'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::delete('/informacje/{infoItem}', [InfoItemController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'can-write:content']);

Route::get('/footer', [FooterController::class, 'show']);
Route::put('/footer', [FooterController::class, 'update'])
    ->middleware(['auth:sanctum', 'can-write:site']);
Route::get('/social', [SocialController::class, 'index']);
Route::put('/social', [SocialController::class, 'update'])
    ->middleware(['auth:sanctum', 'can-write:site']);
Route::get('/fonts', [FontController::class, 'index']);
Route::get('/contact-addresses', [ContactAddressController::class, 'show']);
Route::put('/contact-addresses', [ContactAddressController::class, 'update'])
    ->middleware(['auth:sanctum', 'can-write:site']);
Route::post('/contact-message', [ContactMessageController::class, 'store'])
    ->middleware('throttle:5,1');

Route::post('/content-images', [ContentImageController::class, 'upload'])
    ->middleware(['auth:sanctum', 'can-write:content']);

Route::get('/media', [MediaController::class, 'index'])
    ->middleware('auth:sanctum');
Route::delete('/media/{media}', [MediaController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'can-write:site']);

Route::post('/page-views', [PageViewController::class, 'store'])
    ->middleware('throttle:120,1');
Route::get('/page-views/summary', [PageViewController::class, 'summary'])
    ->middleware('auth:sanctum');

Route::get('/sakramenty-topics', [SakramentyTopicController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/sakramenty-topics/manage', [SakramentyTopicController::class, 'manage']);
});
Route::post('/sakramenty-topics', [SakramentyTopicController::class, 'store'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::post('/sakramenty-topics/upload-image', [SakramentyTopicController::class, 'uploadImage'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::put('/sakramenty-topics/{sakramentyTopic}', [SakramentyTopicController::class, 'update'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::delete('/sakramenty-topics/{sakramentyTopic}', [SakramentyTopicController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'can-write:content']);

Route::get('/parafia-topics', [ParafiaTopicController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/parafia-topics/manage', [ParafiaTopicController::class, 'manage']);
});
Route::post('/parafia-topics', [ParafiaTopicController::class, 'store'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::post('/parafia-topics/upload-image', [ParafiaTopicController::class, 'uploadImage'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::put('/parafia-topics/{parafiaTopic}', [ParafiaTopicController::class, 'update'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::delete('/parafia-topics/{parafiaTopic}', [ParafiaTopicController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'can-write:content']);

Route::get('/liturgia-topics', [LiturgiaTopicController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/liturgia-topics/manage', [LiturgiaTopicController::class, 'manage']);
});
Route::post('/liturgia-topics', [LiturgiaTopicController::class, 'store'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::post('/liturgia-topics/upload-image', [LiturgiaTopicController::class, 'uploadImage'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::put('/liturgia-topics/{liturgiaTopic}', [LiturgiaTopicController::class, 'update'])
    ->middleware(['auth:sanctum', 'can-write:content']);
Route::delete('/liturgia-topics/{liturgiaTopic}', [LiturgiaTopicController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'can-write:content']);
