<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TopController;


Route::get('/', function () {
    return view('welcome');
});
Route::get('/top', [TopController::class, 'index']);
Route::get('/top/inertia', [TopController::class, 'inertia']);
