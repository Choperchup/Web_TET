<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderTrackingController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');

// --- CÁC ROUTE CHO NGƯỜI DÙNG (USERS) ---
Route::prefix('users')->controller(UserController::class)
    ->name('users.')->group(function () {
        Route::get('/', 'index')->name('index');

        Route::get('/create', 'create')->name('create');

        Route::post('/store', 'store')->name('store');
    });

// --- CÁC ROUTE CÔNG KHAI CHO BÀI VIẾT (POSTS) ---
Route::prefix('posts')->controller(PostController::class)->group(function () {
    Route::get('/', 'index')->name('posts.index');
    Route::get('{slug}', 'show')->name('posts.show');
});

// --- CÁC ROUTE CÔNG KHAI CHO SẢN PHẨM (PRODUCTS) ---
Route::prefix('products')->controller(ProductController::class)->group(function () {
    Route::get('/', 'index')->name('products.index');
    Route::get('{id}', 'show')->name('products.show');
});

// --- CÁC ROUTE CÔNG KHAI CHO GIỎ HÀNG (CART) ---
Route::prefix('cart')->controller(CartController::class)->group(function () {
    // Trang hiển thị giỏ hàng
    Route::get('/', 'index')->name('cart.index');

    // Thêm sản phẩm vào giỏ hàng (thường dùng POST/AJAX)
    Route::post('/add', 'add')->name('cart.add');

    // Cập nhật số lượng
    Route::put('/update', 'update')->name('cart.update');

    // Xóa sản phẩm khỏi giỏ
    Route::delete('/remove/{rowId}', 'remove')->name('cart.remove');

    // Xóa tất cả giỏ hàng
    Route::delete('/clear', 'clear')->name('cart.clear');
});

// --- CÁC ROUTE CHO THANH TOÁN (CHECKOUT) ---
Route::prefix('checkout')->controller(CheckoutController::class)->group(function () {
    // Trang hiển thị form thanh toán
    Route::get('/', 'index')->name('checkout.index'); // <<<< THÊM DÒNG NÀY

    // Xử lý POST khi người dùng xác nhận thanh toán
    Route::post('/', 'placeOrder')->name('checkout.placeOrder');
});

// --- CÁC ROUTE CHO THEO DÕI ĐƠN HÀNG (ORDER TRACKING) ---
Route::prefix('order-tracking')->controller(OrderTrackingController::class)->group(function () {
    Route::get('/', 'index')->name('order-tracking.index');
    Route::post('/', 'trackOrder')->name('order-tracking.track');
});

// --- CÁC ROUTE CHO NGƯỜI DÙNG ĐÃ ĐĂNG NHẬP (USER DASHBOARD) ---
Route::middleware('auth')->prefix('user/orders')->controller(UserController::class)->group(function () {
    // Trang hiển thị danh sách đơn hàng
    Route::get('/', 'ordersIndex')->name('users.orders.index');

    // Trang xem chi tiết đơn hàng
    Route::get('/{order}', 'ordersShow')->name('users.orders.show');
});

Route::middleware('auth')->prefix('categories')->controller(CategoryController::class)->group(function () {});

Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [AuthController::class, 'postRegister'])->name('postRegister');

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'postLogin'])->name('postLogin');

Route::post('logout', [AuthController::class, 'logout'])->name('logout');
