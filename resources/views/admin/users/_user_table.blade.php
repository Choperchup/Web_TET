<div class="table-responsive">
    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Role</th>
                <th>Ngày tạo</th>
                <th style="width: 180px;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        {{-- Giữ nguyên logic hiển thị role --}}
                        <span class="badge {{ $user->isAdmin() ? 'bg-danger' : 'bg-success' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        {{-- Nút SỬA --}}
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-info btn-sm me-1" title="Sửa">
                            <i class="fas fa-edit"></i> Sửa
                        </a>

                        {{-- Form XOÁ (Chuyển vào Thùng rác) --}}
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                            style="display: inline-block;"
                            onsubmit="return confirm('Bạn có chắc chắn muốn chuyển người dùng {{ $user->name }} vào thùng rác không?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" title="Xóa">
                                <i class="fas fa-trash-alt"></i> Xóa
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Không tìm thấy người dùng nào trong danh sách này.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    {{-- Loại bỏ phần phân trang (bạn đã comment ở code cũ) --}}
</div>