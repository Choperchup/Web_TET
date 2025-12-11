<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Order;
use App\Models\Admin\Products; // Giả định Model Products ở đây
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminOrderController extends Controller
{
    /**
     * 1. Hiển thị danh sách đơn hàng (Orders Index)
     */
    public function index(Request $request)
    {
        $query = Order::with('user'); // Eager load user

        // Lọc theo trạng thái
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Tìm kiếm theo tên khách hàng, SĐT, ID
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('id', 'like', "%{$request->q}%")
                    ->orWhere('customer_name', 'like', "%{$request->q}%")
                    ->orWhere('customer_phone', 'like', "%{$request->q}%");
            });
        }

        $orders = $query->latest()->paginate(10)->appends($request->query());

        // Danh sách trạng thái để hiển thị bộ lọc
        $statuses = ['all', 'pending', 'confirmed', 'shipping', 'delivered', 'canceled'];

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    /**
     * 2. Hiển thị chi tiết đơn hàng
     */
    public function show(Order $order)
    {
        // Load chi tiết (details) và thông tin sản phẩm liên quan
        $order->load('details.product');
        return view('admin.orders.show', compact('order'));
    }

    /**
     * 3. XÁC NHẬN ĐƠN HÀNG
     */
    public function confirm(Order $order)
    {
        if ($order->status === 'pending') {
            try {
                // Giảm tồn kho và cập nhật trạng thái
                DB::transaction(function () use ($order) {
                    $order->status = 'confirmed';
                    $order->confirmed_at = Carbon::now();
                    $order->save();

                    // Logic giảm tồn kho:
                    foreach ($order->details as $detail) {
                        // Tránh lỗi nếu sản phẩm bị xóa mềm hoặc xóa vĩnh viễn
                        if ($detail->product) {
                            $product = Products::find($detail->product_id);
                            if ($product) {
                                $product->stock -= $detail->quantity;
                                $product->save();
                            }
                        }
                    }
                });

                return back()->with('success', 'Đã xác nhận đơn hàng #' . $order->id . ' thành công! Tồn kho đã được cập nhật.');
            } catch (\Exception $e) {
                return back()->with('error', 'Lỗi khi xác nhận đơn hàng: ' . $e->getMessage());
            }
        }

        return back()->with('error', 'Đơn hàng này không ở trạng thái Chờ xử lý (Pending) để xác nhận.');
    }

    /**
     * 4. HỦY BỎ ĐƠN HÀNG (Admin hủy)
     */
    public function cancel(Order $order, Request $request)
    {
        // Chỉ cho phép hủy khi đơn hàng chưa giao/chưa hủy
        if (in_array($order->status, ['pending', 'confirmed', 'shipping'])) {
            try {
                DB::transaction(function () use ($order, $request) {
                    $order->status = 'canceled';
                    $order->canceled_at = Carbon::now();
                    $order->admin_notes = $request->input('admin_notes', 'Đã hủy bởi Admin'); // Có thể yêu cầu Admin nhập lý do
                    $order->save();

                    // Logic hoàn trả tồn kho (chỉ khi đơn hàng đã từng được confirmed)
                    if ($order->confirmed_at) {
                        foreach ($order->details as $detail) {
                            if ($detail->product_id) {
                                $product = Products::find($detail->product_id);
                                if ($product) {
                                    $product->stock += $detail->quantity;
                                    $product->save();
                                }
                            }
                        }
                    }
                });

                return back()->with('success', 'Đã hủy đơn hàng #' . $order->id . ' thành công. Tồn kho đã được hoàn trả (nếu có).');
            } catch (\Exception $e) {
                return back()->with('error', 'Lỗi khi hủy đơn hàng: ' . $e->getMessage());
            }
        }

        return back()->with('error', 'Không thể hủy đơn hàng ở trạng thái "' . $order->status . '".');
    }

    public function updateStatus(Order $order, Request $request)
    {
        $request->validate(['status' => 'required|in:shipping,delivered']);

        if ($order->status == 'canceled' || $order->status == 'delivered') {
            return back()->with('error', 'Không thể thay đổi trạng thái của đơn hàng đã hủy hoặc đã giao.');
        }

        $order->status = $request->status;
        $order->save();

        return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công sang: ' . $request->status);
    }
}
