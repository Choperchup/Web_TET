<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* --- CSS GIAO DIỆN CHAT --- */
        #chat-circle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .chat-box {
            position: fixed;
            bottom: 90px;
            right: 20px;
            width: 350px;
            max-width: 85vw;
            background: white;
            border-radius: 15px;
            z-index: 1000;
            display: none;
            flex-direction: column;
            overflow: hidden;
        }

        .chat-box-body {
            height: 350px;
            overflow-y: auto;
            background-color: #f8f9fa;
        }

        /* --- BỐ CỤC TIN NHẮN --- */
        .message-sent {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 15px;
            padding-left: 60px;
            padding-right: 10px;
        }

        .message-wrapper {
            display: flex;
            align-items: center;
            position: relative;
        }

        .message-sent .text {
            background: #007bff;
            color: white;
            padding: 8px 12px;
            border-radius: 15px 15px 0 15px;
            word-wrap: break-word;
        }

        .message-received {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 15px;
            padding-right: 60px;
            padding-left: 10px;
        }

        .message-received .text {
            background: #e9ecef;
            color: #333;
            padding: 8px 12px;
            border-radius: 15px 15px 15px 0;
            word-wrap: break-word;
        }

        /* --- NÚT 3 CHẤM (CHỈ HIỆN KHI HOVER) --- */
        .msg-options-container {
            margin-right: 8px;
            opacity: 0; /* Mặc định ẩn */
            transition: opacity 0.2s ease;
            position: relative;
        }

        .message-sent:hover .msg-options-container {
            opacity: 1; /* Hiện khi di chuột vào tin nhắn */
        }

        .btn-msg-menu {
            cursor: pointer;
            color: #adb5bd;
            font-size: 1.1rem;
            padding: 4px;
        }

        /* --- DROPDOWN MENU TÁCH BIỆT --- */
        .msg-dropdown {
            display: none;
            position: absolute;
            bottom: 100%;
            right: 0;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1001;
            min-width: 120px;
            margin-bottom: 5px;
            overflow: hidden;
        }

        .msg-dropdown.show {
            display: block;
        }

        .msg-dropdown-item {
            padding: 10px 15px;
            font-size: 13px;
            cursor: pointer;
            display: flex;
            align-items: center;
            transition: background 0.2s;
        }

        /* Màu sắc riêng cho Chỉnh sửa và Thu hồi */
        .item-edit { color: #007bff; border-bottom: 1px solid #f8f9fa; }
        .item-edit:hover { background-color: #f0f7ff; }

        .item-recall { color: #dc3545; }
        .item-recall:hover { background-color: #fff1f0; }

        .msg-dropdown-item i { margin-right: 8px; }
    </style>
</head>

<body>
    @include('layouts.navbar')

    <main class="container my-5">
        @include('layouts.slider')
        @yield('content')
    </main>

    @include('layouts.footer')

    <div id="chat-circle" class="btn btn-primary rounded-circle shadow-lg">
        <i class="bi bi-chat-dots-fill"></i>
    </div>

    <div class="chat-box shadow-lg border" id="chat-box">
        <div class="chat-box-header bg-primary text-white d-flex justify-content-between align-items-center p-3">
            <div class="d-flex align-items-center">
                <i class="bi bi-person-circle me-2"></i>
                <span class="fw-bold">Hỗ trợ trực tuyến</span>
            </div>
            <button type="button" class="btn-close btn-close-white" id="chat-box-toggle"></button>
        </div>

        <div class="chat-box-body p-3" id="chat-content"></div>

        <div class="chat-box-footer p-2 border-top bg-white">
            <div class="input-group">
                <input type="text" id="chat-input" class="form-control border-0" placeholder="Nhập tin nhắn..." autocomplete="off">
                <button class="btn btn-link text-primary" id="chat-submit">
                    <i class="bi bi-send-fill fs-5"></i>
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chatCircle = document.getElementById('chat-circle');
            const chatBox = document.getElementById('chat-box');
            const toggle = document.getElementById('chat-box-toggle');
            const input = document.getElementById('chat-input');
            const submit = document.getElementById('chat-submit');
            const content = document.getElementById('chat-content');

            const GREETING = `<div class="message-received">
                                <div class="text small shadow-sm">Chào bạn! Shop có thể giúp gì cho bạn ạ?</div>
                              </div>`;

            chatCircle.onclick = () => {
                chatBox.style.display = 'flex';
                loadChatHistory();
            };

            toggle.onclick = () => chatBox.style.display = 'none';

            function loadChatHistory() {
                fetch("/chat/history")
                    .then(res => res.json())
                    .then(messages => {
                        content.innerHTML = GREETING;
                        messages.forEach(msg => {
                            const side = msg.sender_role === 'user' ? 'sent' : 'received';
                            appendMessage(msg.message, side, msg.id);
                        });
                        scrollDown();
                    });
            }

            // --- HÀM HIỂN THỊ TIN NHẮN (ĐÃ TÁCH BIỆT CHỈNH SỬA & THU HỒI) ---
            function appendMessage(text, side, id = null) {
                const msgDiv = document.createElement('div');
                msgDiv.className = `message-${side}`;
                if (id) msgDiv.id = `msg-${id}`;

                let optionsHtml = '';
                if (side === 'sent' && id) {
                    optionsHtml = `
                        <div class="msg-options-container">
                            <div class="msg-dropdown" id="dropdown-${id}">
                                <div class="msg-dropdown-item item-edit" onclick="editMessage(${id})">
                                    <i class="bi bi-pencil-square"></i> Chỉnh sửa
                                </div>
                                <div class="msg-dropdown-item item-recall" onclick="recallMessage(${id})">
                                    <i class="bi bi-trash3"></i> Thu hồi
                                </div>
                            </div>
                            <i class="bi bi-three-dots-vertical btn-msg-menu" onclick="toggleMessageMenu(event, ${id})"></i>
                        </div>`;
                }

                msgDiv.innerHTML = `
                    <div class="message-wrapper">
                        ${optionsHtml}
                        <div class="text small shadow-sm">${text}</div>
                    </div>`;

                content.appendChild(msgDiv);
            }

            // --- ĐÓNG MỞ MENU ---
            window.toggleMessageMenu = function (event, id) {
                event.stopPropagation();
                document.querySelectorAll('.msg-dropdown').forEach(el => {
                    if (el.id !== `dropdown-${id}`) el.classList.remove('show');
                });
                const menu = document.getElementById(`dropdown-${id}`);
                if (menu) menu.classList.toggle('show');
            };

            document.addEventListener('click', () => {
                document.querySelectorAll('.msg-dropdown').forEach(el => el.classList.remove('show'));
            });

            // --- CHỨC NĂNG 1: CHỈNH SỬA (Thu hồi + đưa text vào ô input) ---
            window.editMessage = function(id) {
                fetch(`/chat/recall/${id}`, {
                    method: "DELETE",
                    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const element = document.getElementById(`msg-${id}`);
                        if (element) element.remove();
                        // Sửa lỗi undefined bằng cách dùng đúng tên biến từ Controller (content)
                        input.value = data.content; 
                        input.focus();
                    }
                });
            };

            // --- CHỨC NĂNG 2: THU HỒI (Chỉ xóa tin nhắn) ---
            window.recallMessage = function(id) {
                if (!confirm("Bạn có chắc chắn muốn thu hồi tin nhắn này?")) return;
                fetch(`/chat/recall/${id}`, {
                    method: "DELETE",
                    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const element = document.getElementById(`msg-${id}`);
                        if (element) element.remove();
                    } else {
                        alert(data.message);
                    }
                });
            };

            function sendMessage() {
                let msg = input.value.trim();
                if (msg === "") return;
                input.value = "";
                fetch("{{ route('chat.send') }}", {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                    body: JSON.stringify({ message: msg })
                })
                .then(res => res.json())
                .then(response => {
                    if (response.status === 'success') {
                        appendMessage(response.data.message, 'sent', response.data.id);
                        scrollDown();
                    }
                });
            }

            function scrollDown() { content.scrollTop = content.scrollHeight; }
            submit.onclick = sendMessage;
            input.onkeypress = (e) => { if (e.key === "Enter") { e.preventDefault(); sendMessage(); } };
        });
    </script>
</body>
</html>