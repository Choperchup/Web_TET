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
        Schema::table('products', function (Blueprint $table) {
            // 💡 Thêm cột stock sau cột sale_price (hoặc bất kỳ cột nào bạn muốn)
            $table->integer('stock')->default(0)->after('sale_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Xóa cột stock nếu rollback
            $table->dropColumn('stock');
        });
    }
};
