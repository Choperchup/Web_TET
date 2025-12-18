@extends('layouts.admin.main')

@section('title', 'Quản lý Sản phẩm')

@section('admin_content')
    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                {{-- Tiêu đề --}}
                <h1 class="h3 mb-4 pb-2 border-bottom text-dark">Quản Lý Sản Phẩm</h1>

                {{-- Hiển thị thông báo (theo mẫu) --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Nút Tạo Mới và Thùng rác --}}
                <div class="d-flex gap-2 justify-content-end mb-3">
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary shadow-sm">
                        <i class="fas fa-plus me-1"></i> Thêm Sản Phẩm Mới
                    </a>

                    <a href="{{ route('admin.products.trash') }}" class="btn btn-warning shadow-sm">
                        <i class="bi bi-trash"></i> Thùng rác
                    </a>
                </div>

                {{-- Khung tìm kiếm --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.products.index') }}" class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <input type="search" name="q" class="form-control"
                                    placeholder="Tìm kiếm theo tên sản phẩm..." value="{{ request('q') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">-- Chọn trạng thái --</option>
                                    <option value="published" @if(request('status') == 'published') selected @endif>Hoạt động</option>
                                    <option value="draft" @if(request('status') == 'draft') selected @endif>Tạm ẩn</option>
                                </select>
                            </div>
                            <div class="col-md-4 text-end">
                                <button type="submit" class="btn btn-dark">Tìm kiếm</button>
                                @if (request('q') || request('status'))
                                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Xóa tìm
                                        kiếm</a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-lg border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>SKU</th> {{-- CỘT MỚI: SKU --}}
                                        <th>Ảnh</th>
                                        <th>Tên Sản Phẩm</th>
                                        <th>Danh Mục</th>
                                        <th>Giá Bán</th>
                                        <th>Giá Khuyến Mãi</th> {{-- CỘT MỚI: GIÁ KHUYẾN MÃI --}}
                                        <th>Tồn Kho</th>
                                        <th>Trạng Thái</th>
                                        <th class="text-center">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($products as $product)
                                        <tr>
                                            <td>{{ $product->id }}</td>
                                            <td>{{ $product->sku }}</td> {{-- HIỂN THỊ SKU --}}
                                            {{-- HIỂN THỊ HÌNH ẢNH --}}
                                            <td>
                                                @if ($product->thumbnail)
                                                    <img src="{{ asset('storage/' . $product->thumbnail) }}"
                                                        alt="{{ $product->name }}"
                                                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                                @else
                                                    <i class="text-muted">Không ảnh</i>
                                                @endif
                                            </td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->category->name ?? 'Không có' }}</td>
                                            <td>{{ number_format($product->price) }} VNĐ</td>
                                            {{-- ✅ ĐÃ SỬA: Thêm ID để JS cập nhật động --}}
                                            <td id="sale-price-display-{{ $product->id }}">
                                                {{-- HIỂN THỊ GIÁ KHUYẾN MÃI (CHỈ HIỂN THỊ NẾU CÓ VÀ ĐANG BẬT) --}}
                                                @if ($product->sale_price > 0)
                                                    <span class="text-danger fw-bold">{{ number_format($product->sale_price) }} VNĐ</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $product->stock }}</td>
                                            {{-- Cột Trạng Thái --}}
                                            <td>
                                            {{-- ✅ ĐÃ SỬA: Dùng d-flex flex-column để đặt 2 trạng thái (Active và Sale) xếp chồng --}}
                                                <div class="d-flex flex-column align-items-start gap-1">
        
                                                {{-- 1. Trạng thái Hoạt Động/Tạm ẩn (status) --}}
                                                <div class="form-check form-switch p-0 m-0 d-flex align-items-center">
                                                <input class="form-check-input status-toggle m-0" type="checkbox" role="switch"
                                                    id="status-{{ $product->id }}"
                                                    data-product-id="{{ $product->id }}"
                                                    data-toggle-url="{{ route('admin.products.toggle_status', $product) }}"
                                                    {{ $product->status === 'published' ? 'checked' : '' }}>

                                                {{-- Label hiển thị trạng thái --}}
                                                <label class="form-check-label ms-2" for="status-{{ $product->id }}">
                                                    <span id="status-label-{{ $product->id }}"
                                                        class="badge {{ $product->status === 'published' ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $product->status === 'published' ? 'Hoạt động' : 'Tạm ẩn' }}
                                                    </span>
                                                </label>
                                                </div>

                                            {{-- 2. Trạng thái Giá Khuyến Mãi (is_on_sale) --}}
                                        @if ($product->sale_price > 0)
            <div class="form-check form-switch p-0 m-0 d-flex align-items-center">
                <input class="form-check-input sale-toggle m-0" type="checkbox" role="switch"
                    id="sale-status-{{ $product->id }}"
                    data-product-id="{{ $product->id }}"
                    data-toggle-url="{{ route('admin.products.toggle_sale', $product) }}"
                    {{ $product->is_on_sale ? 'checked' : '' }}>
                
                <label class="form-check-label ms-2" for="sale-status-{{ $product->id }}">
                    <span id="sale-label-{{ $product->id }}"
                        class="badge {{ $product->is_on_sale ? 'bg-warning text-dark' : 'bg-secondary' }}">
                        {{ $product->is_on_sale ? 'Đang Sale' : 'Tắt Sale' }}
                    </span>
                </label>
            </div>
        @endif

        {{-- Cảnh báo Hết hàng (Giữ nguyên) --}}
        @if ($product->status === 'published' && $product->stock <= 0)
            <div id="stock-warning-{{ $product->id }}" class="text-danger small w-100">Hết hàng!</div>
        @else
            <div id="stock-warning-{{ $product->id }}" class="text-danger small w-100" style="display: none;">Hết hàng!</div>
        @endif
        
    </div>
</td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <a href="{{ route('admin.products.edit', $product) }}"
                                                        class="btn btn-sm btn-info text-white">Sửa</a>
                                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa mềm sản phẩm này không?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-4 text-muted"> {{-- ĐÃ SỬA colspan TỪ 8 LÊN 10 --}}
                                                Chưa có sản phẩm nào được tạo.
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

@push('scripts')
<script>
$(document).ready(function() {
    // Xử lý sự kiện khi nút switch Trạng thái chính được thay đổi
    $('.status-toggle').on('change', function() {
        let checkbox = $(this);
        let productId = checkbox.data('product-id');
        let toggleUrl = checkbox.data('toggle-url');
        let isChecked = checkbox.prop('checked');
        let statusLabel = $('#status-label-' + productId);
        let stockWarning = $('#stock-warning-' + productId);

        // Tạm thời disable nút để tránh spam
        checkbox.prop('disabled', true);

        // Thực hiện yêu cầu AJAX
        $.ajax({
            url: toggleUrl,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}', // Đảm bảo có CSRF token
                _method: 'POST',
            },
            success: function(response) {
                if (response.success) {
                    // Cập nhật label và class
                    statusLabel.text(response.status_label);
                    statusLabel.removeClass('bg-success bg-secondary').addClass(response.status_class);

                    // Xử lý cảnh báo hết hàng
                    if (response.new_status === 'published' && response.stock <= 0) {
                        stockWarning.show();
                    } else {
                        stockWarning.hide();
                    }

                    // Kích hoạt lại nút
                    checkbox.prop('disabled', false);

                } else {
                    // Quay lại trạng thái ban đầu nếu thất bại
                    checkbox.prop('checked', !isChecked);
                    alert('Có lỗi xảy ra: ' + (response.message || 'Lỗi không xác định.'));
                    checkbox.prop('disabled', false);
                }
            },
            error: function(xhr) {
                // Quay lại trạng thái ban đầu nếu lỗi
                checkbox.prop('checked', !isChecked);
                alert('Lỗi kết nối hoặc quyền truy cập. Vui lòng kiểm tra console.');
                console.error(xhr.responseText);
                checkbox.prop('disabled', false);
            }
        });
    });

    // ✅ BỔ SUNG: Xử lý sự kiện khi nút switch Giá Khuyến Mãi được thay đổi
    $('.sale-toggle').on('change', function() {
        let checkbox = $(this);
        let productId = checkbox.data('product-id');
        let toggleUrl = checkbox.data('toggle-url');
        let isChecked = checkbox.prop('checked');
        let saleLabel = $('#sale-label-' + productId);
        let salePriceDisplay = $('#sale-price-display-' + productId); // ✅ THÊM: Biến này

        // Tạm thời disable nút để tránh spam
        checkbox.prop('disabled', true);

        // Thực hiện yêu cầu AJAX
        $.ajax({
            url: toggleUrl,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}', // Đảm bảo có CSRF token
                _method: 'POST',
            },
            success: function(response) {
                if (response.success) {
                    // Cập nhật label và class
                    saleLabel.text(response.status_label);
                    // Dùng remove/add class để chuyển đổi giữa trạng thái Đang Sale và Tắt Sale
                    saleLabel.removeClass('bg-warning bg-secondary text-dark').addClass(response.status_class);

                    // ✅ CẬP NHẬT GIÁ KHUYẾN MÃI TRONG BẢNG ADMIN
                    if (response.new_status) {
                        // Nếu đang bật sale, hiển thị giá sale
                        salePriceDisplay.html('<span class="text-danger fw-bold">' + response.sale_price_formatted + '</span>');
                    } else {
                        // Nếu tắt sale, hiển thị "-"
                        salePriceDisplay.html('-');
                    }
                } else {
                    // Quay lại trạng thái ban đầu nếu thất bại
                    checkbox.prop('checked', !isChecked);
                    alert('Có lỗi xảy ra: ' + (response.message || 'Lỗi không xác định.'));
                }
                checkbox.prop('disabled', false);
            },
            error: function(xhr) {
                // Quay lại trạng thái ban đầu nếu lỗi
                checkbox.prop('checked', !isChecked);
                alert('Lỗi kết nối hoặc quyền truy cập. Vui lòng kiểm tra console.');
                console.error(xhr.responseText);
                checkbox.prop('disabled', false);
            }
        });
    });
});
</script>
@endpush