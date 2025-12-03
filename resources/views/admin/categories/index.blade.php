@extends('layouts.admin.main')

@section('admin_content')
    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-4 pb-2 border-bottom text-dark">Danh Mục Bài Viết</h1>

                <!-- Hiển thị thông báo (nếu có) -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Nút Tạo Mới -->
                <div class="d-flex gap-2 justify-content-end mb-3">
                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary shadow-sm">
                        Thêm Danh Mục Mới
                    </a>
                    <a href="{{ route('admin.categories.trash') }}" class="btn btn-warning shadow-sm"><i
                            class="bi bi-trash"></i> Thùng rác
                    </a>
                </div>

                {{-- Khối Tìm kiếm (Giữ nguyên) --}}
                <div class="d-flex justify-content-end mb-3">
                    <form action="#" method="GET" class="d-flex align-items-center">
                        <input type="hidden" name="status" value="#">
                        <input type="text" name="q" placeholder="Tìm theo tên..." value="{{ request('q') }}"
                            class="form-control form-control-sm me-2" style="width: 250px;">
                        <button type="submit" class="btn btn-secondary btn-sm">
                            Tìm kiếm
                        </button>
                    </form>
                </div>

                <!-- Bảng Danh Mục -->
                <div class="card shadow-lg border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="py-3">ID</th>
                                        <th scope="col" class="py-3">Tên Danh Mục</th>
                                        <th scope="col" class="py-3">Slug</th>
                                        <th scope="col" class="py-3">Mô Tả</th>
                                        <th scope="col" class="text-center py-3">Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($categories as $category)
                                        <tr>
                                            <td class="align-middle">{{ $category->id }}</td>
                                            <td class="align-middle">{{ $category->name }}</td>
                                            <td class="align-middle text-muted">{{ $category->slug }}</td>
                                            <td class="align-middle text-muted"
                                                style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                {{ $category->description ?? 'Không có mô tả' }}
                                            </td>
                                            <td class="text-center align-middle">
                                                <a href="{{ route('admin.categories.edit', $category) }}"
                                                    class="btn btn-sm btn-info text-white me-2">Sửa</a>
                                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa mềm danh mục này không?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">Chưa có danh mục nào được tạo.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection