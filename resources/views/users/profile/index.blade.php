@extends('layouts.main') {{-- Đã giữ nguyên layout của bạn --}}

@section('title', 'Cập nhật Profile')

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">

                <h2>👤 Profile Người Dùng</h2>

                {{-- Logic xác định Tab đang hoạt động --}}
                @php
                    // Mặc định là tab 'profile'
                    $activeTab = 'profile';

                    // Kiểm tra nếu có lỗi validation liên quan đến mật khẩu, chuyển sang tab 'password'
                    if ($errors->has('current_password') || $errors->has('password') || session('password_success')) {
                        $activeTab = 'password';
                    }
                @endphp


                {{-- Hiển thị thông báo thành công (có thể xuất hiện từ cả 2 chức năng) --}}
                @if (session('success'))
                    <div class="alert alert-success mt-3">
                        {{ session('success') }}
                    </div>
                @endif


                <div class="card mt-4">
                    <div class="card-header p-0">
                        {{-- NAV TABS --}}
                        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $activeTab == 'profile' ? 'active' : '' }}" id="profile-tab"
                                    data-bs-toggle="tab" data-bs-target="#profile-pane" type="button" role="tab"
                                    aria-controls="profile-pane"
                                    aria-selected="{{ $activeTab == 'profile' ? 'true' : 'false' }}">
                                    Thông Tin Cơ Bản
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $activeTab == 'password' ? 'active' : '' }}" id="password-tab"
                                    data-bs-toggle="tab" data-bs-target="#password-pane" type="button" role="tab"
                                    aria-controls="password-pane"
                                    aria-selected="{{ $activeTab == 'password' ? 'true' : 'false' }}">
                                    Đổi Mật Khẩu
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        {{-- TAB CONTENT --}}
                        <div class="tab-content" id="profileTabsContent">

                            {{-- PHẦN 1: CẬP NHẬT THÔNG TIN CÁ NHÂN --}}
                            <div class="tab-pane fade {{ $activeTab == 'profile' ? 'show active' : '' }}" id="profile-pane"
                                role="tabpanel" aria-labelledby="profile-tab" tabindex="0">

                                <form method="POST" action="{{ route('user.profile.update') }}">
                                    @csrf
                                    @method('PUT') {{-- Sử dụng phương thức PUT cho cập nhật --}}

                                    {{-- Hiển thị lỗi validation riêng cho phần này nếu có --}}
                                    @if ($errors->hasAny(['name', 'phone_number', 'address']))
                                        <div class="alert alert-warning">
                                            Vui lòng kiểm tra các lỗi trong form Thông tin cơ bản.
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <label for="name" class="form-label">Tên người dùng</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email đăng ký</label>
                                        <input type="email" class="form-control" id="email" value="{{ $user->email }}"
                                            disabled readonly>
                                        <small class="form-text text-muted">Email không thể thay đổi tại đây.</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone_number" class="form-label">Số điện thoại</label>
                                        <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                            id="phone_number" name="phone_number"
                                            value="{{ old('phone_number', $user->phone_number) }}"
                                            placeholder="Ví dụ: 0901234567">
                                        @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="address" class="form-label">Địa chỉ</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" id="address"
                                            name="address" rows="3"
                                            placeholder="Địa chỉ chi tiết để Admin dễ kiểm soát">{{ old('address', $user->address) }}</textarea>
                                        <small class="form-text text-muted">Thông tin này giúp Admin dễ dàng kiểm soát đơn
                                            hàng.</small>
                                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary">Cập Nhật Thông Tin</button>
                                </form>
                            </div>

                            {{-- PHẦN 2: ĐỔI MẬT KHẨU (Tách riêng) --}}
                            <div class="tab-pane fade {{ $activeTab == 'password' ? 'show active' : '' }}"
                                id="password-pane" role="tabpanel" aria-labelledby="password-tab" tabindex="0">

                                <form method="POST" action="{{ route('user.password.update') }}">
                                    @csrf
                                    @method('PUT') {{-- Sử dụng phương thức PUT cho cập nhật --}}

                                    {{-- Hiển thị lỗi validation riêng cho phần này nếu có --}}
                                    @if ($errors->hasAny(['current_password', 'password']))
                                        <div class="alert alert-warning">
                                            Vui lòng kiểm tra các lỗi trong form Đổi mật khẩu.
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                                        <input type="password"
                                            class="form-control @error('current_password') is-invalid @enderror"
                                            id="current_password" name="current_password" required>
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label">Mật khẩu mới</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                            id="password" name="password" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" required>
                                    </div>

                                    <button type="submit" class="btn btn-warning">Đổi Mật Khẩu</button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection