<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminStoreCategories;
use App\Models\Admin\Categories;
use Illuminate\Http\Request;

class AdminCategoriesController extends Controller
{
    /**
     * Hiển thị danh sách tất cả các danh mục.
     */
    public function index()
    {
        $categories = Categories::latest()->get();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Hiển thị form tạo danh mục mới.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Lưu trữ danh mục mới vào cơ sở dữ liệu.
     */
    public function store(AdminStoreCategories $request)
    {
        // Slug được tự động tạo thông qua setNameAttribute trong Category Model
        $categories = Categories::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Danh mục đã được tạo thành công!');
    }

    /**
     * Hiển thị form chỉnh sửa danh mục.
     */
    public function edit(Categories $categories)
    {
        // Controller chỉ truyền biến $categories
        return view('admin.categories.edit', compact('categories'));
    }

    /**
     * Cập nhật danh mục trong cơ sở dữ liệu.
     */
    public function update(AdminStoreCategories $request, Categories $categories)
    {
        // Cập nhật thuộc tính. setNameAttribute sẽ tự động cập nhật slug
        $categories->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Danh mục đã được cập nhật thành công!');
    }

    /**
     * Hiển thị danh sách các danh mục đã xóa mềm (thùng rác).
     */
    public function trash()
    {
        $categories = Categories::onlyTrashed()->latest('deleted_at')->get();

        return view('admin.categories.trash', compact('categories'));
    }

    /**
     * Khôi phục (Restore) danh mục đã xóa mềm.
     */
    public function restore($id)
    {
        $categories = Categories::withTrashed()->findOrFail($id);

        $categories->restore();

        return redirect()->route('admin.categories.trash')->with('success', 'Danh mục đã được khôi phục thành công!');
    }
    /**
     * Xóa vĩnh viễn (Force Delete) danh mục đã xóa mềm.
     */
    public function forceDelete($id)
    {
        $categories = Categories::withTrashed()->findOrFail($id);
        $categoriesName = $categories->name;
        $categories->forceDelete();
        return redirect()->route('admin.categories.trash')->with('success', 'Danh mục đã được xóa vĩnh viễn!');
    }
    /**
     * Xóa vĩnh viễn tất cả các danh mục đã xóa mềm.
     */
    public function forceDeleteAll()
    {
        $trashedCategories = Categories::onlyTrashed()->get();
        foreach ($trashedCategories as $category) {
            $category->forceDelete();
        }
        return redirect()->route('admin.categories.trash')->with('success', 'Đã xóa vĩnh viễn tất cả các danh mục trong thùng rác!');
    }

    /**
     * Xóa mềm (Soft Delete) danh mục.
     */
    public function destroy(Categories $categories)
    {
        $categories->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Danh mục đã được xóa thành công!');
    }
}
