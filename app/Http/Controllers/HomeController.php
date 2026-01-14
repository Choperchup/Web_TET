<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin\Products;
use App\Models\Admin\Posts;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // 1. Dữ liệu cho SLIDER (Biến $hotProducts mà Slider đang yêu cầu)
        $hotProducts = Products::where('status', 'published')
            ->where(function ($query) {
                $query->where('is_featured', true)
                    ->orWhere('is_on_sale', true);
            })
            ->latest()
            ->take(5)
            ->get();

        // 2. Sản phẩm Nổi bật (Hiển thị danh sách bên dưới)
        $featuredProducts = Products::where('status', 'published')
            ->where('is_featured', true)
            ->latest()
            ->take(4)
            ->get();

        // 3. Sản phẩm Giảm giá
        $saleProducts = Products::where('status', 'published')
            ->where('is_on_sale', true)
            ->where('sale_price', '>', 0)
            ->latest()
            ->take(4)
            ->get();

        // 4. Bài viết mới nhất
        // Lưu ý: Đã bỏ lọc 'title' = 'published' vì logic đó sai. 
        // Nếu bạn có cột status trong bảng post, hãy đổi lại thành ->where('status', 'published')
        // 4. Bài viết mới nhất
        $latestPosts = Posts::where('status', 'published') // Thêm dòng này để chỉ hiện bài viết đã xuất bản
            ->latest()
            ->take(3)
            ->get();

        return view('home', compact('hotProducts', 'featuredProducts', 'saleProducts', 'latestPosts'));
    }
}
