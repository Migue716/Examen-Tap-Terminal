<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::get('/sections', [SectionController::class, 'index']);

    Route::middleware('section:productos')->group(function () {
        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/products/{id}', [ProductController::class, 'show']);
        Route::get('/products-export/{format}', [ExportController::class, 'products'])
            ->where('format', 'pdf|excel');
    });

    Route::middleware('section:productos,write')->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    });

    Route::middleware('section:usuarios')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::get('/users-export/{format}', [ExportController::class, 'users'])
            ->where('format', 'pdf|excel');
    });

    Route::middleware('section:usuarios,write')->group(function () {
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
    });

    Route::middleware('section:perfiles')->group(function () {
        Route::get('/profiles', [ProfileController::class, 'index']);
        Route::get('/profiles/{id}', [ProfileController::class, 'show']);
        Route::get('/profiles-export/{format}', [ExportController::class, 'profiles'])
            ->where('format', 'pdf|excel');
    });

    Route::middleware('section:perfiles,write')->group(function () {
        Route::post('/profiles', [ProfileController::class, 'store']);
        Route::put('/profiles/{id}', [ProfileController::class, 'update']);
        Route::delete('/profiles/{id}', [ProfileController::class, 'destroy']);
    });
});
