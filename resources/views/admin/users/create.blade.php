@extends('layouts.admin.main') {{-- Thay thế bằng layout Admin của bạn --}}
@section('title', 'Tạo Người dùng Mới')

@section('admin_content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800"><i class="fas fa-user-plus"></i> Tạo Người dùng Mới</h1>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Thông tin User</h6>
            </div>
            <div class="card-body">
                {{-- Hiển thị lỗi validation --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.store') }}" novalidate>
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên</label>
                        <input type="text" class="form-control" id="name" name="name" 
                            value="{{ old('name') }}" placeholder="Nhập Tên..." required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                            value="{{ old('email') }}" placeholder="Nhập Email..." required>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Vai trò (Role)</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="{{ \App\Models\User::ROLE_USER }}" 
                                {{ old('role') == \App\Models\User::ROLE_USER ? 'selected' : '' }}>User (Người dùng thường)</option>
                            <option value="{{ \App\Models\User::ROLE_ADMIN }}" 
                                {{ old('role') == \App\Models\User::ROLE_ADMIN ? 'selected' : '' }}>Admin (Quản trị viên)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Nhập Mật khẩu..." required>
                    </div>

                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Xác nhận Mật khẩu</label>
                        <input type="password" class="form-control" id="confirmPassword" name="password_confirmation"
                            placeholder="Nhập lại Mật khẩu..." required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Tạo User</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Hủy</a>
                </form>
            </div>
        </div>
    </div>
@endsection