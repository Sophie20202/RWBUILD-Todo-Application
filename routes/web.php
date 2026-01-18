<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\AdminTodoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ISTAfricaAuthController;

Route::get('/', fn() => view('welcome'));

Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // User Todos
    Route::resource('todos', TodoController::class);
    Route::patch('todos/{todo}/toggle', [TodoController::class, 'toggleComplete'])->name('todos.toggle');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes
    Route::middleware(['admin'])
        ->prefix('admin')
        ->as('admin.')
        ->group(function () {
            Route::resource('todos', AdminTodoController::class)->only(['index', 'destroy']);
        });
});

Route::get('/auth/ist-africa/redirect', [ISTAfricaAuthController::class, 'redirect'])
    ->name('auth.ist-africa');

Route::get('/auth/callback', [ISTAfricaAuthController::class, 'callback'])->name('iaa.callback');
Route::post('/auth/authenticate', [ISTAfricaAuthController::class, 'apiAuthenticate']);


require __DIR__.'/auth.php';
