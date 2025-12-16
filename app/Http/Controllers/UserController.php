<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin\Order; // Đảm bảo import Model Order
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdatePasswordRequest;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lấy danh sách Admin (Giả định User::ROLE_ADMIN là 'admin')
        $adminUsers = User::where('role', User::ROLE_ADMIN)->get();

        // Lấy danh sách Users thường (Giả định User::ROLE_USER là 'user')
        $regularUsers = User::where('role', User::ROLE_USER)->get();

        // Truyền cả hai danh sách vào view
        return view('admin.users.index', compact('adminUsers', 'regularUsers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
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
            'role' => $request->role ?? User::ROLE_USER,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
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
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Thêm validation cho role và unique email
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:' . User::ROLE_ADMIN . ',' . User::ROLE_USER,
            // Thêm logic password nếu Admin muốn reset
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        // Chuyển hướng về route Admin mới
        return redirect()->route('admin.users.index')->with('success', 'User ' . $user->name . ' updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        // Chuyển hướng về route Admin mới
        return redirect()->route('admin.users.index')->with('success', 'User ' . $user->name . ' deleted successfully!');
    }

    /**
     * Hiển thị thùng rác người dùng đã xóa mềm.
     */

    public function trash()
    {
        $users = User::onlyTrashed()->paginate(10);
        return view('admin.users.trash', compact('users'));
    }

    /**
     * Khôi phục người dùng từ thùng rác.
     */
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        return redirect()->route('admin.users.trash')->with('success', 'User ' . $user->name . ' restored successfully!');
    }

    /**
     * Xóa vĩnh viễn người dùng khỏi hệ thống.
     */
    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete();
        return redirect()->route('admin.users.trash')->with('success', 'User ' . $user->name . ' permanently deleted!');
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

    /**
     * Hiển thị trang profile cá nhân của người dùng hiện tại.
     */
    public function profile()
    {
        $user = Auth::user(); // Lấy thông tin người dùng đang đăng nhập
        return view('users.profile.index', compact('user'));
    }

    /**
     * Xử lý cập nhật thông tin cá nhân (Tên, SĐT, Địa chỉ).
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        // 2. Cập nhật
        $user->update([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
        ]);

        return redirect()->route('user.profile')->with('success', 'Thông tin cá nhân đã được cập nhật thành công!');
    }

    /**
     * Xử lý đổi mật khẩu.
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = Auth::user();


        // 3. Cập nhật mật khẩu mới
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('user.profile')->with('success', 'Mật khẩu đã được đổi thành công!');
    }
}
