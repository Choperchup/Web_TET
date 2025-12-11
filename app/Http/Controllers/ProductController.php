<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin\Products; // Sử dụng Model Products
use App\Models\Admin\ProductCategory; // Sử dụng Model ProductCategory

class ProductController extends Controller
{
    /**
     * Hiển thị danh sách các sản phẩm đã được duyệt (published).
     */
    public function index()
    {
        // 1. Lấy danh sách sản phẩm: CHỈ CÁC SẢN PHẨM ĐÃ XUẤT BẢN
        $products = Products::with('category') // Eager load category
            ->where('status', 'published') // Lọc sản phẩm đã xuất bản
            ->latest()
            ->paginate(12); // Phân trang 12 sản phẩm

        // Lấy danh mục nếu cần hiển thị sidebar/menu
        $categories = ProductCategory::all();

        // 2. Truyền dữ liệu sang view
        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Hiển thị chi tiết một sản phẩm.
     */
    public function show($slug)
    {
        // 1. Tìm sản phẩm theo slug và đảm bảo trạng thái là 'published'
        $product = Products::with('category', 'author') // Eager load category và author
            ->where('slug', $slug)
            ->where('status', 'published') // Đảm bảo sản phẩm đang hoạt động
            ->firstOrFail();

        // 2. Lấy 4 sản phẩm liên quan (cùng category, trừ sản phẩm hiện tại)
        $relatedProducts = Products::where('category_id', $product->category_id)
            ->where('status', 'published')
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        // 3. Truyền dữ liệu sang view
        return view('products.show', compact('product', 'relatedProducts'));
    }
}
