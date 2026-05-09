<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TopController;
use App\Http\Controllers\SampleController;


Route::get('/', function () {
    return view('welcome');
});
Route::get('/top', [TopController::class, 'index']);
Route::get('/top/inertia', [TopController::class, 'inertia']);

Route::get('/sample/sample1', [SampleController::class, 'sample1']);
Route::get('/sample/sample2', [SampleController::class, 'sample2']);
Route::get('/sample/sample3', [SampleController::class, 'sample3']);
Route::get('/sample/sample4', [SampleController::class, 'sample4']);
Route::get('/sample/sample5', [SampleController::class, 'sample5']);
Route::get('/sample/sample6', [SampleController::class, 'sample6']);
Route::get('/sample/sample7', [SampleController::class, 'sample7']);
Route::get('/sample/sample8', [SampleController::class, 'sample8']);
