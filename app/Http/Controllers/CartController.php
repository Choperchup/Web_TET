<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin\Products;
use Illuminate\Support\Facades\Session; // ✨ SỬ DỤNG SESSION THAY CHO CART FACADE ✨
use Illuminate\Support\Str; // Dùng để tạo ID duy nhất cho item trong giỏ hàng

class CartController extends Controller
{
    private $sessionKey = 'shopping_cart';

    /**
     * Lấy danh sách giỏ hàng từ Session.
     */
    private function getCartItems()
    {
        return Session::get($this->sessionKey, collect());
    }

    /**
     * Hiển thị trang Giỏ hàng.
     */
    public function index()
    {
        $cartItems = $this->getCartItems();

        // Tính tổng tiền
        $total = $cartItems->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        return view('cart.index', compact('cartItems', 'total'));
    }

    /**
     * Thêm sản phẩm vào giỏ hàng.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:100', // Giới hạn số lượng
        ]);

        $product = Products::where('status', 'published')->findOrFail($request->product_id);
        $quantity = (int) $request->quantity;

        $cartItems = $this->getCartItems();
        $price = $product->sale_price > 0 ? $product->sale_price : $product->price;

        // 1. Kiểm tra sản phẩm đã có trong giỏ chưa
        $existingItemKey = $cartItems->search(function ($item) use ($product) {
            return $item['product_id'] === $product->id;
        });

        if ($existingItemKey !== false) {
            // 2. Nếu đã có, TĂNG SỐ LƯỢNG VÀ GÁN LẠI (Sửa lỗi ở đây)

            // Lấy item hiện tại ra
            $item = $cartItems->get($existingItemKey);

            // Cập nhật số lượng
            $item['quantity'] += $quantity;

            // Gán lại item đã sửa đổi vào Collection gốc tại vị trí key
            $cartItems->put($existingItemKey, $item); // <--- Đã sửa lỗi Indirect modification

            $message = 'Đã cập nhật số lượng sản phẩm ' . $product->name . ' trong giỏ hàng!';
        } else {
            // 3. Nếu chưa có, thêm mới item vào giỏ
            $cartItems->push([
                'id' => Str::random(10), // Tạo ID duy nhất (RowId) cho item trong giỏ hàng
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $price,
                'quantity' => $quantity,
                'thumbnail' => $product->thumbnail,
                'slug' => $product->slug,
            ]);
            $message = 'Đã thêm sản phẩm ' . $product->name . ' vào giỏ hàng!';
        }

        // Cập nhật lại Session
        Session::put($this->sessionKey, $cartItems);

        return redirect()->back()->with('success', $message);
    }


    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required', // ID duy nhất của item trong giỏ (RowId)
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $cartItems = $this->getCartItems();
        $newQuantity = (int) $request->quantity;
        $rowId = $request->id;

        // 1. Tìm vị trí (key) của item cần cập nhật
        $existingItemKey = $cartItems->search(function ($item) use ($rowId) {
            return $item['id'] === $rowId;
        });

        if ($existingItemKey !== false) {
            // 2. Lấy item hiện tại ra để thao tác (sử dụng get() để lấy bản sao)
            $item = $cartItems->get($existingItemKey);

            // 3. Cập nhật số lượng mới trên item đã lấy ra
            $item['quantity'] = $newQuantity;

            // 4. Gán lại item đã sửa đổi vào Collection gốc tại vị trí key bằng put()
            $cartItems->put($existingItemKey, $item);

            // 5. Lưu Collection đã sửa đổi trở lại Session
            Session::put($this->sessionKey, $cartItems);

            return redirect()->route('cart.index')->with('success', 'Cập nhật số lượng sản phẩm thành công!');
        }

        return redirect()->route('cart.index')->with('error', 'Không tìm thấy sản phẩm cần cập nhật trong giỏ hàng!');
    }

    /**
     * Xóa một sản phẩm khỏi giỏ hàng (sử dụng RowId).
     */
    public function remove($id)
    {
        $cartItems = $this->getCartItems();

        // Lọc item muốn xóa
        $updatedCartItems = $cartItems->filter(function ($item) use ($id) {
            return $item['id'] !== $id;
        });

        Session::put($this->sessionKey, $updatedCartItems);

        return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }

    /**
     * Xóa toàn bộ giỏ hàng.
     */
    public function clear()
    {
        Session::forget($this->sessionKey);

        return redirect()->route('cart.index')->with('success', 'Đã xóa toàn bộ giỏ hàng!');
    }
}
