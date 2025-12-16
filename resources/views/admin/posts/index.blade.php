@extends('layouts.admin.main')

@section('admin_content')
    <div class="card p-3 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Danh sách bài viết</h4>
            <div class="d-flex gap-2">
                <!-- Thêm nút Thùng Rác -->
                <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">+ Thêm bài viết</a>
                <a href="{{ route('admin.posts.trash') }}" class="btn btn-warning">
                    <i class="bi bi-trash"></i> Thùng Rác
                </a>
            </div>
        </div>

        <!-- Hiển thị thông báo (nếu có) -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Bộ lọc -->
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                    placeholder="Tìm theo tiêu đề, nội dung...">
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">-- Tất cả danh mục --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @if(request('category_id') == $cat->id) selected @endif>{{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">-- Trạng thái --</option>
                    <option value="draft" @if(request('status') == 'draft') selected @endif>Nháp</option>
                    <option value="published" @if(request('status') == 'published') selected @endif>Xuất bản</option>
                    <option value="archived" @if(request('status') == 'archived') selected @endif>Đã lưu trữ</option>
                    <!-- Loại bỏ 'trashed' khỏi bộ lọc nếu bạn muốn sử dụng trang trash riêng -->
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-secondary">Lọc</button>
                @if (request()->has('q') || request()->has('category_id') || request()->has('status'))
                    <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">Clear</a>
                @endif
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle bg-white">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Ảnh</th>
                        <th>Tiêu đề</th>
                        <th>Danh mục</th>
                        <th>Tác giả</th>
                        <th>Trạng thái</th>
                        <th>Ngày</th>
                        <th width="220">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $i => $post)
                        <tr>
                            <td>{{ $posts->firstItem() + $i }}</td>
                            <td>
                                @if($post->thumbnail)
                                    <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="thumb"
                                        style="width: 100px; height: auto;">
                                @else
                                    <div class="text-muted">No image</div>
                                @endif
                            </td>
                            <td>{{ $post->title }}</td>
                            <td>{{ $post->category->name ?? '-' }}</td>
                            <td>{{ $post->author->name ?? '-' }}</td>
                            <td>
                                <span
                                    class="badge {{ $post->status === 'published' ? 'bg-success' : ($post->status === 'draft' ? 'bg-secondary' : 'bg-warning') }}">
                                    {{ $post->status }}
                                </span>
                                <!-- Chỉ hiển thị trashed ở trang trash.blade.php, không cần ở đây -->
                            </td>
                            <td>{{ $post->published_at ? $post->published_at->format('Y-m-d') : $post->created_at->format('Y-m-d') }}
                            </td>
                            <td>
                                <!-- Hành động cho bài viết HOẠT ĐỘNG -->
                                @if(!$post->deleted_at)
                                    <a href="{{ route('admin.posts.edit', $post->id) }}"
                                        class="btn btn-sm btn-outline-primary">Sửa</a>

                                    <!-- Publish / Draft -->
                                    @if($post->status !== 'published')
                                        <form action="{{ route('admin.posts.publish', $post->id) }}" method="POST"
                                            style="display:inline">@csrf
                                            <button class="btn btn-sm btn-success">Xuất bản</button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.posts.draft', $post->id) }}" method="POST" style="display:inline">
                                            @csrf
                                            <button class="btn btn-sm btn-secondary">Lưu nháp</button>
                                        </form>
                                    @endif

                                    <!-- Xóa mềm (chuyển vào thùng rác) -->
                                    <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST"
                                        style="display:inline">
                                        @csrf @method('DELETE')
                                        <button onclick="return confirm('Bạn chắc chắn muốn chuyển bài viết này vào thùng rác?')"
                                            class="btn btn-sm btn-danger">Xóa</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Không có bài viết nào khớp với điều kiện lọc.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>Hiển thị {{ $posts->firstItem() ?? 0 }} - {{ $posts->lastItem() ?? 0 }} / {{ $posts->total() }}</div>
            <div>{{ $posts->links() }}</div>
        </div>
    </div>
@endsection