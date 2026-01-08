<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // 1. Lưu tin nhắn của khách gửi lên
    public function sendMessage(Request $request)
    {
        $request->validate(['message' => 'required']);

        $message = Message::create([
            'user_id' => Auth::id(),
            'session_id' => session()->getId(),
            'message' => $request->message,
            'sender_role' => 'user',
        ]);

        return response()->json(['status' => 'success', 'data' => $message]);
    }

    // 2. Thu hồi tin nhắn (Mới)
    public function recallMessage($id)
    {
        $message = Message::findOrFail($id);

        // Kiểm tra quyền: Chỉ cho phép người gửi thu hồi trong phiên hiện tại
        $isOwner = (Auth::check() && $message->user_id == Auth::id()) || ($message->session_id == session()->getId());

        if ($isOwner) {
            $oldContent = $message->message;
            $message->delete(); // Xóa khỏi Database
            return response()->json(['success' => true, 'content' => $oldContent]);
        }

        return response()->json(['success' => false, 'message' => 'Không có quyền thu hồi'], 403);
    }

    // 2. Lấy lại lịch sử chat khi khách load lại trang
    public function getMessages()
    {
        $sessionId = session()->getId();
        $userId = Auth::id();

        $query = Message::query();
        if ($userId) {
            $query->where('user_id', $userId)->orWhere('session_id', $sessionId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $messages = $query->orderBy('created_at', 'asc')->get();
        return response()->json($messages);
    }
}
