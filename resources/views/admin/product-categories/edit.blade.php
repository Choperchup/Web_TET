@extends('layouts.admin.main')

@section('admin_content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h1 class="mb-4 text-dark">Chỉnh sửa Danh mục: {{ $productCategory->name }}</h1>

                <div class="card shadow-lg">
                    <div class="card-body p-4">
                        <form action="{{ route('admin.product-categories.update', $productCategory) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                {{-- Tên Danh mục --}}
                                <div class="col-12 mb-3">
                                    <label for="name" class="form-label">Tên Danh mục <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name"
                                        value="{{ old('name', $productCategory->name) }}" required
                                        class="form-control @error('name') is-invalid @enderror">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Danh mục Cha (Chỉ hiển thị danh mục gốc, trừ chính nó) --}}
                                <div class="col-12 mb-3">
                                    <label for="parent_id" class="form-label">Danh mục Cha</label>
                                    <select name="parent_id" id="parent_id"
                                        class="form-select @error('parent_id') is-invalid @enderror">
                                        <option value="">-- Bỏ trống nếu là danh mục Cấp 1 --</option>
                                        @foreach ($parentCategories as $parentCategory)
                                            <option value="{{ $parentCategory->id }}"
                                                {{ old('parent_id', $productCategory->parent_id) == $parentCategory->id ? 'selected' : '' }}>
                                                {{ $parentCategory->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Slug (Đường dẫn thân thiện) --}}
                                <div class="col-12 mb-3">
                                    <label for="slug" class="form-label">Slug (Đường dẫn thân thiện)</label>
                                    <input type="text" name="slug" id="slug"
                                        value="{{ old('slug', $productCategory->slug) }}"
                                        class="form-control @error('slug') is-invalid @enderror">
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                {{-- Icon URL --}}
                                <div class="col-12 mb-4">
                                    <label for="icon_url" class="form-label">Icon/Hình ảnh URL</label>
                                    <input type="text" name="icon_url" id="icon_url"
                                        value="{{ old('icon_url', $productCategory->icon_url) }}"
                                        class="form-control @error('icon_url') is-invalid @enderror">
                                    @error('icon_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Mô tả --}}
                                <div class="col-12 mb-4">
                                    <label for="content" class="form-label">Mô tả</label>
                                    <textarea name="content" id="content" rows="3"
                                        class="form-control @error('content') is-invalid @enderror">{{ old('content', $productCategory->content) }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.product-categories.index') }}"
                                    class="btn btn-secondary me-2">Hủy</a>
                                <button type="submit" class="btn btn-success">
                                    Cập nhật Danh mục
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
