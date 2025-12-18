@extends('layouts.admin.main') {{-- Thay thế bằng layout Admin của bạn --}}
@section('title', 'Thùng rác Người dùng')

@section('admin_content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800"><i class="fas fa-trash-restore"></i> Thùng rác Người dùng</h1>

        {{-- Hiển thị thông báo (nếu có) --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-warning">Danh sách User đã xóa mềm</h6>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
            <div class="card-body">
                @if ($users->isEmpty())
                    <div class="alert alert-info">
                        Thùng rác trống. Không có người dùng nào bị xóa mềm.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Tên</th>
                                    <th>Email</th>
                                    <th>Ngày xóa</th>
                                    <th style="width: 180px;">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->deleted_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            {{-- Form KHÔI PHỤC --}}
                                            <form action="{{ route('admin.users.restore', $user->id) }}" method="POST"
                                                style="display: inline-block;">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-success btn-sm me-1" title="Khôi phục">
                                                    <i class="fas fa-redo">Khôi phục</i>
                                                </button>
                                            </form>

                                            {{-- Form XÓA VĨNH VIỄN --}}
                                            <form action="{{ route('admin.users.forceDelete', $user->id) }}" method="POST"
                                                style="display: inline-block;"
                                                onsubmit="return confirm('CẢNH BÁO: Bạn có chắc chắn muốn XÓA VĨNH VIỄN người dùng {{ $user->name }} không? Thao tác này không thể hoàn tác.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Xóa Vĩnh viễn">
                                                    <i class="fas fa-minus-circle">Xóa vĩnh viễn</i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- @if (method_exists($users, 'links'))
                    <div class="mt-3">
                        {{ $users->links('pagination::bootstrap-5') }}
                    </div>
                    @endif --}}
                @endif
            </div>
        </div>
    </div>
@endsection