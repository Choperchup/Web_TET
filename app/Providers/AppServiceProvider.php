<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. CHỈ ĐỊNH LARAVEL SỬ DỤNG TEMPLATE PHÂN TRANG BOOTSTRAP 5
        Paginator::useBootstrapFive();
        // 2. CHIA SẺ DỮ LIỆU GIỎ HÀNG TOÀN ỨNG DỤNG QUA VIEW COMPOSER
        View::composer('layouts.navbar', function ($view) {
            // Key Session được định nghĩa trong CartController
            $sessionKey = 'shopping_cart';

            // Lấy giỏ hàng từ Session (nếu chưa có thì trả về Collection rỗng)
            $cartItems = Session::get($sessionKey, collect());

            // Tính tổng số lượng sản phẩm trong giỏ
            // .sum('quantity') tính tổng cột 'quantity' của các item
            $cartCount = $cartItems->sum('quantity');

            // Truyền biến $cartCount vào view Navbar
            $view->with('cartCount', $cartCount);
        });
    }
}
