<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin\Order; // Đảm bảo import Model Order
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateUserRequest $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Hiển thị danh sách các đơn hàng đã mua của người dùng hiện tại.
     */
    public function ordersIndex()
    {
        // Chỉ lấy các đơn hàng của người dùng đang đăng nhập (auth()->id())
        // Sắp xếp theo ngày đặt hàng mới nhất
        $orders = Order::where('user_id', Auth::id())
            ->latest()
            ->paginate(10); // Phân trang 10 đơn hàng/trang

        return view('users.orders.index', compact('orders'));
    }

    /**
     * Hiển thị chi tiết một đơn hàng cụ thể của người dùng hiện tại.
     */
    public function ordersShow(Order $order)
    {
        // 1. Kiểm tra quyền sở hữu đơn hàng
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem đơn hàng này.');
        }

        // 2. Eager load chi tiết đơn hàng
        $order->load('details.product'); // Tải chi tiết đơn hàng và thông tin sản phẩm liên quan

        // 3. Trả về view chi tiết
        return view('users.orders.show', compact('order'));
    }
}
