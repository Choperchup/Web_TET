@extends('layouts.admin.main')

@section('admin_content')
    <div class="card p-3 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Thùng Rác Bài Viết</h4>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>

        <!-- Hiển thị thông báo (nếu có) -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table align-middle bg-white">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Ảnh</th>
                        <th>Tiêu đề</th>
                        <th>Danh mục</th>
                        <th>Tác giả</th>
                        <th>Thời gian xóa</th>
                        <th width="200">Hành động</th>
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
                            <!-- Hiển thị thời gian xóa -->
                            <td class="text-danger">
                                {{ $post->deleted_at->format('Y-m-d H:i') }}
                            </td>
                            <td>
                                <!-- Khôi phục -->
                                <form action="{{ route('admin.posts.restore', $post->id) }}" method="POST"
                                    style="display:inline">
                                    @csrf
                                    @method('PUT')
                                    <button class="btn btn-sm btn-success">Khôi phục</button>
                                </form>

                                <!-- Xóa vĩnh viễn -->
                                <form action="{{ route('admin.posts.forceDelete', $post->id) }}" method="POST"
                                    style="display:inline">
                                    @csrf @method('DELETE')
                                    <button onclick="return confirm('CẢNH BÁO: Bạn chắc chắn muốn XÓA VĨNH VIỄN bài viết này?')"
                                        class="btn btn-sm btn-dark">Xóa vĩnh viễn</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Thùng rác rỗng. Không có bài viết nào bị xóa mềm.</td>
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