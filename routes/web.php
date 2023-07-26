<?php

use App\Http\Controllers\TaskController; // Ditambahkan
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view ('home');
})->name('home');

Route::prefix('tasks')
    ->name('tasks.')
    ->controller(TaskController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('{id}/edit', 'edit')->name('edit');
        Route::post('/', 'store')->name('store');
        Route::get('create', 'create')->name('create');
        Route::put('/{id}', 'update')->name('update');
        Route::get('/{id}/delete', 'delete')->name('delete');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::get('progress', 'progress')->name('progress');
        Route::patch('{id}/move', 'move')->name('move');
        // Route::patch('{id}/checklist', 'movechecklist')->name('checklist');
    });