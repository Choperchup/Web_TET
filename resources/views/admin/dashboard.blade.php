@extends('layouts.admin.main')
{{-- Giả định bạn có layout chung cho Admin --}}

@section('title', 'Admin Dashboard')

@section('admin_content')
    {{-- Nội dung Dashboard --}}
    <h1 class="h3 mb-4 text-gray-800">Tổng Quan Hệ Thống</h1>

    <div class="row">

        {{-- 1. Tổng Doanh thu --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Tổng Doanh Thu (Delivered)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($lifetimeRevenue, 0, ',', '.') }} VNĐ
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Tổng Số Đơn Hàng --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng Số Đơn Hàng
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalOrders, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Tổng Người Dùng --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tổng Người Dùng</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                        {{ number_format($totalUsers, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Tổng Sản Phẩm --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Tổng Sản Phẩm
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalProducts, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cube fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        {{-- Chart 1: Doanh thu theo Tháng (12 tháng) --}}
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Doanh Thu Theo Tháng (12 tháng gần nhất)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        {{-- Canvas cho Biểu đồ Đường (Line Chart) --}}
                        <canvas id="monthlyRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chart 2: Trạng thái Đơn hàng (Pie Chart) --}}
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Thống Kê Trạng Thái Đơn Hàng</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="statusPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach ($statusCounts as $status => $count)
                            <span class="mr-2">
                                <i
                                    class="fas fa-circle text-{{ $status === 'delivered' ? 'success' : ($status === 'pending' ? 'warning' : ($status === 'canceled' ? 'danger' : 'secondary')) }}"></i>
                                {{ ucfirst($status) }}: {{ number_format($count) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        {{-- Chart 3: Doanh thu theo Ngày (7 ngày) --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Doanh Thu 7 Ngày Gần Nhất</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="dailyRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table: Sản phẩm Tồn kho thấp --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">⚠️ Cảnh Báo Tồn Kho Thấp</h6>
                </div>
                <div class="card-body">
                    @if($lowStockProducts->isNotEmpty())
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên Sản Phẩm</th>
                                    <th>Tồn Kho</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lowStockProducts as $product)
                                    <tr>
                                        <td>{{ $product->id }}</td>
                                        <td><a href="#">{{ $product->name }}</a></td>
                                        <td class="text-danger font-weight-bold">{{ $product->stock }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-success">Không có sản phẩm nào cần cảnh báo tồn kho.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Dữ liệu từ Controller
            const dailyLabels = @json(array_keys($dailyRevenue));
            const dailyData = @json(array_values($dailyRevenue));

            const monthlyLabels = @json(array_keys($monthlyRevenue));
            const monthlyData = @json(array_values($monthlyRevenue));

            const statusLabels = @json(array_keys($statusCounts));
            const statusData = @json(array_values($statusCounts));


            // --- 1. Biểu đồ Doanh thu theo Tháng (Line Chart) ---
            new Chart(document.getElementById('monthlyRevenueChart'), {
                type: 'line',
                data: {
                    labels: monthlyLabels,
                    datasets: [{
                        label: "Doanh Thu (VNĐ)",
                        lineTension: 0.3,
                        backgroundColor: "rgba(78, 115, 223, 0.05)",
                        borderColor: "rgba(78, 115, 223, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointBorderColor: "rgba(78, 115, 223, 1)",
                        data: monthlyData,
                    }],
                },
                options: {
                    // Cấu hình options cho line chart (ẩn/hiện lưới, tooltip, ...)
                }
            });

            // --- 2. Biểu đồ Trạng thái Đơn hàng (Pie Chart) ---
            new Chart(document.getElementById('statusPieChart'), {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusData,
                        backgroundColor: ['#f6c23e', '#1cc88a', '#36b9cc', '#e74a3b', '#858796', '#4e73df'], // Ví dụ màu sắc
                        hoverBackgroundColor: ['#f6c23e', '#1cc88a', '#36b9cc', '#e74a3b', '#858796', '#4e73df'],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    // Cấu hình options cho pie chart
                }
            });

            // --- 3. Biểu đồ Doanh thu theo Ngày (Bar Chart) ---
            new Chart(document.getElementById('dailyRevenueChart'), {
                type: 'bar',
                data: {
                    labels: dailyLabels,
                    datasets: [{
                        label: "Doanh Thu (VNĐ)",
                        backgroundColor: "#4e73df",
                        hoverBackgroundColor: "#2e59d9",
                        borderColor: "#4e73df",
                        data: dailyData,
                    }],
                },
                options: {
                    // Cấu hình options cho bar chart
                }
            });
        });
    </script>

@endsection