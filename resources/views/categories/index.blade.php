@extends('layouts.main')

@section('content')
    <h1 class="mb-4">Danh sách danh mục</h1>
    <div class="flex gap-2 mb-3">
        <a href="{{ route('categories.create') }}" class="btn btn-primary mb-3">Thêm danh mục</a>
        <a href="{{ route('categories.destroyAll') }}" class="btn btn-danger mb-3">Xóa tất cả</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <!--dữ liệu động-->
            @foreach ($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->created_at }}</td>
                    <td>
                        <a href="{{ route('categories.edit', ['id' => $category->id]) }}" class="btn btn-sm btn-warning">Sửa</a>
                        <a href="{{ route('categories.destroy', ['id' => $category->id]) }}"
                            class="btn btn-sm btn-danger">Xóa</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection