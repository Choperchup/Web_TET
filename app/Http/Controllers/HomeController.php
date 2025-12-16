<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin\Products;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Truy vấn Sản phẩm Nổi bật:
        $featuredProducts = Products::where('status', 'published') // Bắt buộc phải là sản phẩm đang hoạt động
            ->where('is_featured', true)    // Bắt buộc phải là sản phẩm nổi bật
            ->with('category')              // Tải thông tin danh mục
            ->latest()                      // Sắp xếp theo mới nhất
            ->take(8)                       // Giới hạn số lượng hiển thị (Ví dụ: 8 sản phẩm)
            ->get();

        // 2. Trả về View trang chủ
        return view('home', compact('featuredProducts'));
    }

    public function about()
    {
        return view('about');
    }
}
