<?php

use App\Http\Controllers\TaskController; // Ditambahkan
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home'); // name ditambahkan

Route::get('/tasks/', [TaskController::class, 'index'])->name('tasks.index'); // name ditambahka