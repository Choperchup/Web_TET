<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostReuqest;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use function Symfony\Component\String\b;

class PostController extends Controller
{
    public function index()
    {
        // Logic to retrieve and display all posts
        $posts = DB::table('post')->get();
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        // Logic to show form for creating a new post
        $posts = DB::table('post')->get();
        return view('posts.create', compact('posts'));
    }

    public function store(StorePostReuqest $request)
    {
        // Xử lý lưu file hình ảnh
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('images', $fileName, 'public');
        }


        $newPost = Post::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'created_at' => now(),
            'updated_at' => now(),
            'thumbnail' => $path ?? null,
        ]);

        return redirect()->route('posts.index')->with('success', 'Post created successfully.');
    }

    public function edit($id)
    {
        // Logic to show form for editing a post
        $post = DB::table('post')->where('id', $id)->first();
        if (!$post) {
            return redirect()->route('posts.index')->with('error', 'Post not found.');
        }
        return view('posts.edit', compact('post'));
    }

    public function update(StorePostReuqest $request, $id)
    {
        $post = Post::findOrFail($id);

        if ($request->hasFile('thumbnail')) {
            Storage::delete($post->thumbnail);

            $file = $request->file('thumbnail');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('images', $fileName, 'public');
            $post->thumbnail = $path;
        }

        // Logic to update a post
        // $updated = DB::table('post')->where('id', $id)->update([
        //     'title' => $request->input('title'),
        //     'content' => $request->input('content'),
        //     'updated_at' => now(),
        // ]);
        $updated = $post->update([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'thumbnail' => $path ?? $post->thumbnail,
        ]);

        if ($updated) {
            return redirect()->route('posts.index')->with('success', 'Post updated successfully.');
        } else {
            return redirect()->route('posts.index')->with('error', 'Post not found or no changes made.');
        }
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        // Logic to delete a post
        $deleted = DB::table('post')->where('id', $id)->delete();

        Storage::delete($post->thumbnail);

        if ($deleted) {
            return back()->with('success', 'Post deleted successfully.');
        } else {
            return back()->with('error', 'Post not found.');
        }
    }
}
