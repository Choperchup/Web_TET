<div class="col">
    <div class="card h-100 shadow-sm border-0 product-card">
        <div class="product-image-container">
            <a href="{{ route('products.show', $product->slug) }}" class="d-block w-100 h-100 position-relative">
                <img src="{{ $product->thumbnail ? asset('storage/' . $product->thumbnail) : 'https://via.placeholder.com/400x300' }}"
                    class="card-img-top" alt="{{ $product->name }}">

                @if ($product->is_featured)
                    <span class="badge bg-danger position-absolute top-0 start-0 m-2">Nổi bật</span>
                @endif

                @if ($product->is_on_sale)
                    <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2">SALE</span>
                @endif
            </a>
        </div>

        <div class="card-body d-flex flex-column">
            <h5 class="card-title mt-1">
                <a href="{{ route('products.show', $product->slug) }}" class="text-dark text-decoration-none">
                    {{ Str::limit($product->name, 40) }}
                </a>
            </h5>

            <div class="mt-auto">
                @if ($product->is_on_sale)
                    <p class="mb-1 fw-bold text-danger h5">{{ number_format($product->sale_price, 0, ',', '.') }} VNĐ</p>
                    <p class="mb-0 text-muted text-decoration-line-through small">
                        {{ number_format($product->price, 0, ',', '.') }} VNĐ
                    </p>
                @else
                    <p class="mb-1 fw-bold text-primary h5">{{ number_format($product->price, 0, ',', '.') }} VNĐ</p>
                @endif
            </div>
        </div>

        <div class="card-footer bg-transparent border-0 pt-0 pb-3 text-center">
            <form action="{{ route('cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-primary w-100 fw-bold">
                    <i class="bi bi-cart-plus me-1"></i> Thêm vào giỏ hàng
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    /* Hiệu ứng tổng thể cho Card */
    .product-card {
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, .05) !important;
    }

    .product-card:hover {
        transform: translateY(-8px);
        /* Nhấc card lên 8px */
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15) !important;
        /* Đổ bóng đậm hơn */
    }

    /* Hiệu ứng phóng to hình ảnh */
    .product-image-container {
        overflow: hidden;
        /* Cắt phần ảnh thừa khi phóng to */
        position: relative;
    }

    .product-image-container img {
        transition: transform 0.5s ease;
    }

    .product-card:hover .product-image-container img {
        transform: scale(1.1);
        /* Phóng to ảnh 10% */
    }

    /* Hiệu ứng nút bấm Thêm vào giỏ hàng */
    .product-card .btn-warning {
        transition: all 0.3s ease;
        border-radius: 8px;
        opacity: 0.9;
    }

    .product-card:hover .btn-warning {
        opacity: 1;
        background-color: #ffc107;
        transform: scale(1.02);
        /* Nút bấm to lên một chút */
    }

    /* Làm mờ nhẹ badge khi hover để tập trung vào sản phẩm */
    .product-card:hover .badge {
        filter: brightness(1.1);
    }
</style>