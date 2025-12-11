<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Admin\Order;
use App\Models\Admin\OrderDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection; // Dùng cho hàm getCartItems

class CheckoutController extends Controller
{
    private $sessionKey = 'shopping_cart';

    /**
     * Lấy danh sách giỏ hàng từ Session.
     * Phương thức này cần thiết cho placeOrder() và được gọi nội bộ.
     */
    private function getCartItems(): Collection
    {
        return Session::get($this->sessionKey, collect());
    }

    /**
     * Hiển thị trang Thanh toán.
     */
    public function index()
    {
        // Lấy giỏ hàng từ session (Đã được giải quyết bằng $this->getCartItems())
        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        // Tính tổng tiền
        $total = $cartItems->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        // Tải view thanh toán và truyền dữ liệu
        // ⚠️ KIỂM TRA LẠI: 'checkout.index' hay 'frontend.checkout.index'
        return view('checkout.index', compact('cartItems', 'total'));
    }

    /**
     * Xử lý đặt hàng.
     */
    public function placeOrder(CheckoutRequest $request)
    {
        $cartItems = $this->getCartItems();
        $total = $cartItems->sum(fn($item) => $item['price'] * $item['quantity']);

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        // ✨ CHUẨN HÓA SỐ ĐIỆN THOẠI ✨
        $cleanedPhone = preg_replace('/[^0-9]/', '', $request['phone']);

        try {
            DB::beginTransaction();

            // 2. Tạo Đơn hàng mới (Order)
            $order = Order::create([
                // Logic auth()->id() hoạt động đúng, trả về null nếu chưa đăng nhập
                'user_id' => auth()->id(),
                'customer_name' => $request['name'],
                'customer_phone' => $cleanedPhone,
                'customer_address' => $request['address'],
                // Logic email hoạt động đúng
                'customer_email' => $request['email'] ?? optional(auth()->user())->email,
                'total_amount' => $total,
                'payment_method' => $request['payment_method'],
                'notes' => $request['notes'],
                'status' => 'pending',
            ]);

            // 3. Tạo Chi tiết Đơn hàng (Order Details)
            $orderDetailsData = $cartItems->map(function ($item) use ($order) {
                return [
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            OrderDetail::insert($orderDetailsData);

            // 4. Xóa giỏ hàng khỏi Session
            Session::forget('shopping_cart');

            DB::commit();

            // 5. Chuyển hướng thành công
            return redirect()->route('home')->with('success', 'Đặt hàng thành công! Đơn hàng #' . $order->id . ' đang chờ được xử lý.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Lưu lại lỗi để debug: \Log::error('Order creation failed: ' . $e->getMessage());
            return back()->with('error', 'Đã xảy ra lỗi trong quá trình đặt hàng. Vui lòng thử lại.');
        }
    }
}
