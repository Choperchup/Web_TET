<?php

use App\Http\Controllers\Admin\AdminCategoriesController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminPostController;
use App\Http\Controllers\Admin\AdminProductCategoryController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminOrderController;

Route::middleware('auth', 'admin')->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
});

//NGƯỜI DÙNG QUẢN TRỊ
Route::middleware('auth', 'admin')->prefix('admin')->group(function () {
    Route::get('/dashboard/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/dashboard/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/dashboard/users/store', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/dashboard/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/dashboard/users/{user}/update', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/dashboard/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    Route::delete('/dashboard/users/force-delete', [UserController::class, 'forceDeleteAll'])->name('admin.users.forceDeleteAll');
    Route::delete('/dashboard/users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('admin.users.forceDelete');
    Route::put('/dashboard/users/{id}/restore', [UserController::class, 'restore'])->name('admin.users.restore');
    Route::get('/dashboard/users/trash', [UserController::class, 'trash'])->name('admin.users.trash');
});

// Quản lý bài viết
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
// Quản lý phân loại bai viết
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


// phân loại sản phẩm
Route::middleware('auth', 'admin')->prefix('admin')->group(function () {
    // Quản lý phân loại sản phẩm (INDEX)
    Route::get('/dashboard/product-categories', [AdminProductCategoryController::class, 'index'])->name('admin.product-categories.index');

    // Thùng rác (TRASH)
    Route::get('/dashboard/product-categories/trash', [AdminProductCategoryController::class, 'trash'])->name('admin.product-categories.trash'); // Thường dùng index và truyền tham số

    // Thêm (CREATE & STORE)
    Route::get('/dashboard/product-categories/create', [AdminProductCategoryController::class, 'create'])->name('admin.product-categories.create');
    Route::post('/dashboard/product-categories/store', [AdminProductCategoryController::class, 'store'])->name('admin.product-categories.store');

    // CRUD chi tiết (SỬ DỤNG {product_category} SỐ ÍT)
    Route::get('/dashboard/product-categories/{product_category}/edit', [AdminProductCategoryController::class, 'edit'])->name('admin.product-categories.edit');
    Route::put('/dashboard/product-categories/{product_category}', [AdminProductCategoryController::class, 'update'])->name('admin.product-categories.update');
    Route::delete('/dashboard/product-categories/{product_category}', [AdminProductCategoryController::class, 'destroy'])->name('admin.product-categories.destroy');

    // Xóa và khôi phục (Sử dụng {id} là cách truyền thống cho Soft Delete)
    Route::put('/dashboard/product-categories/{id}/restore', [AdminProductCategoryController::class, 'restore'])->name('admin.product-categories.restore');
    Route::delete('/dashboard/product-categories/{id}/force-delete', [AdminProductCategoryController::class, 'forceDelete'])->name('admin.product-categories.forceDelete');

    // Nếu bạn muốn xóa toàn bộ, hãy dùng một route không có tham số
    Route::delete('/dashboard/product-categories/force-delete-all', [AdminProductCategoryController::class, 'forceDeleteAll'])->name('admin.product-categories.forceDeleteAll');
});

// Quản lý sản phẩm

Route::middleware('auth', 'admin')->prefix('admin')->group(function () {
    // Quản lý sản phẩm sẽ được thêm ở đây
    // Ví dụ:
    Route::get('/dashboard/products', [AdminProductController::class, 'index'])->name('admin.products.index');

    // Thêm các định tuyến CRUD khác cho sản phẩm khi cần thiết
    Route::get('/dashboard/products/create', [AdminProductController::class, 'create'])->name('admin.products.create');
    Route::post('/dashboard/products/store', [AdminProductController::class, 'store'])->name('admin.products.store');
    Route::get('/dashboard/products/{product}/edit', [AdminProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/dashboard/products/{product}/update', [AdminProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/dashboard/products/{product}', [AdminProductController::class, 'destroy'])->name('admin.products.destroy');

    // Thùng rác sản phẩm
    Route::get('/dashboard/products/trash', [AdminProductController::class, 'trash'])->name('admin.products.trash');
    Route::put('/dashboard/products/{id}/restore', [AdminProductController::class, 'restore'])->name('admin.products.restore');
    Route::delete('/dashboard/products/{id}/force-delete', [AdminProductController::class, 'forceDelete'])->name('admin.products.forceDelete');

    // toggle status
    Route::post('/dashboard/products/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('admin.products.toggle_status');
});

Route::middleware('auth', 'admin')->prefix('admin')->group(function () {
    Route::get('/dashboard/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/dashboard/orders/{order}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::post('/dashboard/orders/{order}/confirm', [AdminOrderController::class, 'confirm'])->name('admin.orders.confirm');
    Route::post('/dashboard/orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('admin.orders.cancel');
    Route::put('/dashboard/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
});
