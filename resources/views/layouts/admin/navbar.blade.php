<!-- Thanh Điều Hướng Admin - Dùng Bootstrap 5 -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
    <div class="container-fluid">
        <!-- Logo/Tên hệ thống -->
        <a class="navbar-brand fw-bolder text-white" href="{{ url('/admin/dashboard') }}">
            <i class="bi bi-box-seam-fill me-2"></i> ADMIN DASHBOARD
        </a>

        <!-- Nút toggle cho mobile (dùng để mở/đóng menu trên điện thoại) -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbarContent"
            aria-controls="adminNavbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Nội dung điều hướng chính -->
        <div class="collapse navbar-collapse" id="adminNavbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <!-- Quản lý Sản phẩm & Thêm Sản phẩm (Dropdown) -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="productDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-box-fill me-1"></i> Quản lý Sản phẩm
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="productDropdown">
                        <!-- Quản lý Sản phẩm (Danh sách) -->
                        <li><a class="dropdown-item" href="{{ route('admin.products.index') }}">
                                <i class="bi bi-list-columns me-2"></i> Danh sách Sản phẩm
                            </a></li>
                        <!-- Thêm Sản phẩm -->
                        <li><a class="dropdown-item" href="#">
                                <i class="bi bi-plus-circle-fill me-2"></i> Thêm Sản phẩm mới
                            </a></li>
                    </ul>
                </li>

                <!-- Quản lý Bài viết -->
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('admin.posts.index') }}">
                        <i class="bi bi-newspaper me-1"></i> Quản lý Bài viết
                    </a>
                </li>

                <!--Quản lý phân loại-->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="productDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-box-fill me-1"></i> Quản lý Phân loại
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="productDropdown">
                        <!-- Quản lý Sản phẩm (Danh sách) -->
                        <li><a class="dropdown-item" href="{{ route('admin.categories.index') }}">
                                <i class="bi bi-list-columns me-2"></i> Phân loại Bài viết
                            </a></li>
                        <!-- Thêm Sản phẩm -->
                        <li><a class="dropdown-item" href="{{ route('admin.product-categories.index') }}">
                                <i class="bi bi-list-columns me-2"></i> Phân loại Sản phẩm
                            </a></li>
                    </ul>
                </li>

                <!-- Quản lý Tiêu thụ (thường là Đơn hàng/Báo cáo) -->
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">
                        <i class="bi bi-graph-up-arrow me-1"></i> Quản lý Tiêu thụ
                    </a>
                </li>
            </ul>

            <!-- Phần Admin User/Logout bên phải -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white fw-semibold" href="#" id="adminUserDropdown"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name ?? 'Admin' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminUserDropdown">
                        <li><a class="dropdown-item" href="#">
                                <i class="bi bi-gear-fill me-2"></i> Hồ sơ
                            </a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <!-- Form Logout -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>