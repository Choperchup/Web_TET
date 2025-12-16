@extends('layouts.admin.main') {{-- Thay thế bằng layout Admin của bạn --}}
@section('title', 'Chỉnh sửa Người dùng')

@section('admin_content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800"><i class="fas fa-edit"></i> Chỉnh sửa Người dùng: {{ $user->name }}</h1>

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

                {{-- Dùng PUT method để UPDATE --}}
                <form method="POST" action="{{ route('admin.users.update', $user->id) }}" novalidate>
                    @csrf
                    @method('PUT') 
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ old('name', $user->name) }}" placeholder="Nhập Tên..." required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="{{ old('email', $user->email) }}" placeholder="Nhập Email..." required>
                    </div>

                    {{-- Trường Role để Admin thay đổi vai trò --}}
                    <div class="mb-3">
                        <label for="role" class="form-label">Vai trò (Role)</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="{{ \App\Models\User::ROLE_USER }}"
                                {{ old('role', $user->role) == \App\Models\User::ROLE_USER ? 'selected' : '' }}>User (Người dùng thường)</option>
                            <option value="{{ \App\Models\User::ROLE_ADMIN }}"
                                {{ old('role', $user->role) == \App\Models\User::ROLE_ADMIN ? 'selected' : '' }}>Admin (Quản trị viên)</option>
                        </select>
                    </div>
                    
                    {{-- Thường Admin có form riêng để reset password, không nên hiển thị ở đây --}}
                    
                    <button type="submit" class="btn btn-primary">Cập nhật User</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Hủy</a>
                </form>
            </div>
        </div>
    </div>
@endsection