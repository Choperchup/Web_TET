<?php

use App\Http\Controllers\Admin\AdminCategoriesController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminPostController;

Route::middleware('auth', 'admin')->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});

Route::middleware('auth', 'admin')->prefix('admin')->group(function () {
    //Quản lý bài viết
    Route::get('/dashboard/posts', [AdminPostController::class, 'index'])->name('admin.posts.index');

    //Xoá và khôi phục
    Route::get('/dashboard/posts/trash', [AdminPostController::class, 'trash'])->name('admin.posts.trash');
    Route::put('/dashboard/posts/{id}/restore', [AdminPostController::class, 'restore'])->name('admin.posts.restore');
    Route::delete('/dashboard/posts/{id}/force-delete', [AdminPostController::class, 'forceDelete'])->name('admin.posts.forceDelete');
    Route::delete('/dashboard/posts/force-delete', [AdminPostController::class, 'forceDeleteAll'])->name('admin.posts.forceDeleteAll');

    // Thêm các định tuyến CRUD còn lại
    Route::get('/dashboard/posts/create', [AdminPostController::class, 'create'])->name('admin.posts.create');
    Route::post('/dashboard/posts/store', [AdminPostController::class, 'store'])->name('admin.posts.store');
    Route::get('/dashboard/posts/{posts}/edit', [AdminPostController::class, 'edit'])->name('admin.posts.edit');
    Route::put('/dashboard/posts/{posts}/update', [AdminPostController::class, 'update'])->name('admin.posts.update');
    Route::delete('/dashboard/posts/{posts}', [AdminPostController::class, 'destroy'])->name('admin.posts.destroy');
    Route::post('/dashboard/posts/{posts}/publish', [AdminPostController::class, 'publish'])->name('admin.posts.publish');
    Route::post('/dashboard/posts/{posts}/draft', [AdminPostController::class, 'draft'])->name('admin.posts.draft');
    Route::post('/dashboard/posts/upload', [AdminPostController::class, 'uploadImage'])->name('admin.posts.upload');
});

Route::middleware('auth', 'admin')->prefix('admin')->group(function () {
    //Quản lý phân loại
    Route::get('/dashboard/categories', [AdminCategoriesController::class, 'index'])->name('admin.categories.index');

    //Xóa và khôi phục
    Route::get('/dashboard/categories/trash', [AdminCategoriesController::class, 'trash'])->name('admin.categories.trash');
    Route::put('/dashboard/categories/{id}/restore', [AdminCategoriesController::class, 'restore'])->name('admin.categories.restore');
    Route::delete('/dashboard/categories/{id}/force-delete', [AdminCategoriesController::class, 'forceDelete'])->name('admin.categories.forceDelete');
    Route::delete('/dashboard/categories/force-delete', [AdminCategoriesController::class, 'forceDeleteAll'])->name('admin.categories.forceDeleteAll');
    // Thêm các định tuyến CRUD còn lại
    Route::get('/dashboard/categories/create', [AdminCategoriesController::class, 'create'])->name('admin.categories.create');
    Route::post('/dashboard/categories/store', [AdminCategoriesController::class, 'store'])->name('admin.categories.store');
    Route::get('/dashboard/categories/{categories}/edit', [AdminCategoriesController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/dashboard/categories/{categories}', [AdminCategoriesController::class, 'update'])->name('admin.categories.update');
    Route::delete('/dashboard/categories/{categories}', [AdminCategoriesController::class, 'destroy'])->name('admin.categories.destroy');
});
