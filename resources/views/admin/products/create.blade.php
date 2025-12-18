@extends('layouts.admin.main')

@section('title', 'Quản lý Sản phẩm')

@section('admin_content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <h1 class="mb-4 text-dark">Tạo Sản Phẩm Mới</h1>

                <div class="card shadow-lg">
                    <div class="card-body p-4">
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

                                {{-- Giá Bán Gốc --}}
                                <div class="col-md-6 mb-3">
                                    <label for="price" class="form-label">Giá Bán Gốc (VNĐ) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="price" id="price" value="{{ old('price') }}" required
                                        class="form-control @error('price') is-invalid @enderror">
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- ✨ Bổ sung: Giá Khuyến Mãi/Sale Price --}}
                                <div class="col-md-6 mb-3">
                                    <label for="sale_price" class="form-label">Giá Khuyến Mãi (VNĐ) <span class="text-muted">(Không bắt buộc)</span></label>
                                    <input type="number" name="sale_price" id="sale_price" value="{{ old('sale_price') }}"
                                        class="form-control @error('sale_price') is-invalid @enderror">
                                    @error('sale_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- ✅ BỔ SUNG: SKU (Mã Sản Phẩm) --}}
                                <div class="col-md-6 mb-3">
                                    <label for="sku" class="form-label">SKU (Mã Sản Phẩm)</label>
                                    <input type="text" name="sku" id="sku" value="{{ old('sku') }}"
                                        class="form-control @error('sku') is-invalid @enderror">
                                    @error('sku')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- END BỔ SUNG: SKU --}}

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
                                        accept="image/*">
                                    @error('thumbnail')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Kích hoạt/Trạng thái & Nổi bật --}}
                                <div class="col-md-6 mb-3 d-flex align-items-center pt-4">
                                    <div class="form-check form-switch me-4">
                                        {{-- Mặc định 'checked' (published) nếu không có old('is_active') --}}
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

                                {{-- Mô tả Ngắn --}}
                                <div class="col-12 mb-4">
                                    <label for="short_description" class="form-label">Mô tả Ngắn (Hiển thị tóm tắt)</label>
                                    <textarea name="short_description" id="short_description" rows="2"
                                        class="form-control @error('short_description') is-invalid @enderror">{{ old('short_description') }}</textarea>
                                    @error('short_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Mô tả Chi tiết/Nội dung (Đã bỏ dấu * để đồng bộ với Edit) --}}
                                <div class="col-12 mb-4">
                                    <label for="content" class="form-label">Nội dung Chi tiết Sản Phẩm</label>
                                    <textarea name="content" id="content" rows="5"
                                        class="form-control @error('content') is-invalid @enderror">{{ old('content') }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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