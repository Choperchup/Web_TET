@extends('layouts.main')

@section('title', 'Posts List')
@section('content')
    <h1 class="mb-4">Sửa bài viết</h1>
    <form method="POST" action="{{ route('posts.update', ['id' => $post->id]) }}" novalidate enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="_method" value="PUT">
        <div class="mb-3">
            <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->title }}"
                style="width: 100px; height: auto;">
        </div>
        <div class="mb-3">
            <label class="form-lable">Thumbnail</label><br>
            <input type="file" name="thumbnail" accept="image/*/">
            @error('thumbnail')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="title" class="form-label">Tiêu đề</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ $post->title }}" required>
            @error('title')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Content</label>
            <textarea class="form-control" id="content" name="content" rows="5" required>{{ $post->content }}</textarea>
            @error('content')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('posts.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
@endsection