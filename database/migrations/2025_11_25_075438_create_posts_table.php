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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title'); //Tiêu đề bài viết
            $table->string('slug')->unique(); //Đường dẫn thân thiện
            $table->text('content'); //Nội dung bài viết
            $table->string('thumbnail')->nullable(); //Ảnh đại diện bài viết

            // Khóa ngoại category_id trỏ tới bảng 'post_categories'
            $table->foreignId('category_id')->constrained('post_categories')->onDelete('cascade'); // ID danh mục bài viết (Trỏ đến bảng post_categories)

            // Khóa ngoại user_id trỏ tới bảng 'users'
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // ID tác giả bài viết

            $table->enum('status', ['draft', 'published', 'archived'])->default('draft'); //Trạng thái bài viết
            $table->timestamp('published_at')->nullable(); // Thời điểm xuất bản

            // SEO metadata
            $table->string('meta_title')->nullable(); //Tiêu đề SEO
            $table->text('meta_description')->nullable(); //Mô tả SEO

            // THÊM CỘT MỚI: Bài viết nổi bật (featured)
            $table->boolean('featured')->default(false);

            $table->softDeletes(); //Xóa mềm

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
