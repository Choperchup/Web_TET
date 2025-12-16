<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Admin\Products;
use App\Models\Admin\Order;
use Illuminate\Support\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Số liệu tổng quan (K Pis)
        $totalUsers = User::count(); // Tổng số người dùng đã đăng ký
        $totalProducts = Products::count(); // Tổng số sản phẩm (kể cả draft và deleted - nếu không dùng withTrashed())

        // Tổng doanh thu Trọn đời (Chỉ tính đơn hàng đã giao thành công: status = 'delivered')
        $lifetimeRevenue = Order::where('status', 'delivered')->sum('total_amount');

        // Tổng số đơn hàng (Tất cả)
        $totalOrders = Order::count();

        // Thống kê đơn hàng theo trạng thái
        $statusCounts = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // 2. Doanh thu theo Ngày (7 ngày gần nhất)
        $dailyRevenue = $this->getDailyRevenue(7);

        // 3. Doanh thu theo Tháng (12 tháng gần nhất)
        $monthlyRevenue = $this->getMonthlyRevenue(12);

        // 4. Sản phẩm tồn kho thấp (Giả định sản phẩm có cột 'stock' và ngưỡng là 10)
        // Dựa trên logic giảm tồn kho trong AdminOrderController và cột 'stock' trong AdminProductController
        $lowStockProducts = Products::where('stock', '<', 10)
            ->where('stock', '>', 0)
            ->where('status', 'published')
            ->select('id', 'name', 'stock')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalProducts',
            'totalOrders',
            'lifetimeRevenue',
            'statusCounts',
            'dailyRevenue',
            'monthlyRevenue',
            'lowStockProducts'
        ));
    }

    /**
     * Lấy doanh thu theo ngày trong N ngày gần nhất.
     */
    private function getDailyRevenue(int $days = 7)
    {
        // Tạo chuỗi ngày từ 7 ngày trước đến hôm nay
        $dates = collect(range(-$days + 1, 0))->map(function ($day) {
            return Carbon::now()->addDays($day)->format('Y-m-d');
        });

        $query = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_amount) as total')
        )
            ->where('status', 'delivered')
            ->where('created_at', '>=', Carbon::now()->subDays($days - 1))
            ->groupBy('date')
            ->pluck('total', 'date');

        // Ghép dữ liệu doanh thu với tất cả các ngày trong $dates, gán 0 cho ngày không có doanh thu
        return $dates->mapWithKeys(function ($date) use ($query) {
            return [$date => $query->get($date) ?? 0];
        })->toArray();
    }

    /**
     * Lấy doanh thu theo tháng trong N tháng gần nhất.
     */
    private function getMonthlyRevenue(int $months = 12)
    {
        // Lấy doanh thu theo năm và tháng
        $query = Order::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_amount) as total')
        )
            ->where('status', 'delivered')
            ->where('created_at', '>=', Carbon::now()->subMonths($months))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Chuẩn hóa dữ liệu để dễ hiển thị trên biểu đồ (ví dụ: "Tháng 12/2025")
        $monthlyData = [];
        foreach ($query as $item) {
            $label = "Tháng {$item->month}/{$item->year}";
            $monthlyData[$label] = (float) $item->total;
        }

        return $monthlyData;
    }
}
