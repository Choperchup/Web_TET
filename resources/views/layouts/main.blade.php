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
        /* CSS cho nút Chat tròn */
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

        #chat-circle:hover {
            transform: scale(1.1);
        }

        /* CSS cho khung Chat */
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
            /* Ẩn mặc định */
            flex-direction: column;
            overflow: hidden;
        }

        .chat-box-body {
            height: 350px;
            overflow-y: auto;
            background-color: #f8f9fa;
        }

        /* Bong bóng tin nhắn */
        .message-sent {
            text-align: right;
            margin-bottom: 15px;
        }

        .message-sent .text {
            background: #007bff;
            color: white;
            padding: 8px 12px;
            border-radius: 15px 15px 0 15px;
            display: inline-block;
            max-width: 80%;
        }

        .message-received {
            text-align: left;
            margin-bottom: 15px;
        }

        .message-received .text {
            background: #e9ecef;
            color: #333;
            padding: 8px 12px;
            border-radius: 15px 15px 15px 0;
            display: inline-block;
            max-width: 80%;
        }
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

        <div class="chat-box-body p-3" id="chat-content">
            <div class="message-received">
                <div class="text small shadow-sm">Chào bạn! Shop có thể giúp gì cho bạn ạ?</div>
            </div>
        </div>

        <div class="chat-box-footer p-2 border-top bg-white">
            <div class="input-group">
                <input type="text" id="chat-input" class="form-control border-0" placeholder="Nhập tin nhắn..."
                    autocomplete="off">
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

            // Mở khung chat
            chatCircle.onclick = () => {
                chatBox.style.display = 'flex';
                loadChatHistory();
            };

            toggle.onclick = () => chatBox.style.display = 'none';

            // Hàm load lịch sử chat
            function loadChatHistory() {
                fetch("/chat/history")
                    .then(res => res.json())
                    .then(messages => {
                        // Giữ lại câu chào và render tin nhắn
                        content.innerHTML = GREETING;
                        messages.forEach(msg => {
                            const side = msg.sender_role === 'user' ? 'sent' : 'received';
                            appendMessage(msg.message, side);
                        });
                        scrollDown();
                    });
            }

            // Tự động kiểm tra tin nhắn mới mỗi 5 giây (để nhận phản hồi từ Admin)
            setInterval(() => {
                if (chatBox.style.display === 'flex') {
                    loadChatHistory();
                }
            }, 5000);

            function appendMessage(text, side) {
                // Sử dụng div tạm để tránh lỗi XSS và hiển thị an toàn
                const msgDiv = document.createElement('div');
                msgDiv.className = `message-${side}`;
                msgDiv.innerHTML = `<div class="text small shadow-sm"></div>`;
                msgDiv.querySelector('.text').textContent = text; // Gán text an toàn
                content.appendChild(msgDiv);
            }

            function scrollDown() {
                content.scrollTop = content.scrollHeight;
            }

            // Gửi tin nhắn mới
            function sendMessage() {
                let msg = input.value.trim();
                if (msg === "") return;

                // Hiển thị tạm thời phía khách
                appendMessage(msg, 'sent');
                input.value = "";
                scrollDown();

                fetch("{{ route('chat.send') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ message: msg })
                })
                    .then(res => {
                        if (!res.ok) alert("Lỗi gửi tin nhắn!");
                    });
            }

            submit.onclick = sendMessage;
            input.addEventListener("keypress", (e) => {
                if (e.key === "Enter") {
                    e.preventDefault(); // Tránh reload trang
                    sendMessage();
                }
            });
        });
    </script>
</body>

</html>