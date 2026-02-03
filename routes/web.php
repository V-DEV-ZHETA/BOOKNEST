<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookController;

Route::get('/', function () {
    return view('welcome');
});

// API Routes for Landing Page
Route::prefix('api')->group(function () {
    Route::get('/books/search', [BookController::class, 'search']);
    Route::get('/books/category/{category}', [BookController::class, 'byCategory']);
    Route::get('/books/popular', [BookController::class, 'popular']);
    Route::get('/books/recent', [BookController::class, 'recent']);
    Route::get('/stats', [BookController::class, 'stats']);
});

