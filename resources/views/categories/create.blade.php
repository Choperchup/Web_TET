@extends('layouts.main')

@section('content')
    <h1 class="mb-4">Tạo danh mục mới</h1>
    <form method="POST" action="{{ route('categories.store') }}" novalidate>
        @csrf
        <div class="mb-3">
            <label for="title" class="form-label">Name</label>
            <input type="text" class="form-control" id="title" name="name" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
            @error('description')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-success">Lưu</button>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
@endsection