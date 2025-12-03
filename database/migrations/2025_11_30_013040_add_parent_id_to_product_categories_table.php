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
        Schema::table('product_categories', function (Blueprint $table) {
            // Thêm parent_id, cho phép NULL (dành cho danh mục cấp cao nhất)
            // Liên kết khóa ngoại với chính bảng product_categories
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('product_categories')
                ->onDelete('cascade') // Nếu danh mục cha bị xóa, danh mục con cũng bị xóa (có thể cân nhắc set null)
                ->after('slug'); // Đặt sau cột 'slug'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            // Xóa khóa ngoại trước khi xóa cột
            $table->dropConstrainedForeignId('parent_id');
            // Xóa cột
            $table->dropColumn('parent_id');
        });
    }
};
