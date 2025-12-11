@extends('layouts.admin.main')

@section('title', 'Chi Tiết Đơn Hàng #' . $order->id)

@section('admin_content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Chi Tiết Đơn Hàng #{{ $order->id }}</h1>
            <a href="{{ route('admin.orders.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại Danh sách
            </a>
        </div>

        {{-- Hiển thị thông báo (Xác nhận/Hủy bỏ thành công) --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Hàm Helper để hiển thị Badge Trạng thái --}}
        @php
            $statusBadge = function ($status) {
                $class = match ($status) {
                    'pending' => 'badge bg-warning text-dark',
                    'confirmed' => 'badge bg-info',
                    'shipping' => 'badge bg-primary',
                    'delivered' => 'badge bg-success',
                    'canceled' => 'badge bg-danger',
                    default => 'badge bg-secondary'
                };
                $label = match ($status) {
                    'pending' => 'Chờ xử lý',
                    'confirmed' => 'Đã xác nhận',
                    'shipping' => 'Đang giao hàng',
                    'delivered' => 'Đã giao thành công',
                    'canceled' => 'Đã hủy',
                    default => 'Không rõ'
                };
                return "<span class='{$class}'>{$label}</span>";
            };
        @endphp

        <div class="row">
            {{-- Cột 1: Thông tin Đơn hàng và Khách hàng --}}
            <div class="col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">Thông Tin Chung</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Trạng thái:</strong> {!! $statusBadge($order->status) !!}</p>
                        <p><strong>Ngày đặt hàng:</strong> {{ $order->created_at->format('H:i:s d/m/Y') }}</p>
                        @if ($order->confirmed_at)
                            <p><strong>Ngày xác nhận:</strong> <span
                                    class="text-success">{{ $order->confirmed_at->format('H:i:s d/m/Y') }}</span></p>
                        @endif
                        @if ($order->canceled_at)
                            <p><strong>Ngày hủy:</strong> <span
                                    class="text-danger">{{ $order->canceled_at->format('H:i:s d/m/Y') }}</span></p>
                        @endif
                        <hr>

                        <h6 class="font-weight-bold mt-4">Thông Tin Khách Hàng</h6>
                        <p><strong>Tên người nhận:</strong> {{ $order->customer_name }}</p>
                        <p><strong>Số điện thoại:</strong> {{ $order->customer_phone }}</p>
                        @if ($order->customer_email)
                            <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                        @endif
                        <p><strong>Địa chỉ:</strong> {{ $order->customer_address }}</p>
                        <p><strong>Ghi chú KH:</strong> {{ $order->notes ?? 'Không có ghi chú' }}</p>

                        @if ($order->user)
                            <p class="text-muted">Được đặt bởi User ID: {{ $order->user->id }} ({{ $order->user->name }})</p>
                        @endif

                        @if ($order->admin_notes)
                            <h6 class="font-weight-bold text-danger mt-4">Ghi chú của Admin</h6>
                            <p class="text-danger">{{ $order->admin_notes }}</p>
                        @endif
                    </div>
                </div>

                {{-- Khu vực Hành động (Action Buttons) --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-secondary text-white">
                        <h6 class="m-0 font-weight-bold">Hành Động Quản Lý</h6>
                    </div>
                    <div class="card-body">

                        {{-- Xác nhận Đơn hàng --}}
                        @if ($order->status == 'pending')
                            <form action="{{ route('admin.orders.confirm', $order) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-block w-100 mb-2"
                                    onclick="return confirm('Xác nhận đơn hàng này? Việc này sẽ trừ tồn kho sản phẩm.')">
                                    <i class="fas fa-check"></i> Xác Nhận Đơn Hàng
                                </button>
                            </form>
                        @endif

                        {{-- Chuyển trạng thái Đang giao hàng --}}
                        @if ($order->status == 'confirmed')
                            {{-- Bạn có thể thêm form/modal cho trạng thái này --}}
                            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="shipping">
                                <button type="submit" class="btn btn-primary btn-block w-100 mb-2"
                                    onclick="return confirm('Chuyển trạng thái sang Đang giao hàng?')">
                                    <i class="fas fa-truck"></i> Chuyển Đang Giao
                                </button>
                            </form>
                        @endif

                        {{-- Chuyển trạng thái Đã giao thành công --}}
                        @if ($order->status == 'shipping')
                            {{-- Bạn có thể thêm form/modal cho trạng thái này --}}
                            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="delivered">
                                <button type="submit" class="btn btn-info btn-block w-100 mb-2"
                                    onclick="return confirm('Chuyển trạng thái sang Đã giao thành công?')">
                                    <i class="fas fa-box"></i> Đã Giao Hàng
                                </button>
                            </form>
                        @endif


                        {{-- Nút Hủy bỏ Đơn hàng (Dùng modal để nhập lý do) --}}
                        @if (in_array($order->status, ['pending', 'confirmed', 'shipping']))
                            <button type="button" class="btn btn-danger btn-block w-100" data-bs-toggle="modal"
                                data-bs-target="#cancelModal">
                                <i class="fas fa-times"></i> Hủy Đơn Hàng
                            </button>
                        @endif

                        {{-- Đã hủy / Đã giao: Không còn nút hành động --}}
                        @if ($order->status == 'canceled' || $order->status == 'delivered')
                            <div class="alert alert-light text-center">Không có hành động khả dụng.</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Cột 2: Chi tiết Sản phẩm và Tổng kết --}}
            <div class="col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-info text-white">
                        <h6 class="m-0 font-weight-bold">Chi Tiết Sản Phẩm</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Giá</th>
                                        <th>SL</th>
                                        <th>Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->details as $detail)
                                        <tr>
                                            <td>
                                                @if ($detail->product)
                                                    <a href="{{ route('products.show', $detail->product->slug) }}" target="_blank">
                                                        {{ $detail->product_name }}
                                                    </a>
                                                @else
                                                    {{ $detail->product_name }} <span class="badge bg-danger">Đã xóa</span>
                                                @endif
                                            </td>
                                            <td>{{ number_format($detail->price) }} VNĐ</td>
                                            <td>{{ $detail->quantity }}</td>
                                            <td>{{ number_format($detail->price * $detail->quantity) }} VNĐ</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Tổng Cộng (Chưa phí VC):</th>
                                        <th>{{ number_format($order->total_amount) }} VNĐ</th>
                                    </tr>
                                    {{-- Thêm dòng phí vận chuyển, giảm giá nếu có --}}
                                    <tr>
                                        <th colspan="3" class="text-end">Phương thức thanh toán:</th>
                                        <th><span class="text-primary">{{ $order->payment_method }}</span></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL HỦY ĐƠN HÀNG --}}
    @if (in_array($order->status, ['pending', 'confirmed', 'shipping']))
        <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="cancelModalLabel">Xác Nhận Hủy Đơn Hàng #{{ $order->id }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.orders.cancel', $order) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <p class="text-danger">Bạn có chắc chắn muốn hủy đơn hàng này? Việc này sẽ **HOÀN TRẢ TỒN KHO** (nếu
                                đơn hàng đã được xác nhận trước đó).</p>
                            <div class="mb-3">
                                <label for="admin_notes" class="form-label">Lý do hủy (bắt buộc):</label>
                                <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-danger">Hủy Đơn Hàng</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Lưu ý: Bạn cần đảm bảo đã tạo route 'admin.orders.updateStatus' (PUT) trong BƯỚC 3 nếu muốn sử dụng các nút chuyển
    trạng thái 'shipping' và 'delivered' ở trên. --}}
@endsection