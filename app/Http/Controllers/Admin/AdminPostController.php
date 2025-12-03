<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminStorePost;
use App\Http\Requests\Admin\AdminUploadPost;
use App\Models\Admin\Categories;
use App\Models\Admin\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminPostController extends Controller
{
    /**
     * Hiển thị danh sách các bài viết (áp dụng bộ lọc).
     */
    public function index(Request $request)
    {
        $query = Posts::with('category', 'author');

        // Lọc theo trạng thái 'trashed' sẽ được xử lý bằng view `trash.blade.php` riêng
        // Mặc định chỉ lấy các bài viết chưa bị xóa mềm
        $query->whereNull('deleted_at');

        // search
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($r) use ($q) {
                $r->where('title', 'like', "%{$q}%")
                    ->orWhere('content', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%");
            });
        }
        // filter: category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // filter: status (draft, published, archived)
        if ($request->filled('status')) {
            // Loại bỏ 'trashed' vì nó có view riêng, chỉ lọc các trạng thái hoạt động
            if ($request->status !== 'trashed') {
                $query->where('status', $request->status);
            }
        }
        // filter: author (optional)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $posts = $query->latest('created_at')->paginate(12)->appends($request->query());
        $categories = Categories::orderBy('name')->get();

        return view('admin.posts.index', compact('posts', 'categories'));
    }

    /**
     * Hiển thị danh sách các bài viết đã xóa mềm (Trash View).
     */

    public function trash(Request $request)
    {
        // Chỉ lấy các bài viết đã xóa mềm
        $query = Posts::onlyTrashed()->with('category', 'author');

        // Thêm khả năng tìm kiếm trong thùng rác nếu cần
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($r) use ($q) {
                $r->where('title', 'like', "%{$q}%")
                    ->orWhere('content', 'like', "%{$q}%");
            });
        }

        $posts = $query->latest('deleted_at')->paginate(12)->appends($request->query());
        $categories = Categories::orderBy('name')->get(); // Cần thiết cho bộ lọc nếu bạn muốn thêm

        return view('admin.posts.trash', compact('posts', 'categories'));
    }

    public function create()
    {
        $categories = Categories::all();
        return view('admin.posts.create', compact('categories'));
    }

    /**
     * Summary of store
     * @param AdminStorePost $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AdminStorePost $request)
    {
        // handle thumbnail
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('posts', 'public');
        }

        $slug = Str::slug($request->title);
        // ensure unique slug
        $original = $slug;
        $i = 1;
        while (Posts::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }

        $publishedAt = null;
        if ($request->status === 'published') {
            $publishedAt = Carbon::now();
        } elseif ($request->filled('published_at')) {
            $publishedAt = Carbon::parse($request->published_at);
        }

        $posts = Posts::create([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'thumbnail' => $thumbnailPath,
            'category_id' => $request->category_id,
            'user_id' => auth()->id(),
            'status' => $request->status ?? 'draft',
            'published_at' => $publishedAt,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'featured' => $request->filled('featured') ? true : false
        ]);

        return redirect()->route('admin.posts.index')->with('success', 'Tạo bài viết thành công');
    }

    /**
     * Summary of edit
     * @param Posts $posts
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Posts $posts)
    {
        $categories = Categories::all();
        return view('admin.posts.edit', compact('posts', 'categories'));
    }

    /**
     * Summary of update
     * @param Request $request
     * @param Posts $posts
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(AdminStorePost $request, Posts $posts)
    {
        // handle thumbnail
        if ($request->hasFile('thumbnail')) {
            // delete old
            if ($posts->thumbnail && Storage::disk('public')->exists($posts->thumbnail)) {
                Storage::disk('public')->delete($posts->thumbnail);
            }
            $posts->thumbnail = $request->file('thumbnail')->store('posts', 'public');
        }

        // update slug if title changed
        if ($posts->title !== $request->title) {
            $slug = Str::slug($request->title);
            $original = $slug;
            $i = 1;
            while (Posts::where('slug', $slug)->where('id', '!=', $posts->id)->exists()) {
                $slug = $original . '-' . $i++;
            }
            $posts->slug = $slug;
        }

        $posts->title = $request->title;
        $posts->content = $request->content;
        $posts->category_id = $request->category_id;
        $posts->status = $request->status ?? $posts->status;
        $posts->meta_title = $request->meta_title;
        $posts->meta_description = $request->meta_description;
        $posts->featured = $request->filled('featured');

        if ($request->status === 'published' && !$posts->published_at) {
            $posts->published_at = Carbon::now();
        } elseif ($request->status !== 'published') {
            //Không xóa published_at khi chuyển từ archived sang draft
            if ($posts->status === 'published' && $request->status === 'draft') {
                $posts->published_at = null;
            }
        } elseif ($request->filled('published_at')) {
            $posts->published_at = Carbon::parse($request->published_at);
        }

        $posts->save();

        return redirect()->route('admin.posts.index')->with('success', 'Cập nhật bài viết thành công');
    }

    // soft delete
    public function destroy(Posts $posts)
    {
        $posts->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Đã chuyển bài viết "' . $posts->title . '" vào thùng rác');
    }

    // restore soft deleted
    public function restore($id)
    {
        $posts = Posts::withTrashed()->findOrFail($id);
        $posts->restore();
        // Chuyển hướng người dùng về trang thùng rác
        return redirect()->route('admin.posts.trash')->with('success', 'Đã khôi phục bài viết "' . $posts->title . '"thành công');
    }

    // permanently delete
    public function forceDelete($id)
    {
        $posts = Posts::withTrashed()->findOrFail($id);
        $postTitle = $posts->title; // Lưu tiêu đề trước khi xóa
        if ($posts->thumbnail && Storage::disk('public')->exists($posts->thumbnail)) {
            Storage::disk('public')->delete($posts->thumbnail);
        }
        $posts->forceDelete();
        // Chuyển hướng người dùng về trang thùng rác
        return redirect()->route('admin.posts.trash')->with('success', 'Đã xóa vĩnh viễn bài viết "' . $postTitle . '"');
    }

    // publish action (via route)
    public function publish($id)
    {
        $posts = Posts::findOrFail($id);
        $posts->status = 'published';
        if (!$posts->published_at) {
            $posts->published_at = Carbon::now();
        }
        $posts->save();
        return back()->with('success', 'Bài đã được xuất bản');
    }

    // set draft
    public function draft($id)
    {
        $posts = Posts::findOrFail($id);
        $posts->status = 'draft';
        $posts->published_at = null;
        $posts->save();
        return back()->with('success', 'Bài đã được lưu nháp');
    }


    /**
     * Summary of uploadImage
     * @param AdminUploadPost $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(AdminUploadPost $request)
    {
        $path = $request->file('upload')->store('posts/content', 'public');

        //CKEditor expects JSON with "url"
        return response()->json([
            'uploaded' => 1,
            'fileName' => basename($path),
            'url' => Storage::url($path)
        ]);
    }
}
