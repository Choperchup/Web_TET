@extends('layouts.main')

@section('content')
    <div class="container py-5">
        {{-- Breadcrumb để người dùng biết họ đang ở đâu --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none">{{ $product->category->name ?? 'Sản phẩm' }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($product->name, 30) }}</li>
            </ol>
        </nav>

        <div class="row gx-lg-5 mb-5 align-items-start">
            {{-- Hình ảnh sản phẩm --}}
            <div class="col-lg-6 mb-4">
                <div class="product-gallery p-2 bg-white border rounded-4 shadow-sm">
                    @if ($product->thumbnail)
                        <img src="{{ asset('storage/' . $product->thumbnail) }}" 
                             class="img-fluid rounded-3 w-100 main-product-img"
                             alt="{{ $product->name }}">
                    @else
                        <div class="p-5 bg-light text-center rounded-3">
                            <i class="bi bi-image text-muted display-1"></i>
                            <p class="mt-2">Chưa có hình ảnh</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Thông tin chi tiết --}}
            <div class="col-lg-6">
                <div class="ps-lg-3">
                    <h1 class="fw-bold text-dark mb-2 display-6">{{ $product->name }}</h1>
                    
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge {{ $product->stock > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-3 py-2 rounded-pill">
                            {{ $product->stock > 0 ? '● Còn hàng' : '● Hết hàng' }}
                        </span>
                        <span class="ms-3 text-muted small">Mã sản phẩm: {{ $product->sku }}</span>
                    </div>

                    {{-- Khu vực Giá --}}
                    <div class="price-box p-4 bg-light rounded-4 mb-4">
                        @if ($product->sale_price > 0 && $product->sale_price < $product->price)
                            <div class="d-flex align-items-center">
                                <h2 class="text-danger fw-bold mb-0 me-3">{{ number_format($product->sale_price, 0, ',', '.') }} VNĐ</h2>
                                <del class="text-muted fs-5">{{ number_format($product->price, 0, ',', '.') }} VNĐ</del>
                            </div>
                        @else
                            <h2 class="text-primary fw-bold mb-0">{{ number_format($product->price, 0, ',', '.') }} VNĐ</h2>
                        @endif
                    </div>

                    {{-- Mô tả ngắn --}}
                    <div class="short-desc mb-4">
                        <h6 class="fw-bold text-uppercase small text-muted mb-2">Mô tả ngắn:</h6>
                        <p class="text-secondary leading-relaxed">{{ $product->short_description }}</p>
                    </div>

                    {{-- Nút mua hàng --}}
                    @if ($product->stock > 0)
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div class="row g-3">
                                <div class="col-md-4 col-4">
                                    <div class="input-group border rounded-3">
                                        <button class="btn btn-link text-dark text-decoration-none" type="button" onclick="this.parentNode.querySelector('input[type=number]').stepDown()">-</button>
                                        <input type="number" name="quantity" class="form-control border-0 text-center fw-bold" value="1" min="1">
                                        <button class="btn btn-link text-dark text-decoration-none" type="button" onclick="this.parentNode.querySelector('input[type=number]').stepUp()">+</button>
                                    </div>
                                </div>
                                <div class="col-md-8 col-8">
                                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm rounded-3">
                                        <i class="bi bi-cart-plus-fill me-2"></i> THÊM VÀO GIỎ
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tabs Nội dung --}}
        <div class="product-tabs mt-5">
            <ul class="nav nav-pills mb-4 justify-content-center" id="productTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active px-5 py-2 fw-bold" id="description-tab" data-bs-toggle="tab"
                            data-bs-target="#description" type="button" role="tab">Mô Tả Chi Tiết</button>
                </li>
            </ul>
            <div class="tab-content p-4 bg-white border rounded-4 shadow-sm" id="productTabContent">
                <div class="tab-pane fade show active" id="description" role="tabpanel">
                    <article class="product-rich-text">
                        {!! $product->content !!}
                    </article>
                </div>
            </div>
        </div>

        {{-- Sản phẩm liên quan --}}
        @if ($relatedProducts->count())
            <div class="mt-5 pt-5">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h3 class="fw-bold mb-0">Sản Phẩm Liên Quan</h3>
                    <div class="h-line flex-grow-1 ms-4 bg-light" style="height: 2px;"></div>
                </div>
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
                    @foreach ($relatedProducts as $relatedProduct)
                        <div class="col">
                            @include('partials.product-card', ['product' => $relatedProduct])
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <style>
        .main-product-img { transition: transform 0.3s ease; }
        .product-gallery:hover .main-product-img { transform: scale(1.02); }
        .nav-pills .nav-link.active { background-color: #0d6efd; box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3); }
        .nav-pills .nav-link { color: #444; background: #f8f9fa; margin: 0 5px; border-radius: 10px; }
        .product-rich-text img { max-width: 100%; height: auto; border-radius: 8px; }
        .product-rich-text { line-height: 1.8; color: #333; }
        .breadcrumb-item + .breadcrumb-item::before { content: "›"; font-size: 1.2rem; vertical-align: middle; }

        /* Ẩn mũi tên cho Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Ẩn mũi tên cho Firefox */
input[type=number] {
  -moz-appearance: textfield;
}

/* Làm đẹp lại khung số lượng */
.quantity-input {
    width: 50px !important;
    font-weight: bold;
    text-align: center;
    border: none;
}
    </style>
@endsection