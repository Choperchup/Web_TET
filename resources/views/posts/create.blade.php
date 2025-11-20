@extends('layouts.main')

@section('content')
    <h1 class="mb-4">Tạo bài viết mới</h1>
    <form method="POST" action="{{ route('posts.store') }}" novalidate enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-lable">Thumbnail</label><br>
            <input type="file" name="thumbnail" accept="image/*/">
            @error('thumbnail')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="title" class="form-label">Tiêu đề</label>
            <input type="text" class="form-control" id="title" name="title" required>
            @error('title')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Content</label>
            <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
            @error('content')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-success">Lưu</button>
        <a href="{{ route('posts.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
@endsection