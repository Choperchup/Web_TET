@extends('layouts.main')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold text-primary">Chi Tiết Đơn Hàng #{{ $order->id }}</h1>
            <a href="{{ route('users.orders.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Quay lại
            </a>
        </div>

        @php
            $statusBadge = function ($status) {
                $class = match ($status) {
                    'pending' => 'badge bg-warning text-dark',
                    'confirmed' => 'badge bg-info',
                    'shipping' => 'badge bg-primary',
                    'delivered' => 'badge bg-success',
                    'canceled' => 'badge bg-danger',
                    default => 'badge bg-secondary'
                };
                $label = match ($status) {
                    'pending' => 'Chờ xử lý',
                    'confirmed' => 'Đã xác nhận',
                    'shipping' => 'Đang giao hàng',
                    'delivered' => 'Đã giao thành công',
                    'canceled' => 'Đã hủy',
                    default => 'Không rõ'
                };
                return "<span class='{$class} fs-6 py-2'>{$label}</span>";
            };
        @endphp

        <div class="card shadow-lg mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0 fw-bold">Thông Tin Chung & Trạng Thái</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <p class="mb-1"><strong>Mã Đơn Hàng:</strong> #{{ $order->id }}</p>
                        <p class="mb-1"><strong>Ngày Đặt:</strong> {{ $order->created_at->format('H:i:s d/m/Y') }}</p>
                        <p class="mb-1"><strong>Phương Thức TT:</strong> <span
                                class="text-primary">{{ $order->payment_method }}</span></p>
                        <p class="mb-1"><strong>Tổng Tiền:</strong> <span
                                class="text-danger fw-bolder fs-5">{{ number_format($order->total_amount) }} VNĐ</span></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p class="mb-1"><strong>Trạng Thái:</strong> {!! $statusBadge($order->status) !!}</p>
                        @if ($order->confirmed_at)
                            <p class="mb-1"><strong>Xác nhận lúc:</strong> <span
                                    class="text-info">{{ $order->confirmed_at->format('d/m/Y H:i') }}</span></p>
                        @endif
                        @if ($order->canceled_at)
                            <p class="mb-1"><strong>Hủy lúc:</strong> <span
                                    class="text-danger">{{ $order->canceled_at->format('d/m/Y H:i') }}</span></p>
                        @endif
                        @if ($order->admin_notes)
                            <p class="mb-1"><strong>Ghi chú (Admin):</strong> <span
                                    class="text-danger">{{ $order->admin_notes }}</span></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-lg mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0 fw-bold">Thông Tin Giao Hàng</h5>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>Người Nhận:</strong> {{ $order->customer_name }}</p>
                <p class="mb-1"><strong>Điện Thoại:</strong> {{ $order->customer_phone }}</p>
                <p class="mb-1"><strong>Địa Chỉ:</strong> {{ $order->customer_address }}</p>
                <p class="mb-1"><strong>Ghi Chú Cá Nhân:</strong> {{ $order->notes ?? 'Không có ghi chú.' }}</p>
            </div>
        </div>

        <div class="card shadow-lg">
            <div class="card-header bg-light">
                <h5 class="mb-0 fw-bold">Danh Sách Sản Phẩm</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-primary">
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Giá/SP</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->details as $detail)
                                <tr>
                                    <td>
                                        @if ($detail->product)
                                            <a href="{{ route('products.show', $detail->product->slug) }}" target="_blank"
                                                class="text-decoration-none">
                                                {{ $detail->product_name }}
                                            </a>
                                        @else
                                            {{ $detail->product_name }} <span class="badge bg-danger">Đã xóa</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $detail->quantity }}</td>
                                    <td class="text-end">{{ number_format($detail->price) }} VNĐ</td>
                                    <td class="text-end fw-bold">{{ number_format($detail->price * $detail->quantity) }} VNĐ
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end fs-5">TỔNG CỘNG:</th>
                                <th class="text-end text-danger fs-5">{{ number_format($order->total_amount) }} VNĐ</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection