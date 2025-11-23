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


            $table->unsignedBigInteger('category_id'); //ID danh mục bài viết
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

            $table->unsignedBigInteger('user_id'); //ID tác giả bài viết
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->enum('status', ['draft', 'published', 'archived'])->default('draft'); //Trạng thái bài viết
            $table->timestamp('published_at')->nullable(); // Thời điểm xuất bản

            // SEO metadata
            $table->string('meta_title')->nullable(); //Tiêu đề SEO
            $table->text('meta_description')->nullable(); //Mô tả SEO

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
