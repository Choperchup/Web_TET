@extends('layouts.main')

@section('content')
    <div class="container py-5">

        {{-- PHẦN THÔNG TIN CƠ BẢN --}}
        <div class="row gx-lg-5 mb-5">

            {{-- Hình ảnh --}}
            <div class="col-lg-6">
                <div class="product-image-large p-3 border rounded shadow-sm">
                    @if ($product->thumbnail)
                        <img src="{{ asset('storage/' . $product->thumbnail) }}" class="img-fluid rounded w-100"
                            alt="{{ $product->name }}">
                    @else
                        <div class="p-5 bg-light text-center">No Image</div>
                    @endif
                </div>
            </div>

            {{-- Chi tiết --}}
            <div class="col-lg-6 mt-4 mt-lg-0">
                <h1 class="display-5 fw-bold mb-3">{{ $product->name }}</h1>

                {{-- Giá --}}
                <div class="my-4 border-bottom pb-3">
                    @if ($product->sale_price > 0 && $product->sale_price < $product->price)
                        <span class="text-danger fw-bold fs-2 me-3">{{ number_format($product->sale_price) }} VNĐ</span>
                        <del class="text-muted fs-4">{{ number_format($product->price) }} VNĐ</del>
                    @else
                        <span class="text-primary fw-bold fs-2">{{ number_format($product->price) }} VNĐ</span>
                    @endif
                </div>

                {{-- Mô tả ngắn --}}
                <p class="lead text-muted">{{ $product->short_description }}</p>

                {{-- Thông tin khác --}}
                <ul class="list-unstyled mb-4">
                    <li>**Danh mục:** <span class="fw-semibold">{{ $product->category->name ?? 'Chưa phân loại' }}</span>
                    </li>
                    <li>**SKU:** {{ $product->sku }}</li>
                    <li>**Trạng thái:** <span
                            class="badge {{ $product->stock > 0 ? 'bg-success' : 'bg-danger' }}">{{ $product->stock > 0 ? 'Còn hàng' : 'Hết hàng' }}</span>
                    </li>
                </ul>

                {{-- Nút mua hàng/Thêm vào giỏ (BẰNG FORM POST) --}}
                @if ($product->stock > 0)
                    <form action="{{ route('cart.add') }}" method="POST" class="mt-3">
                    @csrf
                        {{-- Truyền ID sản phẩm và số lượng mặc định 1 --}}
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1"> 
        
                        {{-- Nút lớn (btn-lg) --}}
                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold">
                            <i class="bi bi-cart-plus me-1"></i> Thêm vào Giỏ hàng
                        </button>
                    </form>
                @else
                    {{-- Nếu hết hàng, hiển thị nút Hết hàng disabled --}}
                    <button class="btn btn-secondary btn-lg w-100 py-3 mt-3" type="button" disabled>
                        <i class="bi bi-x-circle me-1"></i> Hết hàng
                    </button>
                @endif
            </div>
        </div>

        {{-- PHẦN NỘI DUNG CHI TIẾT VÀ BÀI VIẾT LIÊN QUAN --}}
        <div class="row">
            <div class="col-12">
                <ul class="nav nav-tabs" id="productTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="description-tab" data-bs-toggle="tab"
                            data-bs-target="#description" type="button" role="tab" aria-controls="description"
                            aria-selected="true">Mô Tả Chi Tiết</button>
                    </li>
                </ul>
                <div class="tab-content border border-top-0 p-4 bg-white rounded-bottom" id="productTabContent">
                    {{-- Tab Mô Tả Chi Tiết --}}
                    <div class="tab-pane fade show active" id="description" role="tabpanel"
                        aria-labelledby="description-tab">
                        <div class="product-content">
                            {!! $product->content !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SẢN PHẨM LIÊN QUAN --}}
        @if ($relatedProducts->count())
            <div class="row mt-5">
                <div class="col-12">
                    <h2 class="fw-bold mb-4">Sản Phẩm Liên Quan</h2>
                    <div class="row">
                        @foreach ($relatedProducts as $relatedProduct)
                            {{-- Dùng lại cấu trúc card nhỏ gọn --}}
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                <div class="card h-100 shadow-sm border-0">
                                    {{-- ✨ THÊM ẢNH SẢN PHẨM LIÊN QUAN ✨ --}}
                                    <a href="{{ route('products.show', $relatedProduct->slug) }}" class="d-block overflow-hidden" style="height: 150px;">
                                        @if ($relatedProduct->thumbnail)
                                            <img src="{{ asset('storage/' . $relatedProduct->thumbnail) }}" class="card-img-top h-100 w-100 object-fit-cover" alt="{{ $relatedProduct->name }}">
                                        @else
                                            <div class="h-100 w-100 bg-light d-flex align-items-center justify-content-center text-muted small">No Image</div>
                                        @endif
                                    </a>
                                    {{-- KẾT THÚC THÊM ẢNH --}}

                                    <a href="{{ route('products.show', $relatedProduct->slug) }}">
                                    </a>
                                    <div class="card-body text-center">
                                        <h6 class="card-title fw-semibold">
                                            <a href="{{ route('products.show', $relatedProduct->slug) }}"
                                                class="text-dark text-decoration-none">{{ $relatedProduct->name }}</a>
                                        </h6>
                                        <p class="text-primary fw-bold mt-2 mb-0">{{ number_format($relatedProduct->price) }} VNĐ
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection