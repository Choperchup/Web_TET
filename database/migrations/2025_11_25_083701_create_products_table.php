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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('product_categories')->onDelete('cascade'); // Khóa ngoại liên kết tới product_categories
            $table->foreignId('user_id')->nullable()->constrained('users'); // Người tạo (Admin)

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->longText('content'); // Mô tả chi tiết sản phẩm

            $table->string('sku')->nullable()->unique(); // Mã sản phẩm
            $table->decimal('price', 10, 0); // Giá sản phẩm
            $table->decimal('sale_price', 10, 0)->nullable(); // Giá khuyến mãi

            $table->string('thumbnail')->nullable(); // Ảnh đại diện
            $table->enum('status', ['draft', 'published', 'out_of_stock'])->default('draft'); // Trạng thái sản phẩm
            $table->boolean('is_featured')->default(false); // Sản phẩm nổi bật

            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();

            $table->softDeletes(); // Hỗ trợ xóa mềm

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
