@extends('layouts.admin.main')

@section('admin_content')
    <div class="container-fluid mt-4">
        <div class="row" style="height: 80vh;">
            <div class="col-md-4 border-end bg-white p-0">
                <div class="p-3 border-bottom bg-light font-weight-bold">Danh sách hội thoại</div>
                <div class="list-group list-group-flush overflow-auto" style="max-height: 100%;">
                @foreach($conversations as $conv)
    @php 
        $id = $conv->identifier;
        // Làm gọn tên nếu là khách không đăng nhập
        $name = is_numeric($id) ? "Khách hàng: " . $id : "Khách vãng lai: " . substr($id, 0, 8) . "...";
    @endphp
    <a href="javascript:void(0)" 
       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
       onclick="loadMessages('{{ $id }}', this)">
        <div>
            <h6 class="mb-1 text-primary">{{ $name }}</h6>
            <small class="text-muted">Cuối cùng: {{ $conv->last_message_time }}</small>
        </div>
        {{-- Hiện số tin nhắn mới nếu có --}}
        @if($conv->unread_count > 0)
            <span class="badge bg-danger rounded-pill">{{ $conv->unread_count }}</span>
        @endif
    </a>
@endforeach
                </div>
            </div>

            <div class="col-md-8 d-flex flex-column bg-white p-0">
                <div id="chat-header" class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Chọn một cuộc hội thoại</span>
                    <button id="btn-delete-chat" class="btn btn-sm btn-outline-danger" style="display:none;"
                        onclick="deleteCurrentChat()">
                        <i class="bi bi-trash"></i> Xóa lịch sử
                    </button>
                </div>

                <div id="admin-chat-content" class="flex-grow-1 p-3 overflow-auto bg-light" style="height: 300px;">
                </div>

                <div class="p-3 border-top bg-white">
                    <div class="input-group">
                        <input type="text" id="admin-input" class="form-control" placeholder="Nhập câu trả lời...">
                        <button class="btn btn-primary" onclick="sendReply()"><i class="bi bi-send"></i> Gửi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .msg-admin {
            text-align: right;
            margin-bottom: 10px;
        }

        .msg-admin span {
            background: #007bff;
            color: white;
            padding: 8px 12px;
            border-radius: 15px 15px 0 15px;
            display: inline-block;
        }

        .msg-user {
            text-align: left;
            margin-bottom: 10px;
        }

        .msg-user span {
            background: #e9ecef;
            color: #333;
            padding: 8px 12px;
            border-radius: 15px 15px 15px 0;
            display: inline-block;
        }
    </style>

    <script>
        let currentReceiverId = null;

        // VỊ TRÍ 2: Hàm loadMessages đã được cập nhật để hiện nút xóa và đổi tiêu đề đúng cách
        function loadMessages(id, element) {
           currentReceiverId = id;
    const content = document.getElementById('admin-chat-content');
    
    // Cập nhật giao diện khi chọn khách
    document.querySelector('#chat-header span').innerText = "Đang chat với: " + id;
    document.getElementById('btn-delete-chat').style.display = 'block';

    // Xóa badge thông báo đỏ khi click vào
    if (element && element.querySelector('.badge')) {
        element.querySelector('.badge').remove();
    }

    content.innerHTML = '<div class="text-center mt-5">Đang tải tin nhắn...</div>';

    fetch(`/admin/chat/messages/${id}`)
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(messages => {
            let html = '';
            if (messages.length === 0) {
                html = '<div class="text-center text-muted mt-5">Chưa có tin nhắn nào.</div>';
            } else {
                messages.forEach(m => {
                    let side = m.sender_role === 'admin' ? 'msg-admin' : 'msg-user';
                    html += `<div class="${side}"><span>${m.message}</span></div>`;
                });
            }
            content.innerHTML = html;
            scrollDown();
        })
        .catch(err => {
            console.error(err);
            content.innerHTML = '<div class="text-center text-danger mt-5">Lỗi kết nối máy chủ!</div>';
        });
        }

        // VỊ TRÍ 3: Thêm hàm deleteCurrentChat vào cuối thẻ script
        function deleteCurrentChat() {
            if (!currentReceiverId || !confirm("Bạn có chắc chắn muốn xóa vĩnh viễn lịch sử chat này để tiết kiệm dung lượng?")) return;

            fetch(`/admin/chat/delete/${currentReceiverId}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload(); // Load lại trang để danh sách bên trái biến mất cuộc hội thoại đó
                    } else {
                        alert("Lỗi: " + data.message);
                    }
                })
                .catch(err => alert("Lỗi kết nối khi xóa!"));
        }

        function sendReply() {
            let msg = document.getElementById('admin-input').value;
            if (!msg || !currentReceiverId) return;

            fetch("{{ route('admin.chat.reply') }}", {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: JSON.stringify({ receiver_id: currentReceiverId, message: msg })
            })
                .then(res => res.json())
                .then(() => {
                    document.getElementById('admin-input').value = '';
                    loadMessages(currentReceiverId);
                });
        }

        function scrollDown() {
            let obj = document.getElementById('admin-chat-content');
            obj.scrollTop = obj.scrollHeight;
        }
    </script>
@endsection