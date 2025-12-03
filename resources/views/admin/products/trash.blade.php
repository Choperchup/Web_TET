@extends('layouts.admin.main')

@section('admin_content')
    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-4 pb-2 border-bottom text-dark">Thùng Rác Sản Phẩm</h1>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="d-flex justify-content-start mb-3">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary shadow-sm">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại Danh sách Sản Phẩm
                    </a>
                </div>

                <div class="card shadow-lg border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên Sản Phẩm</th>
                                        <th>Danh Mục</th>
                                        <th>Giá Bán</th>
                                        <th>Thời gian Xóa</th>
                                        <th class="text-center">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($products as $product)
                                        <tr>
                                            <td>{{ $product->id }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->category->name ?? 'Không có' }}</td>
                                            <td>{{ number_format($product->price) }} VNĐ</td>
                                            <td>{{ $product->deleted_at->format('d/m/Y H:i') }}</td>
                                            <td class="text-center">
                                                {{-- **ĐÃ SỬA: Chuyển từ thẻ <a> sang <form> dùng @method('PUT')** --}}
                                                        <form action="{{ route('admin.products.restore', $product->id) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Bạn có chắc chắn muốn khôi phục sản phẩm này không?');">
                                                            @csrf
                                                            @method('PUT') {{-- Bắt buộc phải có chỉ thị này --}}
                                                            <button type="submit" class="btn btn-sm btn-success me-1">Khôi
                                                                Phục</button>
                                                        </form>

                                                        <form action="{{ route('admin.products.forceDelete', $product->id) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('CẢNH BÁO: Hành động này sẽ xóa vĩnh viễn sản phẩm khỏi cơ sở dữ liệu. Bạn có chắc chắn không?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">Xóa Vĩnh
                                                                Viễn</button>
                                                        </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                Thùng rác rỗng. Không có sản phẩm nào đã bị xóa mềm.
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
                    {{ $products->links() }}
                </div>

            </div>
        </div>
    </div>
@endsection