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

    {{-- THANH CÔNG CỤ LỌC NGANG (Giữ nguyên) --}}
    <div class="card shadow-sm p-3 mb-4">
        {{-- ... Form lọc giữ nguyên ... --}}
        <form action="{{ route('products.index') }}" method="GET" class="row g-3 align-items-end">
            
            {{-- Lọc theo Danh mục (3 cột) --}}
            <div class="col-md-3 col-sm-6">
                <label for="category_id" class="form-label small fw-semibold text-primary">
                    <i class="fas fa-list-alt me-1"></i> Danh mục
                </label>
                <select name="category_id" id="category_id" class="form-select form-select-sm">
                    <option value="">Tất cả danh mục</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" 
                            {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Lọc theo Giá Tối thiểu (2 cột) --}}
            <div class="col-md-2 col-sm-6">
                <label for="price_min" class="form-label small fw-semibold text-success">
                    Giá tối thiểu
                </label>
                <input type="number" name="price_min" id="price_min" class="form-control form-control-sm" 
                    placeholder="VD: 50000" value="{{ request('price_min') }}">
            </div>

            {{-- Lọc theo Giá Tối đa (2 cột) --}}
            <div class="col-md-2 col-sm-6">
                <label for="price_max" class="form-label small fw-semibold text-danger">
                    Giá tối đa
                </label>
                <input type="number" name="price_max" id="price_max" class="form-control form-control-sm" 
                    placeholder="VD: 500000" value="{{ request('price_max') }}">
            </div>
            
            {{-- Tìm kiếm theo Tên (3 cột) --}}
            <div class="col-md-3 col-sm-6">
                <label for="search" class="form-label small fw-semibold text-info">
                    <i class="fas fa-search me-1"></i> Tìm kiếm theo Tên
                </label>
                <input type="text" name="search" id="search" class="form-control form-control-sm" 
                    placeholder="Nhập tên sản phẩm..." value="{{ request('search') }}">
            </div>

            {{-- Nút Áp dụng và Xóa (2 cột) --}}
            <div class="col-md-2 col-sm-6 d-flex justify-content-end">
                <button type="submit" class="btn btn-dark btn-sm me-2 fw-bold w-100">
                    <i class="fas fa-search me-1"></i> Tìm
                </button>
                @if (request()->hasAny(['category_id', 'price_min', 'price_max', 'search']))
                    <a href="{{ route('products.index') }}" class="btn btn-outline-dark btn-sm w-100">
                        <i class="fas fa-times me-1"></i> Xóa Lọc
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Danh sách sản phẩm --}}
    <div class="row">
        @forelse ($products as $product)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card h-100 shadow-sm border-0 product-card">
                    {{-- Hình ảnh sản phẩm --}}
                    <a href="{{ route('products.show', $product->slug) }}" class="position-relative overflow-hidden d-block" style="height: 250px;">
                        @if ($product->thumbnail)
                            <img src="{{ asset('storage/' . $product->thumbnail) }}" class="card-img-top h-100 w-100 object-fit-cover" alt="{{ $product->name }}">
                        @else
                            <div class="h-100 w-100 bg-light d-flex align-items-center justify-content-center text-muted">No Image</div>
                        @endif
                        
                        {{-- Badge nổi bật/Sale --}}
                        @if ($product->is_featured)
                            <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-2">Nổi bật</span>
                        @endif
                        
                        {{-- ✅ ĐÃ SỬA: Thêm điều kiện $product->is_on_sale --}}
                        @if ($product->sale_price < $product->price && $product->sale_price > 0 && $product->is_on_sale)
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
                        <div class="mt-auto">
                                {{-- ✅ ĐÃ SỬA: Thêm điều kiện $product->is_on_sale --}}
                                @if ($product->sale_price > 0 && $product->sale_price < $product->price && $product->is_on_sale)
                                    <p class="mb-1 fw-bold text-danger h5">{{ number_format($product->sale_price, 0, ',', '.') }} VNĐ</p>
                                    <p class="mb-0 text-muted text-decoration-line-through small">{{ number_format($product->price, 0, ',', '.') }} VNĐ</p>
                                @else
                                    <p class="mb-1 fw-bold text-primary h5">{{ number_format($product->price, 0, ',', '.') }} VNĐ</p>
                                @endif
                            </div>

                        {{-- Tồn kho (Giữ nguyên) --}}
                        <p class="small text-muted mt-auto">
                            Trạng thái: <span class="badge {{ $product->stock > 0 ? 'bg-success' : 'bg-danger' }}">{{ $product->stock > 0 ? 'Còn hàng' : 'Hết hàng' }}</span>
                        </p>
                        
                        {{-- NÚT THÊM VÀO GIỎ HÀNG (Giữ nguyên) --}}
                        @if ($product->stock > 0)
                            <form action="{{ route('cart.add') }}" method="POST" class="mt-2">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1"> 
                            <button type="submit" class="btn btn-primary w-100 fw-bold">
                                <i class="bi bi-cart-plus me-1"></i> Thêm vào giỏ hàng
                            </button>
                            </form>
                        @else
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
                    Hiện tại chưa có sản phẩm nào phù hợp với bộ lọc.
                </div>
            </div>
        @endforelse
    </div>
    
    {{-- Phân trang (Giữ nguyên) --}}
    <div class="mt-4">
        {{ $products->appends(request()->query())->links() }} 
    </div>
</div>
@endsection


<style>
    /* 1. Hiệu ứng cho toàn bộ thẻ Card */
    .product-card {
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); /* Chuyển động mượt */
        border: 1px solid rgba(0,0,0,.05) !important;
        background-color: #fff;
    }

    .product-card:hover {
        transform: translateY(-10px); /* Nhấc lên cao hơn một chút */
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12) !important; /* Đổ bóng sâu */
    }

    /* 2. Hiệu ứng phóng to hình ảnh bên trong */
    .product-card .overflow-hidden {
        border-top-left-radius: calc(0.375rem - 1px);
        border-top-right-radius: calc(0.375rem - 1px);
    }

    .product-card img.card-img-top {
        transition: transform 0.6s ease; /* Ảnh phóng to chậm và mượt */
    }

    .product-card:hover img.card-img-top {
        transform: scale(1.1); /* Phóng to 10% */
    }

    /* 3. Hiệu ứng cho tên sản phẩm */
    .hover-text-primary {
        transition: color 0.3s ease;
    }

    .product-card:hover .hover-text-primary {
        color: #0d6efd !important; /* Đổi màu xanh khi rê vào card */
    }

    /* 4. Hiệu ứng nút bấm */
    .product-card .btn {
        transition: all 0.3s ease;
        border-radius: 6px;
    }

    .product-card:hover .btn-primary {
        transform: scale(1.03);
        box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2);
    }

    /* 5. Hiệu ứng Badge */
    .product-card .badge {
        z-index: 2; /* Đảm bảo badge luôn nằm trên ảnh khi phóng to */
        transition: transform 0.3s ease;
    }

    .product-card:hover .badge {
        transform: scale(1.1);
    }
</style>
