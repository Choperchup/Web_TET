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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Khách hàng (nếu có tài khoản)
            
            // Thông tin người nhận
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('customer_address');
            $table->string('customer_email')->nullable();

            // Tổng tiền & Thanh toán
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_method')->default('COD'); // COD, BankTransfer, ...

            // Trạng thái đơn hàng
            $table->enum('status', ['pending', 'confirmed', 'shipping', 'delivered', 'canceled'])->default('pending');
            
            // Ghi chú
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            
            // Lịch sử xác nhận/hủy
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
