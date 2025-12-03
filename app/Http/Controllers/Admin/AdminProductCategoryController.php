<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminStoreProductCategories;
use App\Models\Admin\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminProductCategoryController extends Controller
{
    /**
     * Hiển thị danh sách phân loại sản phẩm.
     */
    public function index(Request $request)
    {
        // Sử dụng with('parent') để load tên danh mục cha
        $query = ProductCategory::with('parent')->withCount('products');

        if ($request->filled('q')) {
            $query->where('name', 'like', "%{$request->q}%");
        }

        $categories = $query->latest()->paginate(10)->appends($request->query());

        return view('admin.product-categories.index', compact('categories'));
    }


    // Hiển thị danh sách các danh mục đã bị xóa mềm (Trash)
    public function trash(Request $request)
    {
        // Khởi tạo query chỉ lấy các mục đã bị xóa mềm
        $query = ProductCategory::onlyTrashed();

        // Xử lý tìm kiếm (nếu có)
        if ($request->filled('q')) {
            // Khi tìm kiếm trong thùng rác, cần sử dụng onlyTrashed() trước
            $query->where('name', 'like', "%{$request->q}%");
        }

        // Lấy danh mục, chỉ bao gồm các mục ĐÃ bị xóa mềm
        $categories = $query->latest()->paginate(10)->appends($request->query());

        // View hiển thị thùng rác
        return view('admin.product-categories.trash', compact('categories'));
    }

    /**
     * Hiển thị form tạo danh mục mới.
     */
    public function create()
    {
        // Lấy tất cả danh mục để dùng làm danh mục cha
        $parentCategories = ProductCategory::where('parent_id', null)->get(['id', 'name']);
        return view('admin.product-categories.create', compact('parentCategories'));
    }

    /**
     * Xử lý lưu danh mục mới.
     */
    public function store(AdminStoreProductCategories $request)
    {
        // Validate request (giả định AdminStoreProductCategories đã được cập nhật để chấp nhận parent_id)
        $data = $request->validated();

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);

        $data['slug'] = $this->generateUniqueSlug($slug);

        // Thêm parent_id (có thể là NULL nếu không chọn)
        $data['parent_id'] = $request->parent_id ?: null;

        ProductCategory::create($data);

        return redirect()->route('admin.product-categories.index')->with('success', 'Tạo danh mục sản phẩm thành công!');
    }

    /**
     * Hiển thị form chỉnh sửa danh mục.
     */
    public function edit(ProductCategory $productCategory)
    {
        // ĐÃ SỬA: Lấy tất cả danh mục GỐC (parent_id = NULL) trừ chính nó (để tránh tạo vòng lặp cha-con)
        $parentCategories = ProductCategory::where('parent_id')
            ->where('id', '!=', $productCategory->id)
            ->get(['id', 'name']);

        return view('admin.product-categories.edit', compact('productCategory', 'parentCategories'));
    }

    /**
     * Cập nhật danh mục.
     */
    public function update(Request $request, ProductCategory $productCategory)
    {
        // Cập nhật luật validation
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                Rule::unique('product_categories', 'slug')->ignore($productCategory->id),
                'max:255',
            ],
            'description' => 'nullable|string',
            'parent_id' => [ // Thêm luật cho parent_id
                'nullable',
                'exists:product_categories,id',
                Rule::notIn([$productCategory->id]), // Ngăn không cho danh mục tự làm cha của chính nó
            ],
            'icon_url' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Tên danh mục không được để trống.',
            'slug.unique' => 'Đường dẫn thân thiện (slug) này đã tồn tại.',
            'parent_id.not_in' => 'Danh mục cha không thể là chính nó.',
        ]);


        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);

        $data = $request->only(['name', 'description', 'icon_url']);

        // Gán parent_id (nếu giá trị là rỗng thì gán NULL)
        $data['parent_id'] = $request->parent_id ?: null;

        // Kiểm tra và tạo slug duy nhất chỉ khi slug thực sự thay đổi
        if ($productCategory->slug !== $slug) {
            $data['slug'] = $this->generateUniqueSlug($slug, $productCategory->id);
        }

        $productCategory->update($data);

        return redirect()->route('admin.product-categories.index')->with('success', 'Cập nhật danh mục sản phẩm thành công!');
    }

    /**
     * Xóa mềm danh mục sản phẩm.
     */
    public function destroy(ProductCategory $productCategory)
    {
        // Lưu ý: Nếu dùng onDelete('cascade') trong migration, việc xóa mềm danh mục cha 
        // sẽ không tự động xóa mềm danh mục con. Bạn phải tự xử lý nếu muốn.
        // Ở đây ta để mặc định: danh mục con vẫn còn, nhưng parent_id của nó vẫn trỏ tới ID cha đã xóa.
        $productCategory->delete();
        return redirect()->route('admin.product-categories.index')->with('success', 'Đã chuyển danh mục vào thùng rác.');
    }

    /**
     * Khôi phục danh mục đã xóa mềm.
     */
    public function restore($id)
    {
        ProductCategory::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('admin.product-categories.index')->with('success', 'Đã khôi phục danh mục thành công.');
    }

    /**
     * Xóa vĩnh viễn danh mục (Hard Delete).
     */
    public function forceDelete($id)
    {
        // Với onDelete('cascade') trong migration, xóa vĩnh viễn danh mục cha sẽ tự động xóa vĩnh viễn danh mục con.
        ProductCategory::withTrashed()->findOrFail($id)->forceDelete();
        return redirect()->route('admin.product-categories.trash')->with('success', 'Đã xóa vĩnh viễn danh mục.');
    }
    

    /**
     * Hàm nội bộ để tạo slug duy nhất.
     */
    private function generateUniqueSlug(string $originalSlug, ?int $ignoreId = null): string
    {
        $slug = $originalSlug;
        $i = 1;
        while (ProductCategory::where('slug', $slug)->where('id', '!=', $ignoreId)->exists()) {
            $slug = $originalSlug . '-' . $i++;
        }
        return $slug;
    }
}
