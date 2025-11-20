@extends('layouts.main')

@section('content')
    <h1 class="mb-4">Cập nhật danh mục</h1>
    <form method="POST" action="{{ route('categories.update', ['id' => $category->id]) }}" novalidate>
        @csrf
        <input type="hidden" name="_method" value="PUT">
        <div class="mb-3">
            <label for="title" class="form-label">Name</label>
            <input type="text" class="form-control" id="title" name="name" value="{{ $category->name }}" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="5"
                required>{{ $category->description }}</textarea>
            @error('description')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-success">Lưu</button>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
@endsection