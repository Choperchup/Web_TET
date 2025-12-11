@extends('layouts.main')

@section('content')
<div class="container py-5">
    <h1 class="fw-bold text-center mb-5">Khám Phá Các Sản Phẩm Của Chúng Tôi</h1>
    
    {{-- Hiển thị thông báo (nếu có từ CartController) --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        @forelse ($products as $product)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card h-100 shadow-sm border-0 product-card">
                    {{-- Hình ảnh sản phẩm --}}
                    <a href="{{ route('products.show', $product->slug) }}" class="position-relative overflow-hidden" style="display: block; height: 250px;">
                        @if ($product->thumbnail)
                            <img src="{{ asset('storage/' . $product->thumbnail) }}" class="card-img-top h-100 w-100 object-fit-cover" alt="{{ $product->name }}">
                        @else
                            <div class="h-100 w-100 bg-light d-flex align-items-center justify-content-center text-muted">No Image</div>
                        @endif
                        
                        {{-- Badge nổi bật/Sale --}}
                        @if ($product->is_featured)
                            <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-2">Nổi bật</span>
                        @endif
                        @if ($product->sale_price < $product->price && $product->sale_price > 0)
                            <span class="badge bg-danger position-absolute top-0 end-0 m-2">Sale</span>
                        @endif
                    </a>
                    
                    <div class="card-body d-flex flex-column">
                        {{-- Tên sản phẩm --}}
                        <h5 class="card-title fw-semibold">
                            <a href="{{ route('products.show', $product->slug) }}" class="text-dark text-decoration-none hover-text-primary">
                                {{ $product->name }}
                            </a>
                        </h5>

                        {{-- Giá --}}
                        <div class="mt-2 mb-3">
                            @if ($product->sale_price > 0 && $product->sale_price < $product->price)
                                <span class="text-danger fw-bold fs-5 me-2">{{ number_format($product->sale_price) }} VNĐ</span>
                                <del class="text-muted small">{{ number_format($product->price) }} VNĐ</del>
                            @else
                                <span class="text-primary fw-bold fs-5">{{ number_format($product->price) }} VNĐ</span>
                            @endif
                        </div>

                        {{-- Tồn kho --}}
                        <p class="small text-muted mt-auto">
                            Tồn kho: <span class="badge {{ $product->stock > 0 ? 'bg-success' : 'bg-danger' }}">{{ $product->stock > 0 ? 'Còn hàng' : 'Hết hàng' }}</span>
                        </p>
                        
                        {{-- ✨ THÊM NÚT THÊM VÀO GIỎ HÀNG (ĐÃ ĐỒNG NHẤT) ✨ --}}
                        @if ($product->stock > 0)
                            <form action="{{ route('cart.add') }}" method="POST" class="mt-2">
                            @csrf
                            {{-- Truyền ID sản phẩm và số lượng mặc định 1 --}}
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1"> 
        
                            <button type="submit" class="btn btn-primary w-100 fw-bold">
                                <i class="bi bi-cart-plus me-1"></i> Thêm vào Giỏ
                            </button>
                            </form>
                            @else
                            {{-- Sử dụng BUTTON disabled cho đồng nhất và rõ ràng hơn --}}
                            <button class="btn btn-secondary w-100 fw-bold mt-2" type="button" disabled>
                                <i class="bi bi-x-circle me-1"></i> Hết hàng
                            </button>
                            @endif
                        
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="alert alert-info" role="alert">
                    Hiện tại chưa có sản phẩm nào được công khai.
                </div>
            </div>
        @endforelse
    </div>
    
    {{-- Phân trang --}}
    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>
@endsection