@extends('layouts.main')

@section('title', 'Giỏ Hàng Của Bạn')

@section('content')
    <div class="container py-5">
        <h1 class="fw-bold mb-4">Giỏ Hàng Của Bạn</h1>

        <div class="row">

            {{-- Cột 1: Danh sách sản phẩm --}}
            <div class="col-lg-8">
                {{-- Hiển thị thông báo (ví dụ: thông báo thành công từ Controller) --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr class="bg-light">
                                        <th scope="col" class="text-center">Sản phẩm</th>
                                        <th scope="col">Giá</th>
                                        <th scope="col" class="text-center">Số lượng</th>
                                        <th scope="col">Thành tiền</th>
                                        <th scope="col" class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($cartItems as $item)
                                        <tr>
                                            <td class="d-flex align-items-center">
                                                {{-- Ảnh sản phẩm --}}
                                                <div style="width: 80px; height: 80px; overflow: hidden; border-radius: 4px;"
                                                    class="me-3">
                                                    <a href="{{ route('products.show', $item['slug']) }}">
                                                        {{-- Lấy ảnh từ Session --}}
                                                        <img src="{{ asset('storage/' . $item['thumbnail']) }}"
                                                            alt="{{ $item['name'] }}" class="w-100 h-100 object-fit-cover">
                                                    </a>
                                                </div>
                                                <div class="fw-semibold">
                                                    <a href="{{ route('products.show', $item['slug']) }}"
                                                        class="text-dark text-decoration-none">
                                                        {{ $item['name'] }}
                                                    </a>
                                                </div>
                                            </td>
                                            <td>{{ number_format($item['price']) }} VNĐ</td>
                                            <td class="text-center" style="width: 150px;">
                                                {{-- Form cập nhật số lượng --}}
                                                <form action="{{ route('cart.update') }}" method="POST"
                                                    id="update-form-{{ $item['id'] }}" class="d-flex">
                                                    @csrf
                                                    @method('PUT')
                                                    {{-- ID ở đây là RowId trong Session --}}
                                                    <input type="hidden" name="id" value="{{ $item['id'] }}">
                                                    <input type="number" name="quantity"
                                                        class="form-control form-control-sm text-center"
                                                        value="{{ $item['quantity'] }}" min="1"
                                                        onchange="document.getElementById('update-form-{{ $item['id'] }}').submit()">
                                                </form>
                                            </td>
                                            {{-- Tính toán thành tiền trực tiếp --}}
                                            <td class="fw-bold text-danger">
                                                {{ number_format($item['price'] * $item['quantity']) }} VNĐ
                                            </td>
                                            <td class="text-center">
                                                {{-- Nút xóa --}}
                                                <form action="{{ route('cart.remove', $item['id']) }}" method="POST"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        title="Xóa sản phẩm">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                <i class="bi bi-bag-x me-2"></i> Giỏ hàng của bạn đang trống.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Tiếp tục mua sắm
                    </a>

                    @if ($cartItems->count())
                        <form action="{{ route('cart.clear') }}" method="POST"
                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa toàn bộ giỏ hàng?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-x-circle me-1"></i> Xóa toàn bộ giỏ hàng
                            </button>
                        </form>
                    @endif
                </div>

            </div>

            {{-- Cột 2: Tóm tắt đơn hàng --}}
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                    <div class="card-header bg-primary text-white fw-bold">
                        Tóm Tắt Đơn Hàng
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Tạm tính:</span>
                                <span class="fw-semibold">{{ number_format($total ?? 0) }} VNĐ</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Phí vận chuyển:</span>
                                <span class="text-success fw-semibold">Miễn phí</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between fw-bold fs-5 text-danger">
                                <span>Tổng cộng:</span>
                                <span>{{ number_format($total ?? 0) }} VNĐ</span>
                            </li>
                        </ul>

                        @if ($cartItems->count())
                            {{-- Thay đổi route này thành route thanh toán thực tế của bạn --}}
                            <a href="{{ route('checkout.index') ?? '#' }}" class="btn btn-success btn-lg w-100 mt-4">
                                Tiến hành Thanh toán <i class="bi bi-arrow-right"></i>
                            </a>
                        @else
                            <button class="btn btn-success btn-lg w-100 mt-4" disabled>
                                Giỏ hàng trống
                            </button>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection