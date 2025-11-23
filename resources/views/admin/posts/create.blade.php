@extends('layouts.admin.main')

@section('admin_content')
    <div class="card p-4">
        <h1>Tạo bài viết mới</h1>
        <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label">Tiêu đề</label>
                <input name="title" value="{{ old('title') }}" class="form-control" required>
            </div>
            <div class="mb-3 row">
                <div class="col-md-6">
                    <label class="form-label">Danh mục</label>
                    <select name="category_id" class="form-select" required>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Thumbnail (ảnh)</label>
                    <input type="file" name="thumbnail" class="form-control">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Nội dung</label>
                <textarea id="content" name="content" class="form-control" rows="10">{{ old('content') }}</textarea>
            </div>

            <div class="mb-3 row">
                <div class="col-md-4">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="draft">Nháp</option>
                        <option value="published">Xuất bản</option>
                        <option value="archived">Lưu trữ</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Published at (tùy chọn)</label>
                    <input type="datetime-local" name="published_at" class="form-control">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="featured" id="featured">
                        <label class="form-check-label" for="featured">Bài nổi bật</label>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Meta title</label>
                <input name="meta_title" class="form-control" value="{{ old('meta_title') }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Meta description</label>
                <textarea name="meta_description" class="form-control" rows="2">{{ old('meta_description') }}</textarea>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary">Lưu</button>
                <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">Hủy</a>
            </div>
        </form>
    </div>
@endsection

@push('script')
    <!-- CKEditor CDN -->
    <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('content', {
            filebrowserUploadUrl: "{{ route('admin.posts.upload') }}",
            filebrowserUploadMethod: 'form'
        });
    </script>
@endpush