@extends('layouts.admin.main')

@section('admin_content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-white">
                        <h1 class="h3 font-weight-bold text-dark mb-0">Chỉnh Sửa Danh Mục: <span
                                class="text-primary">{{ $categories->name }}</span></h1>
                    </div>
                    <div class="card-body p-4">

                        <!-- Form Chỉnh Sửa -->
                        <form action="{{ route('admin.categories.update', $categories) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Tên Danh Mục -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Tên Danh Mục</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $categories->name) }}"
                                    required class="form-control @error('name') is-invalid @enderror">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Slug (Chỉ hiển thị) -->
                            <div class="mb-3">
                                <label class="form-label">Slug</label>
                                <input type="text" readonly class="form-control-plaintext text-muted"
                                    value="{{ $categories->slug }}">
                            </div>

                            <!-- Mô Tả -->
                            <div class="mb-4">
                                <label for="description" class="form-label">Mô Tả (Tùy chọn)</label>
                                <textarea name="description" id="description" rows="4"
                                    class="form-control @error('description') is-invalid @enderror">{{ old('description', $categories->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary me-2">
                                    Hủy
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Cập Nhật Danh Mục
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection