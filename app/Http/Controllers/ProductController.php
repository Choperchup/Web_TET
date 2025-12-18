<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin\Products; // Sử dụng Model Products
use App\Models\Admin\ProductCategory; // Sử dụng Model ProductCategory
use Illuminate\Database\Eloquent\Builder; // Import Builder cho truy vấn phức tạp
use Illuminate\Support\Facades\DB; // Import DB cho các truy vấn raw
class ProductController extends Controller
{
    /**
     * Hiển thị danh sách các sản phẩm đã được duyệt (published).
     */
    public function index(Request $request)
    {
        // 1. Lấy tất cả danh mục (cho phần lọc sidebar)
        $categories = ProductCategory::all();

        // 2. Bắt đầu query
        $query = Products::with('category')
            ->where('status', 'published')
            ->latest();

        // 2. Xử lý Tìm kiếm (search)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            // Tìm kiếm không phân biệt chữ hoa/thường trong tên và slug
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('slug', 'like', '%' . $searchTerm . '%');
            });
        }
        // 3. Xử lý Lọc theo Danh mục (category_id)
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 4. Xử lý Lọc theo Khoảng Giá (price_min, price_max)
        if ($request->filled('price_min') || $request->filled('price_max')) {
            $query->where(function (Builder $q) use ($request) {

                // Lọc Min Price:
                if ($request->filled('price_min')) {
                    $priceMin = (int) $request->price_min;

                    // Lọc theo giá hiển thị >= priceMin. 
                    // Giá hiển thị là: Sale Price (nếu hợp lệ) HOẶC Price.
                    $q->where(function (Builder $q2) use ($priceMin) {
                        // Trường hợp 1: Sale Price hợp lệ (sale_price > 0 AND sale_price < price) VÀ sale_price >= priceMin
                        $q2->where('sale_price', '>', 0)
                            ->where('sale_price', '<', DB::raw('price'))
                            ->where('sale_price', '>=', $priceMin);
                    })->orWhere(function (Builder $q2) use ($priceMin) {
                        // Trường hợp 2: Sale Price KHÔNG hợp lệ (hoặc null, hoặc 0, hoặc >= price) VÀ price >= priceMin
                        $q2->where(function (Builder $q3) {
                            $q3->whereNull('sale_price')
                                ->orWhere('sale_price', '=', 0)
                                ->orWhere('sale_price', '>=', DB::raw('price'));
                        })
                            ->where('price', '>=', $priceMin);
                    });
                }

                // Lọc Max Price:
                if ($request->filled('price_max')) {
                    $priceMax = (int) $request->price_max;

                    // Lọc theo giá hiển thị <= priceMax.
                    // Giá hiển thị là: Sale Price (nếu hợp lệ) HOẶC Price.
                    $q->where(function (Builder $q2) use ($priceMax) {
                        // Trường hợp 1: Sale Price hợp lệ VÀ sale_price <= priceMax
                        $q2->where('sale_price', '>', 0)
                            ->where('sale_price', '<', DB::raw('price'))
                            ->where('sale_price', '<=', $priceMax);
                    })->orWhere(function (Builder $q2) use ($priceMax) {
                        // Trường hợp 2: Sale Price KHÔNG hợp lệ VÀ price <= priceMax
                        $q2->where(function (Builder $q3) {
                            $q3->whereNull('sale_price')
                                ->orWhere('sale_price', '=', 0)
                                ->orWhere('sale_price', '>=', DB::raw('price'));
                        })
                            ->where('price', '<=', $priceMax);
                    });
                }
            });
        }

        // 5. Thực thi query và phân trang (giữ lại tham số lọc trên URL)
        $products = $query->paginate(12)->withQueryString();

        // 6. Truyền dữ liệu sang view
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
