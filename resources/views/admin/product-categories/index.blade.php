@extends('layouts.admin.main')

@section('admin_content')
    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                {{-- Tiêu đề --}}
                <h1 class="h3 mb-4 pb-2 border-bottom text-dark">Danh Mục Sản Phẩm</h1>

                {{-- Hiển thị thông báo --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Nút Tạo Mới và Thùng rác --}}
                <div class="d-flex gap-2 justify-content-end mb-3">
                    <a href="{{ route('admin.product-categories.create') }}" class="btn btn-primary shadow-sm">
                        <i class="fas fa-plus me-1"></i> Thêm Danh Mục Mới
                    </a>
                    <a href="{{ route('admin.product-categories.trash') }}" class="btn btn-warning shadow-sm">
                        <i class="bi bi-trash"></i> Thùng rác
                    </a>
                </div>

                {{-- Khu vực Bảng Danh Mục --}}
                <div class="card shadow-lg border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Tên Danh Mục</th>
                                        <th scope="col">Slug</th>
                                        <th scope="col">Danh Mục Cha</th>
                                        <th scope="col">Sản Phẩm</th>
                                        <th scope="col">Mô Tả</th>
                                        <th scope="col">Thời Gian Tạo</th>
                                        <th scope="col">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($categories as $category)
                                        <tr>
                                            <td class="align-middle">{{ $category->id }}</td>
                                            {{-- LOGIC HIỂN THỊ CẤP BẬC --}}
                                            <td class="align-middle">
                                                @if ($category->parent_id)
                                                    <span class="ms-4 me-2 text-muted">↳</span>
                                                @endif
                                                {{ $category->name }}
                                            </td>
                                            <td class="align-middle">{{ $category->slug }}</td>
                                            {{-- HIỂN THỊ TÊN DANH MỤC CHA SỬ DỤNG MỐI QUAN HỆ 'parent' --}}
                                            <td class="align-middle">
                                                {{ $category->parent ? $category->parent->name : '---' }}
                                            </td>
                                            {{-- HIỂN THỊ SỐ LƯỢNG SẢN PHẨM --}}
                                            <td class="align-middle text-center">
                                                <span class="badge bg-primary">{{ $category->products_count }}</span>
                                            </td>
                                            <td class="align-middle">
                                                {{ Str::limit($category->description, 50) }}
                                            </td>
                                            <td class="align-middle">
                                                {{ $category->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="align-middle">
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.product-categories.edit', $category) }}"
                                                        class="btn btn-sm btn-info text-white">Sửa</a>

                                                    <form action="{{ route('admin.product-categories.destroy', $category) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa mềm danh mục này không?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                Chưa có danh mục sản phẩm nào được tạo.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Phân trang --}}
                <div class="mt-4">
                    {{ $categories->links() }}
                </div>

            </div>
        </div>
    </div>
@endsection