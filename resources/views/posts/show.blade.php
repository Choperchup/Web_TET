@extends('layouts.main')

@section('content')
    <div class="container py-5">
        <div class="row">

            {{-- Phần Nội dung Bài viết Chính (Col-lg-8) --}}
            <div class="col-lg-8">
                <article class="bg-white p-4 p-md-5 rounded shadow-sm">
                    {{-- Breadcrumb/Category --}}
                    <a href="#" class="text-primary fw-bold text-uppercase small">{{ $post->category->name ?? 'Chung' }}</a>

                    {{-- Tiêu đề --}}
                    <h1 class="display-6 fw-bold my-3">{{ $post->title }}</h1>

                    {{-- Thông tin Meta --}}
                    <div class="d-flex align-items-center text-muted mb-4 pb-3 border-bottom">
                        <i class="bi bi-person me-1"></i> {{ $post->author->name ?? 'Admin' }}
                        <span class="mx-2">|</span>
                        <i class="bi bi-calendar me-1"></i> Xuất bản:
                        {{ \Carbon\Carbon::parse($post->published_at)->format('d/m/Y') }}
                    </div>

                    {{-- Hình ảnh lớn (Thumbnail) --}}
                    @if ($post->thumbnail)
                        <figure class="mb-4">
                            <img src="{{ asset('storage/' . $post->thumbnail) }}" class="img-fluid rounded-3 w-100"
                                alt="{{ $post->title }}">
                        </figure>
                    @endif

                    {{-- Nội dung chi tiết --}}
                    <div class="post-content mt-5 article-body">
                        {{-- Sử dụng {!! !!} để render nội dung HTML từ CKEditor/TinyMCE --}}
                        {!! $post->content !!}
                    </div>

                </article>
            </div>

            {{-- Phần Sidebar và Bài viết Liên quan (Col-lg-4) --}}
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="sticky-top" style="top: 20px;">

                    {{-- Danh sách Bài viết Liên quan --}}
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-primary text-white fw-bold">
                            Bài Viết Liên Quan
                        </div>
                        <ul class="list-group list-group-flush">
                            @forelse ($relatedPosts as $relatedPost)
                                <li class="list-group-item">
                                    <a href="{{ route('posts.show', $relatedPost->slug) }}"
                                        class="text-dark text-decoration-none hover-text-primary fw-semibold">
                                        {{ $relatedPost->title }}
                                    </a>
                                    <p class="small text-muted mb-0">
                                        {{ \Carbon\Carbon::parse($relatedPost->published_at)->format('d/m/Y') }}
                                    </p>
                                </li>
                            @empty
                                <li class="list-group-item text-muted small">Không có bài viết liên quan nào.</li>
                            @endforelse
                        </ul>
                    </div>

                    {{-- Nút Quay lại --}}
                    <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-left"></i> Quay lại Blog
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* CSS tùy chỉnh cho nội dung bài viết */
        .article-body img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 15px 0;
        }

        .article-body h2,
        .article-body h3 {
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
    </style>
@endpush