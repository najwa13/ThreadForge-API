<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlueprintController;
use App\Http\Controllers\Api\TextBrutController;
use App\Http\Controllers\Api\GeneratePostController;
use App\Http\Controllers\Api\PostController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('blueprints', BlueprintController::class);
    Route::apiResource('text-bruts', TextBrutController::class);
    Route::post('/text-bruts/{textBrut}/generate', [GeneratePostController::class, 'generate']);
    Route::apiResource('posts', PostController::class);
});