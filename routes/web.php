<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');


Route::prefix('users')->controller(UserController::class)
    ->name('users.')->group(function () {
        Route::get('/', 'index')->name('index');

        Route::get('/create', 'create')->name('create');

        Route::post('/store', 'store')->name('store');
    });

Route::middleware('auth')->prefix('posts')->controller(PostController::class)->group(function () {
    Route::get('/', 'index')->name('posts.index');
    Route::get('/create', 'create')->name('posts.create');
    Route::post('/store', 'store')->name('posts.store');
    Route::get('/{id}', 'edit')->name('posts.edit');
    Route::put('/{id}', 'update')->name('posts.update');
    Route::get('/{id}/destroy', 'destroy')->name('posts.destroy');
});

Route::middleware('auth')->prefix('categories')->controller(CategoryController::class)->group(function () {
    // Define category routes here
    Route::get('/', 'index')->name('categories.index');
    Route::get('/create', 'create')->name('categories.create');
    Route::post('/store', 'store')->name('categories.store');
    Route::get('/destroy-all', 'destroyAll')->name('categories.destroyAll');
    Route::get('/{id}', 'edit')->name('categories.edit');
    Route::put('/{id}', 'update')->name('categories.update');
    Route::get('/{id}/destroy', 'destroy')->name('categories.destroy');
});

Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [AuthController::class, 'postRegister'])->name('postRegister');

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'postLogin'])->name('postLogin');

Route::post('logout', [AuthController::class, 'logout'])->name('logout');

