<!--- Navbar --->
<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
    <div class="container">
        <a href="{{ route('home') }}" class="navbar-brand">T.E.T - C.O.M</a>
        <button class="navbar-toggler" type="button" data-bs-toggler="">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu dành cho người đã đăng nhập -->
        <div class="collapse navbar-collapse justify-content-center" id="navbarMenu">
            <ul class="navbar-nav gap-3">
                <li class="nav-item"><a class="nav-link text-white" href="#">Giỏ quà Tết</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#">Hộp quà Tết</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#">Quà tặng doanh nghiệp</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#">Giới thiệu</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#">Dịch vụ</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#">Blog Tết</a></li>
            </ul>
        </div>
        @guest
            <div class="text-center text-light">
                <a href="{{ route('login') }}" class="btn btn-warning px-4 py-2 fw-bold">Đăng nhập</a>
            </div>
        @endguest
        <!-- Thông tin người dùng và nút Logout -->
        @auth
            <div class="dropdown ms-auto">
                <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" id="userDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="fw-semibold text-dark">Xin chào, {{ Auth::user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="bi bi-person-circle me-2"></i> Thông tin cá nhân
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
    </div>
</nav>