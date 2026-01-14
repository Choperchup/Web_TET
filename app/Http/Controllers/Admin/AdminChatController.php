<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class AdminChatController extends Controller
{
    // Hiển thị giao diện quản lý chat
    public function index()
    {
        // Gộp nhóm theo ID người dùng hoặc Session (identifier) để không bị trùng
        $conversations = Message::select(
            DB::raw('COALESCE(CAST(user_id AS CHAR), session_id) as identifier'),
            DB::raw('MAX(created_at) as last_message_time'),
            // Đếm tin nhắn chưa đọc từ phía khách hàng
            DB::raw('SUM(CASE WHEN is_read = 0 AND sender_role != "admin" THEN 1 ELSE 0 END) as unread_count')
        )
            ->groupBy('identifier')
            ->orderBy('last_message_time', 'desc')
            ->get();

        return view('admin.chat.index', compact('conversations'));
    }

    // Lấy tin nhắn của một cuộc hội thoại cụ thể
    public function getConversationMessages($id)
    {
        // Lấy tin nhắn dựa trên cả user_id hoặc session_id
        $messages = Message::where('session_id', $id)
            ->orWhere('user_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        // Đánh dấu đã xem khi admin mở cuộc hội thoại
        Message::where(function ($q) use ($id) {
            $q->where('session_id', $id)->orWhere('user_id', $id);
        })
            ->where('sender_role', '!=', 'admin')
            ->update(['is_read' => true]);

        return response()->json($messages);
    }

    // Admin gửi tin nhắn trả lời
    public function reply(Request $request)
    {
        $id = $request->receiver_id;
        $isUser = is_numeric($id); // Kiểm tra nếu là ID số (user_id)

        $message = Message::create([
            // SỬA LỖI: Nếu là user thì session_id nên để null để đồng bộ logic frontend
            'session_id' => $isUser ? null : $id,
            'user_id' => $isUser ? $id : null,
            'message' => $request->message,
            'sender_role' => 'admin',
            'is_read' => true
        ]);

        return response()->json($message);
    }

    public function deleteConversation($id)
    {
        try {
            // Xóa tất cả tin nhắn có session_id hoặc user_id tương ứng
            Message::where('session_id', $id)
                ->orWhere('user_id', $id)
                ->delete();

            return response()->json(['success' => true, 'message' => 'Đã dọn dẹp lịch sử chat!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' + $e->getMessage()], 500);
        }
    }

    // Thu hồi tin nhắn đã gửi bởi admin
    public function recallMessage($id)
    {
        // Tìm tin nhắn, nếu không có sẽ trả về lỗi 404
        $message = Message::find($id);

        if (!$message) {
            return response::json(['success' => false, 'message' => 'Không tìm thấy tin nhắn'], 404);
        }

        // Kiểm tra nếu đúng là tin nhắn của admin thì mới cho xóa/thu hồi
        if ($message->sender_role === 'admin') {
            $oldContent = $message->message; // Lưu lại nội dung để phục vụ chức năng "Sửa"
            $message->delete();

            return response()->json([
                'success' => true,
                'content' => $oldContent
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Bạn không có quyền thu hồi tin nhắn này'], 403);
    }
}
