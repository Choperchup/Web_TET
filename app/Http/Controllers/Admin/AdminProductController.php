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
        // Vấn đề 1: Đảm bảo trích xuất tất cả các trường cần thiết, bao gồm 'stock'
        $data = $request->only([
            'name',
            'category_id',
            'price',
            'sku',
            'content', // Tên cột DB, được gửi từ form
            'short_description', // Tên cột DB, đã thêm vào form
            'stock', // Thêm trường stock vào dữ liệu lưu
            'meta_title',
            'meta_description',
        ]);

        // Gán user_id
        $data['user_id'] = Auth::id();

        // Gán is_featured (boolean)
        $data['is_featured'] = $request->boolean('is_featured');

        // Vấn đề 2: Mapping checkbox 'is_active' trong form sang ENUM 'status' trong DB.
        // Giả định: nếu 'is_active' được check -> 'published', ngược lại -> 'draft'
        $data['status'] = $request->boolean('is_active') ? 'published' : 'draft';


        // Tạo slug
        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        $data['slug'] = $this->generateUniqueSlug($slug);


        // Bước 2: Xử lý Upload hình ảnh
        if ($request->hasFile('thumbnail')) {
            // Lưu ảnh vào thư mục 'public/products' và lấy đường dẫn.
            $data['thumbnail'] = $request->file('thumbnail')->store('products', 'public');
        } else {
            $data['thumbnail'] = null; // Hoặc gán giá trị mặc định nếu cần
        }

        // Bước 3: Lưu Product vào Database
        Products::create($data);

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
            'sku',
            'content',
            'short_description',
            'stock',
            'meta_title',
            'meta_description',
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['status'] = $request->boolean('is_active') ? 'published' : 'draft'; // Sửa logic status

        // Tạo slug duy nhất (chỉ khi slug thay đổi)
        $newSlug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        if ($product->slug !== $newSlug) {
            $data['slug'] = $this->generateUniqueSlug($newSlug, $product->id);
        } else {
            $data['slug'] = $product->slug;
        }

        // Bước 2: Xử lý Upload hình ảnh mới
        if ($request->hasFile('thumbnail')) {
            // Xóa ảnh cũ nếu có
            if ($product->thumbnail) {
                Storage::disk('public')->delete($product->thumbnail);
            }
            // Lưu ảnh mới
            $data['thumbnail'] = $request->file('thumbnail')->store('products', 'public');
        } else {
            $data['thumbnail'] = $product->thumbnail; // Giữ nguyên ảnh cũ nếu không có ảnh mới
        }

        // Bước 3: Cập nhật Product vào Database
        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
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
