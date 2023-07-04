<?php

use App\Http\Controllers\TaskController; // Ditambahkan
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/tasks/', [TaskController::class, 'index']); // Ditambahkan