<?php

use App\Http\Controllers\TaskController; // Ditambahkan
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/tasks/', [TaskController::class, 'index'])->name('tasks.index');

// Ditambahkan
Route::get('/tasks/{id}/edit', [TaskController::class, 'edit'])->name('tasks.edit');