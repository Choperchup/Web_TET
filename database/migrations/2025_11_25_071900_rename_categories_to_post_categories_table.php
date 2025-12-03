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
        Schema::table('post_categories', function (Blueprint $table) {
            // Đổi tên bảng từ 'categories' sang 'post_categories'
            Schema::rename('categories', 'post_categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_categories', function (Blueprint $table) {
            // Đổi tên bảng ngược lại từ 'post_categories' về 'categories'
            Schema::rename('post_categories', 'categories');
        });
    }
};
