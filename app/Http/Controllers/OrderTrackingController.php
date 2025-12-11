<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrackOrder;
use Illuminate\Http\Request;
use App\Models\Admin\Order;

class OrderTrackingController extends Controller
{
    /**
     * Hiển thị form tra cứu đơn hàng.
     */
    public function index()
    {
        // Trả về view chứa form nhập mã đơn hàng và số điện thoại
        return view('order-tracking.index');
    }

    /**
     * Xử lý tra cứu và hiển thị kết quả.
     */
    public function trackOrder(TrackOrder $request)
    {
        // 1. Lấy dữ liệu từ form đã được xác thực
        $orderId = $request->input('order_id');
        $phone = $request->input('customer_phone');

        // ✨ CHUẨN HÓA SỐ ĐIỆN THOẠI TRƯỚC KHI TRA CỨU ✨
        $cleanedPhone = preg_replace('/[^0-9]/', '', $phone);

        // 2. Tra cứu đơn hàng
        // Tìm đơn hàng dựa trên ID và SĐT khách hàng
        $order = Order::with('details') // Eager load chi tiết sản phẩm
            ->where('id', $orderId)
            ->where('customer_phone', $cleanedPhone)
            ->first();

        if (!$order) {
            // Không tìm thấy đơn hàng, trả về thông báo lỗi
            return back()->with('error', 'Không tìm thấy đơn hàng với Mã và Số điện thoại đã nhập. Vui lòng kiểm tra lại.')->withInput();
        }

        // 3. Trả về view với thông tin đơn hàng
        return view('order-tracking.index', [
            'order' => $order,
            'success_message' => 'Tìm thấy đơn hàng! Dưới đây là trạng thái hiện tại.'
        ]);
    }
}
