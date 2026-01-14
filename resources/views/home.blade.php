@extends('layouts.main')

@section('content')

{{-- PHẦN 1: SẢN PHẨM NỔI BẬT --}}
<div class="container my-5">
    <h2 class="fw-bold text-center text-primary mb-4">⭐ SẢN PHẨM NỔI BẬT ⭐</h2>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
        @foreach ($featuredProducts as $product)
            @include('partials.product-card', ['product' => $product])
        @endforeach
    </div>
</div>

{{-- PHẦN 2: SẢN PHẨM GIẢM GIÁ --}}
<div class="container my-5">
    <h2 class="fw-bold text-center text-danger mb-4">🔥 KHUYẾN MÃI CỰC SỐC 🔥</h2>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
        @foreach ($saleProducts as $product)
            @include('partials.product-card', ['product' => $product])
        @endforeach
    </div>
</div>

<hr class="container my-5">

{{-- PHẦN 3: BÀI VIẾT MỚI NHẤT --}}
<div class="container mb-5">
    <h3 class="fw-bold mb-4"><i class="fas fa-newspaper me-2"></i>Tin tức mới nhất</h3>
    <div class="row g-4">
        @forelse($latestPosts as $post)
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    @if($post->thumbnail)
                        <img src="{{ asset('storage/' . $post->thumbnail) }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="{{ $post->title }}">
                    @else
                        <img src="https://via.placeholder.com/400x250?text=News" class="card-img-top" style="height: 200px; object-fit: cover;">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title fw-bold">
                            <a href="{{ route('posts.show', $post->slug) }}" class="text-dark text-decoration-none">{{ Str::limit($post->title, 60) }}</a>
                        </h5>
                        <p class="card-text text-muted small">{{ Str::limit(strip_tags($post->content), 100) }}</p>
                    </div>
                    <div class="card-footer bg-white border-0 pb-3">
                        <small class="text-muted"><i class="far fa-calendar-alt me-1"></i> {{ $post->created_at->format('d/m/Y') }}</small>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center text-muted">Chưa có bài viết nào.</p>
        @endforelse
    </div>
</div>

@endsection