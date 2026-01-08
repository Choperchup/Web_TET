<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Products;
use Illuminate\Http\Request;
use App\Models\Admin\ProductCategory;
use App\Http\Requests\Admin\AdminStoreProductRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminProductController extends Controller
{

    /**
     * Hiển thị danh sách sản phẩm.
     */
    public function index(Request $request)
    {
        // Eager load category để tránh N+1 Query
        $query = Products::with('category');

        if ($request->filled('q')) {
            $query->where('name', 'like', "%{$request->q}%");
        }

        // Lọc theo trạng thái nếu có
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->latest()->paginate(10)->appends($request->query());

        return view('admin.products.index', compact('products'));
    }

    /**
     * Hiển thị danh sách các sản phẩm đã bị xóa mềm (Trash).
     */
    public function trash(Request $request)
    {
        $query = Products::onlyTrashed()->with('category');

        if ($request->filled('q')) {
            $query->where('name', 'like', "%{$request->q}%");
        }

        $products = $query->latest()->paginate(10)->appends($request->query());

        return view('admin.products.trash', compact('products'));
    }

    /**
     * Hiển thị form tạo sản phẩm mới.
     */
    public function create()
    {
        // Lấy tất cả danh mục đang hoạt động
        $categories = ProductCategory::all(['id', 'name']);

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Xử lý lưu sản phẩm mới (Bao gồm upload hình ảnh).
     */
    public function store(AdminStoreProductRequest $request)
    {
        // Thêm 'sizes' vào danh sách lấy dữ liệu
        $data = $request->only([
            'name',
            'category_id',
            'price',
            'sale_price',
            'sku',
            'content',
            'short_description',
            'stock',
            'meta_title',
            'meta_description',
            'sizes' // THÊM DÒNG NÀY
        ]);

        $data['user_id'] = Auth::id();
        $data['is_featured'] = $request->boolean('is_featured');
        $data['status'] = $request->boolean('is_active') ? 'published' : 'draft';

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        $data['slug'] = $this->generateUniqueSlug($slug);

        // 1. Lưu thumbnail chính
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('products/thumbnails', 'public');
        }

        // 2. Lưu Sản phẩm
        $product = Products::create($data);

        // 3. Xử lý Album ảnh (Gallery)
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $path = $file->store('products/gallery', 'public');
                $product->images()->create(['image_path' => $path]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Tạo sản phẩm thành công!');
    }

    /**
     * Hiển thị form chỉnh sửa sản phẩm.
     */
    public function edit(Products $product)
    {
        $categories = ProductCategory::all(['id', 'name']);

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Cập nhật sản phẩm (Bao gồm xử lý hình ảnh).
     */
    public function update(AdminStoreProductRequest $request, Products $product)
    {

        $data = $request->only([
            'name',
            'category_id',
            'price',
            'sale_price',
            'sku',
            'content',
            'short_description',
            'stock',
            'meta_title',
            'meta_description',
            'sizes' // THÊM DÒNG NÀY
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['status'] = $request->boolean('is_active') ? 'published' : 'draft';

        // Xử lý Thumbnail chính
        if ($request->hasFile('thumbnail')) {
            if ($product->thumbnail) Storage::disk('public')->delete($product->thumbnail);
            $data['thumbnail'] = $request->file('thumbnail')->store('products/thumbnails', 'public');
        }

        $product->update($data);

        // Xử lý upload thêm ảnh vào Album
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $path = $file->store('products/gallery', 'public');
                $product->images()->create(['image_path' => $path]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật thành công!');
    }

    // Thêm hàm xóa ảnh lẻ trong Album (Dùng cho trang Edit)
    public function deleteGalleryImage($id)
    {
        $image = \App\Models\Admin\ProductImage::findOrFail($id);
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
        return back()->with('success', 'Đã xóa ảnh khỏi album');
    }

    /**
     * Xóa mềm sản phẩm (Soft Delete).
     */
    public function destroy(Products $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Đã chuyển sản phẩm vào thùng rác.');
    }

    /**
     * Khôi phục sản phẩm đã xóa mềm.
     */
    public function restore($id)
    {
        Products::withTrashed()->findOrFail($id)->restore();
        // Chuyển hướng về trang thùng rác sau khi khôi phục
        return redirect()->route('admin.products.trash')->with('success', 'Đã khôi phục sản phẩm thành công.');
    }

    /**
     * Xóa vĩnh viễn sản phẩm (Hard Delete) và xóa file ảnh liên quan.
     */
    public function forceDelete($id)
    {
        $product = Products::withTrashed()->findOrFail($id);

        // Xóa file ảnh khỏi Storage
        if ($product->thumbnail) { // Đã sửa tên cột từ thumbnail_url sang thumbnail
            Storage::disk('public')->delete($product->thumbnail);
        }

        $product->forceDelete();
        // Chuyển hướng về trang thùng rác sau khi xóa vĩnh viễn
        return redirect()->route('admin.products.trash')->with('success', 'Đã xóa vĩnh viễn sản phẩm.');
    }

    /**
     * Toggle the product's status between 'published' (Hoạt động) and 'draft' (Tạm ẩn) via AJAX.
     * Status enum: ['draft', 'published', 'out_of_stock']
     */
    public function toggleStatus(Products $product)
    {
        // Xác định trạng thái mới: published <-> draft
        $newStatus = $product->status === 'published' ? 'draft' : 'published';

        // Cập nhật trạng thái
        $product->status = $newStatus;
        $product->save();

        // Trả về JSON response
        return response()->json([
            'success' => true,
            'new_status' => $product->status,
            'status_label' => $product->status === 'published' ? 'Hoạt động' : 'Tạm ẩn',
            'status_class' => $product->status === 'published' ? 'bg-success' : 'bg-secondary',
            'message' => 'Cập nhật trạng thái sản phẩm thành công.',
            'stock' => $product->stock, // Trả về tồn kho để hiển thị cảnh báo
        ]);
    }

    /**
     * Chuyển đổi trạng thái hiển thị giá khuyến mãi của sản phẩm (is_on_sale).
     */
    public function toggleSale(Products $product)
    {
        // Kiểm tra xem sản phẩm có sale_price hay không
        if ($product->sale_price <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm này không có giá khuyến mãi để bật/tắt.',
            ], 400);
        }

        // Đảo ngược trạng thái is_on_sale
        $product->is_on_sale = ! $product->is_on_sale;
        $product->save();

        $isOnSale = $product->is_on_sale;

        return response()->json([
            'success' => true,
            'new_status' => $isOnSale,
            'status_label' => $isOnSale ? 'Đang Sale' : 'Tắt Sale',
            'status_class' => $isOnSale ? 'bg-warning text-dark' : 'bg-secondary',
            'message' => 'Trạng thái Sale đã được cập nhật.',
            'sale_price_formatted' => number_format($product->sale_price) . ' VNĐ',
        ]);
    }

    /**
     * Hàm nội bộ để tạo slug duy nhất.
     */
    private function generateUniqueSlug(string $originalSlug, ?int $ignoreId = null): string
    {
        $slug = $originalSlug;
        $i = 1;
        while (Products::where('slug', $slug)->where('id', '!=', $ignoreId)->exists()) {
            $slug = $originalSlug . '-' . $i++;
        }
        return $slug;
    }
}
