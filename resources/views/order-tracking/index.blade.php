@extends('layouts.main') 

@section('content')
<div class="container py-5">
    <h1 class="fw-bold text-center mb-5 text-primary">Theo Dõi Tình Trạng Đơn Hàng</h1>
    
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg p-4 mb-5">
                
                {{-- Hiển thị thông báo lỗi/thành công --}}
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                
                @if (isset($success_message))
                    <div class="alert alert-success">{{ $success_message }}</div>
                @endif
                {{-- Kết thúc hiển thị thông báo --}}

                {{-- FORM TRA CỨU --}}
                <form method="POST" action="{{ route('order-tracking.track') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="order_id" class="form-label fw-bold">Mã Đơn Hàng (*)</label>
                        <input type="text" class="form-control @error('order_id') is-invalid @enderror" id="order_id" name="order_id" value="{{ old('order_id', request('order_id')) }}" required placeholder="Ví dụ: 1234">
                        @error('order_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="customer_phone" class="form-label fw-bold">Số Điện Thoại Đặt Hàng (*)</label>
                        <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" id="customer_phone" name="customer_phone" value="{{ old('customer_phone', request('customer_phone')) }}" required placeholder="Ví dụ: 0901234567">
                        @error('customer_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Tra Cứu Đơn Hàng</button>
                </form>
            </div>

            {{-- HIỂN THỊ KẾT QUẢ TRA CỨU --}}
            @if (isset($order))
                @php
                    // Định nghĩa ánh xạ trạng thái
                    $statusMapping = [
                        'pending' => ['label' => 'Chờ xử lý', 'class' => 'warning', 'icon' => 'clock'],
                        'confirmed' => ['label' => 'Đã xác nhận', 'class' => 'info', 'icon' => 'check-circle'],
                        'shipping' => ['label' => 'Đang giao hàng', 'class' => 'primary', 'icon' => 'truck'],
                        'delivered' => ['label' => 'Đã giao thành công', 'class' => 'success', 'icon' => 'box'],
                        'canceled' => ['label' => 'Đã hủy', 'class' => 'danger', 'icon' => 'times-circle'],
                    ];
                    $currentStatus = $statusMapping[$order->status] ?? ['label' => 'Không rõ', 'class' => 'secondary', 'icon' => 'question-circle'];
                @endphp
                
                <div class="card shadow-lg mt-4 border-{{ $currentStatus['class'] }}">
                    <div class="card-header bg-{{ $currentStatus['class'] }} text-white text-center">
                        <h4 class="mb-0">
                            <i class="fas fa-{{ $currentStatus['icon'] }} me-2"></i> 
                            Tình Trạng Đơn Hàng #{{ $order->id }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-6 fw-bold">Trạng Thái Hiện Tại:</div>
                            <div class="col-sm-6 text-{{ $currentStatus['class'] }} fw-bolder">{{ $currentStatus['label'] }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-6">Ngày Đặt Hàng:</div>
                            {{-- Đảm bảo đã sửa Model Order để dùng format() an toàn --}}
                            <div class="col-sm-6">{{ $order->created_at->format('H:i:s d/m/Y') }}</div>
                        </div>
                        
                        @if ($order->confirmed_at)
                        <div class="row mb-3">
                            <div class="col-sm-6">Thời gian Xác Nhận:</div>
                            <div class="col-sm-6 text-info">{{ $order->confirmed_at->format('H:i:s d/m/Y') }}</div>
                        </div>
                        @endif

                        <h5 class="mt-4 mb-3 border-bottom pb-2">Chi Tiết Sản Phẩm:</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Số lượng</th>
                                        <th>Giá</th>
                                        <th>Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->details as $detail)
                                    <tr>
                                        <td>{{ $detail->product_name }}</td>
                                        <td>{{ $detail->quantity }}</td>
                                        <td>{{ number_format($detail->price) }} VNĐ</td>
                                        <td>{{ number_format($detail->price * $detail->quantity) }} VNĐ</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Tổng tiền đơn hàng:</th>
                                        <th class="text-danger">{{ number_format($order->total_amount) }} VNĐ</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection