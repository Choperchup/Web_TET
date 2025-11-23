@extends('layouts.admin.main')

@section('admin_content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-white">
                        <h1 class="h3 font-weight-bold text-dark mb-0">Tạo Danh Mục Mới</h1>
                    </div>
                    <div class="card-body p-4">

                        <!-- Form Tạo Mới -->
                        <form action="{{ route('admin.categories.store') }}" method="POST">
                            @csrf

                            <!-- Tên Danh Mục -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Tên Danh Mục</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                    class="form-control @error('name') is-invalid @enderror">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Mô Tả -->
                            <div class="mb-4">
                                <label for="description" class="form-label">Mô Tả (Tùy chọn)</label>
                                <textarea name="description" id="description" rows="4"
                                    class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary me-2">
                                    Hủy
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Lưu Danh Mục
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection