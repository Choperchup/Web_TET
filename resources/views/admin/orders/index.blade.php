@extends('layouts.admin.main') 

@section('admin_content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Quản Lý Đơn Hàng</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Bộ lọc và Tìm kiếm --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="q" class="form-control" placeholder="Tìm kiếm theo ID, Tên, SĐT khách hàng" value="{{ request('q') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                @switch($status)
                                    @case('all') Tất cả @break
                                    @case('pending') Chờ xử lý @break
                                    @case('confirmed') Đã xác nhận @break
                                    @case('shipping') Đang giao @break
                                    @case('delivered') Đã giao @break
                                    @case('canceled') Đã hủy @break
                                @endswitch
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Lọc</button>
                    @if (request()->has('q') || request()->has('status'))
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Xóa</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Bảng danh sách đơn hàng --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách ({{ $orders->total() }} đơn hàng)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Thanh toán</th>
                            <th>Trạng thái</th>
                            <th>Ngày đặt</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>
                                <strong>{{ $order->customer_name }}</strong><br>
                                <small class="text-muted">{{ $order->customer_phone }}</small>
                            </td>
                            <td>{{ number_format($order->total_amount) }} VNĐ</td>
                            <td>{{ $order->payment_method }}</td>
                            <td>
                                @php
                                    $badgeClass = match($order->status) {
                                        'pending' => 'bg-warning',
                                        'confirmed' => 'bg-info',
                                        'shipping' => 'bg-primary',
                                        'delivered' => 'bg-success',
                                        'canceled' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                    // THÊM PHẦN DỊCH SANG TIẾNG VIỆT
                                    $statusText = match($order->status) {
                                        'pending' => 'Chờ xử lý',
                                        'confirmed' => 'Đã xác nhận',
                                        'shipping' => 'Đang giao',
                                        'delivered' => 'Đã giao',
                                        'canceled' => 'Đã hủy',
                                        default => 'Không rõ'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} text-white">{{ $statusText }}</span> {{-- ĐÃ SỬA: Hiển thị trạng thái bằng tiếng Việt --}}
                            </td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-info me-1">Chi tiết</a>
                                
                                {{-- Nút Xác nhận --}}
                                @if ($order->status == 'pending')
                                    <form action="{{ route('admin.orders.confirm', $order) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" 
                                                onclick="return confirm('Bạn có chắc chắn muốn XÁC NHẬN đơn hàng này? Việc này sẽ GIẢM tồn kho sản phẩm.')">
                                            Xác nhận
                                        </button>
                                    </form>
                                @endif

                                {{-- Nút Hủy bỏ --}}
                                @if (in_array($order->status, ['pending', 'confirmed', 'shipping']))
                                    <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger mt-1" 
                                                onclick="return confirm('Bạn có chắc chắn muốn HỦY đơn hàng này? Việc này có thể HOÀN TRẢ tồn kho.')">
                                            Hủy
                                        </button>
                                    </form>
                                @endif
                                
                                {{-- Thêm nút Cập nhật trạng thái Đang giao/Đã giao cho các đơn hàng đã được xác nhận (confirmed) hoặc đang giao (shipping) --}}
                                @if (in_array($order->status, ['confirmed', 'shipping']))
                                    <a href="#" class="btn btn-sm btn-warning mt-1" data-bs-toggle="modal" data-bs-target="#updateStatusModal" 
                                        data-order-id="{{ $order->id }}" data-current-status="{{ $order->status }}">
                                        Cập nhật TT
                                    </a>
                                @endif

                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Không tìm thấy đơn hàng nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
    
    {{-- MODAL CẬP NHẬT TRẠNG THÁI (ĐANG GIAO, ĐÃ GIAO) --}}
    {{-- Bạn nên thêm modal này vào cuối file để xử lý cập nhật trạng thái khác Confirm/Cancel --}}
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="updateStatusForm" method="POST" action="">
                    @csrf
                    {{-- Laravel không hỗ trợ PUT qua form đơn thuần, phải dùng method field --}}
                    @method('PUT') 
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateStatusModalLabel">Cập nhật Trạng thái Đơn hàng #<span id="orderIdDisplay"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">Chọn trạng thái mới</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="shipping">Đang giao hàng (Shipping)</option>
                                <option value="delivered">Đã giao hàng (Delivered)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    {{-- SCRIPT CHO MODAL (Cần jQuery hoặc Bootstrap JS để hoạt động) --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var updateStatusModal = document.getElementById('updateStatusModal');
            if (updateStatusModal) {
                updateStatusModal.addEventListener('show.bs.modal', function (event) {
                    // Button that triggered the modal
                    var button = event.relatedTarget;
                    // Extract info from data-bs-* attributes
                    var orderId = button.getAttribute('data-order-id');
                    var currentStatus = button.getAttribute('data-current-status');

                    // Update the modal's content.
                    var modalTitle = updateStatusModal.querySelector('.modal-title #orderIdDisplay');
                    var statusSelect = updateStatusModal.querySelector('#status');
                    var updateForm = updateStatusModal.querySelector('#updateStatusForm');

                    modalTitle.textContent = orderId;
                    
                    // *** DÒNG SỬA LỖI QUAN TRỌNG NHẤT ***
                    // Tạo URL chính xác từ route name và thay thế tạm thời 'TEMP_ID' bằng orderId thực tế.
                    const routeTemplate = "{{ route('admin.orders.updateStatus', ['order' => 'TEMP_ID']) }}";
                    updateForm.action = routeTemplate.replace('TEMP_ID', orderId);
                    
                    // Cập nhật trạng thái đang được chọn
                    statusSelect.value = currentStatus === 'confirmed' ? 'shipping' : currentStatus;
                });
            }
        });
    </script>
    @endpush
</div>
@endsection