@extends('layouts.main')

@section('content')
    <div class="container py-5">
        <h1 class="fw-bold text-center mb-5 text-primary">Lịch Sử Đơn Hàng Của Tôi</h1>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($orders->isEmpty())
                    <div class="alert alert-info text-center">
                        Bạn chưa có đơn hàng nào. Hãy bắt đầu mua sắm ngay!
                        <a href="{{ route('products.index') }}" class="alert-link">Xem sản phẩm</a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Mã ĐH</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
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
                                            return "<span class='{$class}'>{$label}</span>";
                                        };
                                    @endphp

                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="fw-bold text-danger">{{ number_format($order->total_amount) }} VNĐ</td>
                                        <td>{!! $statusBadge($order->status) !!}</td>
                                        <td>
                                            <a href="{{ route('users.orders.show', $order) }}"
                                                class="btn btn-sm btn-info text-white">
                                                <i class="bi bi-eye"></i> Xem chi tiết
                                            </a>
                                            {{-- Nút Hủy (chỉ hiển thị nếu trạng thái là pending) --}}
                                            @if ($order->status == 'pending')
                                                <button type="button" class="btn btn-sm btn-danger" disabled>Hủy</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Phân trang --}}
                    <div class="d-flex justify-content-center mt-4">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection