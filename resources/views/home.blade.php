@extends('layouts.main') 
{{-- Giả định bạn có layout chung cho Frontend --}}

@section('title', 'Trang Chủ - Cửa hàng E-commerce')

@section('content')

<div class="container my-5">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2 class="fw-bold text-primary">⭐ SẢN PHẨM NỔI BẬT ⭐</h2>
            <p class="lead text-muted">Những sản phẩm được yêu thích và đánh giá cao nhất.</p>
        </div>
    </div>

    @if ($featuredProducts->isNotEmpty())
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            
            @foreach ($featuredProducts as $product)
                {{-- Card sản phẩm --}}
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 product-card">
                        
                        {{-- Ảnh sản phẩm --}}
                        <div class="product-image-container">
                            <a href="{{ route('products.show', $product->slug) }}" class="d-block w-100 h-100 position-relative">
                                @if ($product->thumbnail)
                                    <img src="{{ asset('storage/' . $product->thumbnail) }}" class="card-img-top" alt="{{ $product->name }}" loading="lazy">
                                @else
                                    <img src="https://via.placeholder.com/400x300?text=No+Image" class="card-img-top" alt="No Image">
                                @endif
                                
                                {{-- ✅ ĐÃ SỬA: Bổ sung các Badge đồng bộ --}}
                                
                                {{-- Badge nổi bật (Giữ nguyên) --}}
                                @if ($product->is_featured)
                                    <span class="badge bg-danger position-absolute top-0 start-0 m-2">Nổi bật</span>
                                @endif

                                {{-- ✅ ĐÃ THÊM: Badge Sale chỉ hiện khi is_on_sale là TRUE --}}
                                @if ($product->sale_price < $product->price && $product->sale_price > 0 && $product->is_on_sale)
                                    <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2">SALE</span>
                                @endif
                            </a>
                        </div>

                        <div class="card-body d-flex flex-column">
                            
                            {{-- Tên sản phẩm --}}
                            <h5 class="card-title mt-1">
                                <a href="{{ route('products.show', $product->slug) }}" class="text-dark text-decoration-none hover-text-primary">
                                    {{ Str::limit($product->name, 50) }}
                                </a>
                            </h5>
                            
                            {{-- Giá sản phẩm --}}
                            <div class="mt-auto">
                                {{-- ✅ ĐÃ SỬA: Thêm điều kiện $product->is_on_sale --}}
                                @if ($product->sale_price > 0 && $product->sale_price < $product->price && $product->is_on_sale)
                                    <p class="mb-1 fw-bold text-danger h5">{{ number_format($product->sale_price, 0, ',', '.') }} VNĐ</p>
                                    <p class="mb-0 text-muted text-decoration-line-through small">{{ number_format($product->price, 0, ',', '.') }} VNĐ</p>
                                @else
                                    <p class="mb-1 fw-bold text-primary h5">{{ number_format($product->price, 0, ',', '.') }} VNĐ</p>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Footer Card (Action Buttons) --}}
                        <div class="card-footer bg-transparent border-0 pt-0 pb-3 text-center">
                            {{-- Form Thêm vào giỏ hàng --}}
                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1"> 
                                <button type="submit" class="btn btn-warning btn-sm w-100 fw-bold">
                                    <i class="fas fa-shopping-cart me-2"></i> Thêm vào giỏ hàng
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
            
        </div>
    @else
        <div class="alert alert-info text-center">
            Hiện chưa có sản phẩm nổi bật nào được Admin đánh dấu.
        </div>
    @endif
</div>

{{-- Thêm thư viện Font Awesome nếu cần icon --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    /* Custom CSS để định dạng card sản phẩm đẹp hơn */
    .product-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .product-image-container {
        position: relative;
        overflow: hidden;
        height: 300px; /* Chiều cao cố định cho ảnh */
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .product-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Đảm bảo ảnh vừa khung mà không bị méo */
    }
</style>
@endsection