@extends('layouts.main')

@section('content')
    <h1>Danh sách bài viết</h1>
    <a href="{{ route('posts.create') }}" class="btn btn-primary mb-3">Thêm bài viết</a>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Thumbnail</th>
                <th>Tiêu đề</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <!--dữ liệu động-->
            @foreach ($posts as $post)
                <tr>
                    <td>{{ $post->id }}</td>
                    <td>
                        <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->title }}"
                            style="width: 100px; height: auto;">
                    </td>
                    <td>{{ $post->title }}</td>
                    <td>{{ $post->created_at }}</td>
                    <td>
                        <a href="{{ route('posts.edit', ['id' => $post->id]) }}" class="btn btn-sm btn-warning">Sửa</a>
                        <a href="{{ route('posts.destroy', ['id' => $post->id]) }}" class="btn btn-sm btn-danger">Xóa</a>
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>
@endsection