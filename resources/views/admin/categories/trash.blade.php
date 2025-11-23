@extends('layouts.admin.main')

@section('admin_content')
    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-4 pb-2 border-bottom text-dark">Thùng Rác Danh Mục</h1>

                <!-- Hiển thị thông báo (nếu có) -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="d-flex justify-content-start mb-3">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary shadow-sm">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>

                <!-- Bảng Danh Mục Đã Xóa -->
                <div class="card shadow-lg border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col" class="py-3">ID</th>
                                        <th scope="col" class="py-3">Tên Danh Mục</th>
                                        <th scope="col" class="py-3">Slug</th>
                                        <th scope="col" class="py-3">Thời gian xóa</th>
                                        <th scope="col" class="text-center py-3">Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($categories as $category)
                                        <tr>
                                            <td class="align-middle">{{ $category->id }}</td>
                                            <td class="align-middle">{{ $category->name }}</td>
                                            <td class="align-middle text-muted">{{ $category->slug }}</td>
                                            <!-- Sử dụng diffForHumans() để hiển thị thời gian thân thiện -->
                                            <td class="align-middle text-danger">{{ $category->deleted_at->diffForHumans() }}
                                            </td>
                                            <td class="text-center align-middle" style="min-width: 150px;">
                                                <!-- Form Khôi phục -->
                                                <form action="{{ route('admin.categories.restore', $category->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Bạn có muốn khôi phục danh mục này không?');">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-success me-2">Khôi phục</button>
                                                </form>

                                                <!-- Form Xóa vĩnh viễn -->
                                                <form action="{{ route('admin.categories.forceDelete', $category->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('CẢNH BÁO: Hành động này sẽ xóa vĩnh viễn danh mục khỏi cơ sở dữ liệu. Bạn có chắc chắn không?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Xóa Vĩnh Viễn</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">Thùng rác rỗng. Không có danh
                                                mục nào đã bị xóa mềm.</td>
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