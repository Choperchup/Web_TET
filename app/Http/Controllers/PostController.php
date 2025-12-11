<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostReuqest;
use Illuminate\Support\Facades\Storage;
use App\Models\Admin\Posts; // Sử dụng đúng Model Posts
use App\Models\Admin\Categories; // Sử dụng đúng Model Categories
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use function Symfony\Component\String\b;

class PostController extends Controller
{
    /**
     * Hiển thị danh sách các bài viết đã được duyệt (published).
     */
    public function index()
    {
        $posts = Posts::with('category', 'author') // Tải quan hệ category và author
            ->where('status', 'published')
            ->latest('published_at') // Sắp xếp theo ngày xuất bản
            ->paginate(9);

        $categories = Categories::all();
        return view('posts.index', compact('posts', 'categories'));
    }

    /**
     * Hiển thị chi tiết một bài viết.
     */
    public function show($slug)
    {
        $post = Posts::with('category', 'author')
            ->where('slug', $slug)
            ->where('status', 'published') // Đảm bảo bài viết đang hoạt động
            ->firstOrFail();
        // Lấy 3 bài viết liên quan (cùng category, trừ bài hiện tại)
        $relatedPosts = Posts::where('category_id', $post->category_id)
            ->where('status', 'published')
            ->where('id', '!=', $post->id)
            ->inRandomOrder()
            ->limit(3)
            ->get();
        return view('posts.show', compact('post', 'relatedPosts'));
    }
}
