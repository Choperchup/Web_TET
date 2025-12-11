@extends('layouts.main')

@section('content')
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="fw-bold text-center mb-3">Blog Mới Nhất</h1>
                <p class="lead text-center text-muted">Những thông tin hữu ích được cập nhật thường xuyên.</p>
            </div>
        </div>

        <div class="row">
            @forelse ($posts as $post)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0 transition-3d hover-shadow-lg">
                        {{-- Hình ảnh thumbnail --}}
                        <a href="{{ route('posts.show', $post->slug) }}" class="position-relative overflow-hidden"
                            style="display: block; height: 200px;">
                            @if ($post->thumbnail)
                                <img src="{{ asset('storage/' . $post->thumbnail) }}"
                                    class="card-img-top h-100 w-100 object-fit-cover" alt="{{ $post->title }}">
                            @else
                                <div class="h-100 w-100 bg-light d-flex align-items-center justify-content-center text-muted">Không
                                    có ảnh</div>
                            @endif
                            {{-- Hiển thị featured (nếu có) --}}
                            @if ($post->featured)
                                <span class="badge bg-danger position-absolute top-0 start-0 m-2">Nổi bật</span>
                            @endif
                        </a>

                        <div class="card-body d-flex flex-column">
                            {{-- Category --}}
                            <a href="#" class="text-primary small fw-semibold mb-1">{{ $post->category->name ?? 'Chung' }}</a>

                            {{-- Tiêu đề --}}
                            <h5 class="card-title fw-bold">
                                <a href="{{ route('posts.show', $post->slug) }}"
                                    class="text-dark text-decoration-none hover-text-primary">
                                    {{ $post->title }}
                                </a>
                            </h5>

                            {{-- Mô tả ngắn (lấy từ nội dung nếu không có trường short_description) --}}
                            <p class="card-text text-muted flex-grow-1">
                                {{ Str::limit(strip_tags($post->content), 100) }}
                            </p>

                            {{-- Meta Footer --}}
                            <div class="d-flex align-items-center small text-muted border-top pt-2 mt-auto">
                                <i class="bi bi-person me-1"></i> {{ $post->author->name ?? 'Admin' }}
                                <span class="mx-2">|</span>
                                <i class="bi bi-clock me-1"></i>
                                {{ \Carbon\Carbon::parse($post->published_at)->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="alert alert-info" role="alert">
                        Chưa có bài viết nào được xuất bản.
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Phân trang --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $posts->links() }}
        </div>
    </div>
@endsection