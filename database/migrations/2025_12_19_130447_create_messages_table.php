<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            // ID người dùng nếu họ đã đăng nhập (null nếu là khách)
            $table->unsignedBigInteger('user_id')->nullable();
            // Session ID để nhận diện khách vãng lai (không đăng nhập vẫn chat được)
            $table->string('session_id')->nullable();
            // Nội dung tin nhắn
            $table->text('message');
            // Ai gửi? (user hay admin)
            $table->enum('sender_role', ['user', 'admin'])->default('user');
            // Trạng thái xem
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
