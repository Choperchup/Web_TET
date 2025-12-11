<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
    <div class="container">
        <a href="{{ route('home') }}" class="navbar-brand">T.E.T - C.O.M</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu"
            aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-center" id="navbarMenu">
            <ul class="navbar-nav gap-3">
                <li class="nav-item"><a class="nav-link text-white" href="{{ route('products.index') }}">Giỏ quà Tết</a>
                </li>
                <li class="nav-item"><a class="nav-link text-white" href="#">Hộp quà Tết</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#">Giới thiệu</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="{{ route('posts.index') }}">Blog Tết</a></li>
            </ul>
        </div>

        <div class="d-flex align-items-center gap-3 ms-auto">
            @guest
                {{-- Nút Đăng nhập cho Guest --}}
                <a href="{{ route('login') }}" class="btn btn-outline-light px-4 py-2 fw-bold d-none d-lg-block">Đăng
                    nhập</a>
            @endguest

            @auth
                {{-- Thông tin người dùng và nút Logout --}}
                <div class="dropdown">
                    <a class="d-flex align-items-center text-decoration-none dropdown-toggle text-white" href="#"
                        id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="fw-semibold d-none d-lg-inline">Xin chào, {{ Auth::user()->name }}</span>
                        <i class="bi bi-person-circle fs-4 ms-2"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                        <li>
                            {{-- Thêm kiểm tra Auth::user()->is_admin hoặc tương tự --}}
                            <a class="dropdown-item" href="{{ route('admin.dashboard') ?? '#' }}">
                                <i class="bi bi-speedometer me-2"></i> Trang quản trị
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-person-circle me-2"></i> Thông tin cá nhân
                            </a>
                        </li>
                        {{-- ✨ THÊM LỐI VÀO XEM ĐƠN HÀNG CỦA TÔI (Tùy chọn) ✨ --}}
                        <li>
                            <a class="dropdown-item" href="{{ route('order-tracking.index') }}">
                                <i class="bi bi-box-seam me-2"></i> Đơn hàng của tôi
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('users.orders.index') }}">
                                <i class="bi bi-box-seam me-2"></i> Lịch sử mua hàng
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item text-danger fw-semibold" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                            </a>
                        </li>
                    </ul>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            @endauth

            {{-- ✨ GIỎ HÀNG (CART ICON) ✨ --}}
            <a href="{{ route('cart.index') }}" class="btn btn-warning position-relative px-3 py-2 fw-bold"
                title="Giỏ hàng">
                <i class="bi bi-cart4 fs-5"></i>
                {{-- Badge số lượng sản phẩm trong giỏ (Cần có biến $cartCount từ View Composer hoặc Session) --}}
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark">
                    {{-- Ví dụ: Giả định có biến $cartCount --}}
                    {{ $cartCount ?? 0 }}
                    <span class="visually-hidden">sản phẩm trong giỏ</span>
                </span>
            </a>

        </div>
    </div>
</nav>