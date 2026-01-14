@extends('layouts.admin.main')

@section('admin_content')
<div class="container-fluid mt-4">
    <div class="row shadow-sm mx-1" style="height: 80vh; border: 1px solid #dee2e6; border-radius: 8px; overflow: hidden;">
        <div class="col-md-4 bg-white p-0 border-end">
            <div class="p-3 border-bottom bg-primary text-white fw-bold">
                <i class="bi bi-chat-dots me-2"></i>Danh sách hội thoại
            </div>
            <div class="list-group list-group-flush overflow-auto" style="height: calc(80vh - 56px);">
                @foreach($conversations as $conv)
                    @php 
                        $id = $conv->identifier;
                        $name = is_numeric($id) ? "Khách hàng: " . $id : "Khách vãng lai: " . substr($id, 0, 8);
                    @endphp
                    <a href="javascript:void(0)" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3"
                       onclick="loadMessages('{{ $id }}', this)">
                        <div>
                            <h6 class="mb-1 fw-bold text-dark">{{ $name }}</h6>
                            <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $conv->last_message_time }}</small>
                        </div>
                        @if($conv->unread_count > 0)
                            <span class="badge bg-danger rounded-pill">{{ $conv->unread_count }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        <div class="col-md-8 d-flex flex-column bg-white p-0">
            <div id="chat-header" class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
                <span class="fw-bold text-secondary">Chọn một cuộc hội thoại</span>
                <button id="btn-delete-chat" class="btn btn-sm btn-outline-danger" style="display:none;" onclick="deleteCurrentChat()">
                    <i class="bi bi-trash"></i> Xóa lịch sử
                </button>
            </div>

            <div id="admin-chat-content" class="flex-grow-1 p-4 overflow-auto bg-light d-flex flex-column">
                <div class="text-center my-auto text-muted">
                    <i class="bi bi-chat-left-text" style="font-size: 3rem;"></i>
                    <p>Hãy chọn khách hàng để xem tin nhắn</p>
                </div>
            </div>

            <div class="p-3 border-top bg-white">
                <div class="input-group">
                    <input type="text" id="admin-input" class="form-control" placeholder="Nhập câu trả lời..." autocomplete="off">
                    <button class="btn btn-primary px-4" onclick="sendReply()">
                        <i class="bi bi-send-fill"></i> Gửi
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
#admin-chat-content {
    display: flex;
    flex-direction: column;
}

/* ===== MESSAGE WRAPPER ===== */
.message-wrapper {
    display: flex;
    align-items: center;
    position: relative;
}

/* ===== ADMIN (RIGHT) ===== */
.msg-admin {
    align-self: flex-end;
    margin-bottom: 14px;
    max-width: 80%;
}

.msg-admin .text {
    background: #0d6efd;
    color: #fff;
    padding: 10px 14px;
    border-radius: 16px 16px 4px 16px;
    box-shadow: 0 2px 6px rgba(0,0,0,.1);
}

/* ===== USER (LEFT) ===== */
.msg-user {
    align-self: flex-start;
    margin-bottom: 14px;
    max-width: 80%;
}

.msg-user .text {
    background: #e9ecef;
    color: #333;
    padding: 10px 14px;
    border-radius: 16px 16px 16px 4px;
}

/* ===== 3 DOT MENU ===== */
.msg-options-container {
    margin-left: 6px;
    opacity: 0;
    transition: opacity .2s;
    position: relative;
}

/* Hover đúng bubble mới hiện */
.msg-admin .message-wrapper:hover .msg-options-container {
    opacity: 1;
}

.btn-msg-menu {
    cursor: pointer;
    font-size: 1.1rem;
    color: #dee2e6;
    padding: 4px;
}

.btn-msg-menu:hover {
    color: #fff;
}

/* ===== DROPDOWN ===== */
.msg-dropdown {
    display: none;
    position: absolute;
    top: auto;
    bottom: 110%;
    left: -20px;
    background: #fff;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    box-shadow: 0 6px 16px rgba(0,0,0,.15);
    min-width: 120px;
    z-index: 1000;
}

.msg-dropdown.show {
    display: block;
}

.msg-dropdown-item {
    padding: 8px 12px;
    font-size: 13px;
    cursor: pointer;
    display: flex;
    align-items: center;
}

.msg-dropdown-item i {
    margin-right: 8px;
}

.msg-dropdown-item:hover {
    background: #f8f9fa;
}

.item-edit {
    color: #0d6efd;
    border-bottom: 1px solid #eee;
}

.item-recall {
    color: #dc3545;
}

/* ===== RECALL MESSAGE ===== */
.msg-recalled .text {
    background: #f1f3f5 !important;
    color: #868e96 !important;
    font-style: italic;
}
</style>


<script>
    let currentReceiverId = null;
    const inputField = document.getElementById('admin-input');

    function loadMessages(id, element) {
        currentReceiverId = id;
        const content = document.getElementById('admin-chat-content');
        document.querySelector('#chat-header span').innerText = "Đang chat với: " + id;
        document.getElementById('btn-delete-chat').style.display = 'block';

        if (element && element.querySelector('.badge')) element.querySelector('.badge').remove();

        fetch(`/admin/chat/messages/${id}`)
            .then(res => res.json())
            .then(messages => {
                content.innerHTML = '';
                messages.forEach(m => appendMessageHtml(m));
                scrollDown();
            });
    }
function appendMessageHtml(m) {
    const content = document.getElementById('admin-chat-content');
    const isAdmin = m.sender_role === 'admin';

    const msgDiv = document.createElement('div');
    msgDiv.className = isAdmin ? 'msg-admin' : 'msg-user';
    msgDiv.id = `msg-${m.id}`;

    let menuHtml = '';
    if (isAdmin) {
        menuHtml = `
            <div class="msg-options-container">
                <i class="bi bi-three-dots-vertical btn-msg-menu"
                   onclick="toggleMenu(event, ${m.id})"></i>

                <div class="msg-dropdown" id="dropdown-${m.id}">
                    <div class="msg-dropdown-item item-edit"
                        onclick="editAdminMessage(${m.id})">
                        <i class="bi bi-pencil"></i> Sửa
                    </div>
                    <div class="msg-dropdown-item item-recall"
                        onclick="recallAdminMessage(${m.id})">
                        <i class="bi bi-trash"></i> Thu hồi
                    </div>
                </div>
            </div>
        `;
    }

    msgDiv.innerHTML = `
        <div class="message-wrapper">
            ${isAdmin ? menuHtml : ''}
            <div class="text">${m.message}</div>
        </div>
    `;

    content.appendChild(msgDiv);
}

    window.toggleMenu = function(e, id) {
    e.stopPropagation();
    document.querySelectorAll('.msg-dropdown')
        .forEach(el => el.classList.remove('show'));

    const dropdown = document.getElementById(`dropdown-${id}`);
    if (dropdown) dropdown.classList.toggle('show');
};

document.addEventListener('click', () => {
    document.querySelectorAll('.msg-dropdown')
        .forEach(el => el.classList.remove('show'));
});

    // FIX LỖI UNDEFINED KHI CHỈNH SỬA
    window.editAdminMessage = function(id) {
        fetch(`/admin/chat/recall/${id}`, {
            method: "DELETE",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`msg-${id}`).remove();
                // Đảm bảo data.content tồn tại, nếu không để chuỗi rỗng
                inputField.value = data.content || ''; 
                inputField.focus();
            }
        });
    };

    window.recallAdminMessage = function(id) {
    if (!confirm("Thu hồi tin nhắn này?")) return;

    fetch(`/admin/chat/recall/${id}`, {
        method: "DELETE",
        headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const msg = document.getElementById(`msg-${id}`);
            msg.classList.add('msg-recalled');
            msg.querySelector('.text').innerHTML =
                '<i class="bi bi-arrow-counterclockwise me-1"></i> Tin nhắn đã bị thu hồi';

            const menu = msg.querySelector('.msg-options-container');
            if (menu) menu.remove();
        }
    });
};

    function sendReply() {
        let msg = inputField.value.trim();
        if (!msg || !currentReceiverId) return;

        fetch("{{ route('admin.chat.reply') }}", {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            body: JSON.stringify({ receiver_id: currentReceiverId, message: msg })
        })
        .then(res => res.json())
        .then(newMessage => {
            inputField.value = '';
            appendMessageHtml(newMessage);
            scrollDown();
        });
    }

    function scrollDown() {
        let obj = document.getElementById('admin-chat-content');
        obj.scrollTop = obj.scrollHeight;
    }

    inputField.onkeypress = (e) => { if (e.key === "Enter") sendReply(); };
</script>
@endsection