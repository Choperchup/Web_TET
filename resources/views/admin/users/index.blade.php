@extends('layouts.admin.main') {{-- Thay thế bằng layout Admin của bạn --}}
@section('title', 'Quản lý Người dùng')

@section('admin_content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800"><i class="fas fa-users"></i> Quản lý Người dùng</h1>

        {{-- Hiển thị thông báo (nếu có) --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Danh sách Người dùng</h6>
                <div>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm me-2">
                        <i class="fas fa-plus"></i> Tạo User Mới
                    </a>
                    <a href="{{ route('admin.users.trash') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-trash"></i> Thùng rác
                    </a>
                </div>
            </div>

            <div class="card-body">

                {{-- NAV TABS để chuyển đổi giữa 2 danh sách --}}
                <ul class="nav nav-tabs mb-3" id="userTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="regular-users-tab" data-bs-toggle="tab"
                            data-bs-target="#regular-users-pane" type="button" role="tab" aria-controls="regular-users-pane"
                            aria-selected="true">
                            Users Thường ({{ $regularUsers->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="admin-users-tab" data-bs-toggle="tab"
                            data-bs-target="#admin-users-pane" type="button" role="tab" aria-controls="admin-users-pane"
                            aria-selected="false">
                            Admin ({{ $adminUsers->count() }})
                        </button>
                    </li>
                </ul>

                {{-- TAB CONTENT --}}
                <div class="tab-content" id="userTabsContent">

                    {{-- TAB 1: DANH SÁCH USERS THƯỜNG --}}
                    <div class="tab-pane fade show active" id="regular-users-pane" role="tabpanel"
                        aria-labelledby="regular-users-tab" tabindex="0">
                        @include('admin.users._user_table', ['users' => $regularUsers])
                    </div>

                    {{-- TAB 2: DANH SÁCH ADMIN --}}
                    <div class="tab-pane fade" id="admin-users-pane" role="tabpanel" aria-labelledby="admin-users-tab"
                        tabindex="0">
                        @include('admin.users._user_table', ['users' => $adminUsers])
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection