@extends('layouts.admin.main')

@section('title', 'Quản lý Sản phẩm')

@section('admin_content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <h1 class="mb-4 text-dark">Tạo Sản Phẩm Mới</h1>

                <div class="card shadow-lg">
                    <div class="card-body p-4">
                        {{-- Quan trọng: Thêm enctype="multipart/form-data" để upload file --}}
                        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                {{-- Tên Sản Phẩm --}}
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Tên Sản Phẩm <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                        class="form-control @error('name') is-invalid @enderror">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Slug --}}
                                <div class="col-md-6 mb-3">
                                    <label for="slug" class="form-label">Slug (Đường dẫn thân thiện)</label>
                                    <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                                        class="form-control @error('slug') is-invalid @enderror">
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Danh mục --}}
                                <div class="col-md-6 mb-3">
                                    <label for="category_id" class="form-label">Danh Mục <span
                                            class="text-danger">*</span></label>
                                    <select name="category_id" id="category_id" required
                                        class="form-select @error('category_id') is-invalid @enderror">
                                        <option value="">-- Chọn Danh mục --</option>
                                        {{-- Giả định biến $categories chứa danh sách ProductCategory --}}
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Giá Bán --}}
                                <div class="col-md-6 mb-3">
                                    <label for="price" class="form-label">Giá Bán (VNĐ) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="price" id="price" value="{{ old('price') }}" required
                                        class="form-control @error('price') is-invalid @enderror">
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Tồn Kho --}}
                                <div class="col-md-6 mb-3">
                                    <label for="stock" class="form-label">Số Lượng Tồn Kho <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}" required
                                        class="form-control @error('stock') is-invalid @enderror">
                                    @error('stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Hình ảnh Thumbnail --}}
                                <div class="col-md-6 mb-3">
                                    <label for="thumbnail" class="form-label">Ảnh Thumbnail</label>
                                    <input type="file" name="thumbnail" id="thumbnail"
                                        class="form-control @error('thumbnail') is-invalid @enderror"
                                        accept="image/*"> {{-- Chấp nhận mọi định dạng ảnh --}}
                                    @error('thumbnail')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Mô tả Ngắn (Mới thêm) --}}
                                <div class="col-12 mb-4">
                                    <label for="short_description" class="form-label">Mô tả Ngắn (Hiển thị tóm tắt)</label>
                                    <textarea name="short_description" id="short_description" rows="2"
                                        class="form-control @error('short_description') is-invalid @enderror">{{ old('short_description') }}</textarea>
                                    @error('short_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Mô tả Chi tiết/Nội dung (Đổi tên trường từ 'description' sang 'content' để khớp DB) --}}
                                <div class="col-12 mb-4">
                                    <label for="content" class="form-label">Nội dung Chi tiết Sản Phẩm <span class="text-danger">*</span></label>
                                    {{-- ĐÃ SỬA: name="description" -> name="content" --}}
                                    <textarea name="content" id="content" rows="5" required
                                        class="form-control @error('content') is-invalid @enderror">{{ old('content') }}</textarea>
                                    @error('content')
                                        {{-- ĐÃ SỬA: @error('description') -> @error('content') --}}
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Kích hoạt/Trạng thái & Nổi bật --}}
                                <div class="col-md-6 mb-3 d-flex align-items-center pt-4">
                                    <div class="form-check form-switch me-4">
                                        {{-- Checkbox này sẽ được Controller map sang status ENUM ('published'/'draft') --}}
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                            value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Kích hoạt (Hiển thị ra ngoài)</label>
                                    </div>

                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured"
                                            value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_featured">Sản phẩm Nổi bật</label>
                                    </div>
                                    
                                </div>


                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary me-2">Hủy</a>
                                <button type="submit" class="btn btn-primary">
                                    Tạo Sản Phẩm
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection