@extends('layouts.main')

@section('content')
<div class="container py-5">
    <h1 class="fw-bold mb-4">Thanh Toán Đơn Hàng</h1>
    
    {{-- Hiển thị thông báo lỗi/thành công nếu có --}}
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($cartItems->isEmpty())
        <div class="alert alert-info text-center">
            Giỏ hàng của bạn đang trống. Vui lòng quay lại <a href="{{ route('products.index') }}">trang sản phẩm</a>.
        </div>
    @else
        <div class="row">
            {{-- Cột 1: Thông tin Khách hàng và Vận chuyển --}}
            <div class="col-lg-7">
                <div class="card shadow-sm mb-4">
                    <div class="card-header fw-bold bg-primary text-white">1. Thông Tin Nhận Hàng</div>
                    <div class="card-body">
                        
                        {{-- Form POST để gửi thông tin đơn hàng --}}
                        <form action="{{ route('checkout.placeOrder') }}" method="POST">
                            @csrf
                            
                            {{-- Bạn có thể ẩn các trường này nếu người dùng đã đăng nhập --}}
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Họ và Tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required value="{{ old('name', Auth::check() ? Auth::user()->name : '') }}">
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Số Điện Thoại <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" required value="{{ old('phone') }}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">Địa Chỉ Nhận Hàng <span class="text-danger">*</span></label>
                                {{-- Nên dùng Select2 cho Tỉnh/Thành, Quận/Huyện, Xã/Phường ở đây --}}
                                <textarea class="form-control" id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="notes" class="form-label">Ghi Chú Đơn Hàng (Không bắt buộc)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                            </div>

                            {{-- Phương thức Thanh toán (Thường là radio button) --}}
                            <div class="mt-4 pt-3 border-top">
                                <h5 class="fw-bold mb-3">2. Phương Thức Thanh Toán</h5>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="cod" value="COD" checked>
                                    <label class="form-check-label" for="cod">
                                        Thanh toán khi nhận hàng (COD)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="bank" value="BankTransfer">
                                    <label class="form-check-label" for="bank">
                                        Chuyển khoản Ngân hàng
                                    </label>
                                </div>
                            </div>

                            {{-- Nút Đặt hàng --}}
                            <button type="submit" class="btn btn-success btn-lg w-100 mt-4">
                                HOÀN TẤT ĐẶT HÀNG ({{ number_format($total) }} VNĐ)
                            </button>

                        </form>
                        
                    </div>
                </div>
            </div>

            {{-- Cột 2: Tóm tắt Đơn hàng --}}
            <div class="col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-header fw-bold bg-secondary text-white">Tóm Tắt Đơn Hàng</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush mb-3">
                            @foreach ($cartItems as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="fw-semibold">{{ $item['name'] }}</span>
                                        <small class="d-block text-muted">SL: {{ $item['quantity'] }} x {{ number_format($item['price']) }} VNĐ</small>
                                    </div>
                                    <span class="fw-bold">{{ number_format($item['price'] * $item['quantity']) }} VNĐ</span>
                                </li>
                            @endforeach
                        </ul>

                        <div class="d-flex justify-content-between fw-bold text-success border-top pt-3">
                            <span>Tổng Cộng:</span>
                            <span>{{ number_format($total) }} VNĐ</span>
                        </div>
                        
                        {{-- Có thể thêm phí vận chuyển, giảm giá ở đây --}}
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection