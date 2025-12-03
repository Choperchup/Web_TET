<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'product_categories'; // Chỉ định tên bảng đã tạo trong Migration

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon_url',
        'parent_id', // Thêm trường parent_id để hỗ trợ danh mục cha
    ];

    /**
     * Định nghĩa mối quan hệ: Một danh mục có nhiều sản phẩm.
     * Mối quan hệ này sử dụng khóa ngoại 'category_id' trong bảng 'products'.
     */
    public function products()
    {
        return $this->hasMany(Products::class, 'category_id');
    }

    /**
     * QUAN HỆ CẤP BẬC: Một danh mục thuộc về một danh mục cha.
     */
    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    /**
     * QUAN HỆ CẤP BẬC: Một danh mục có thể có nhiều danh mục con.
     */
    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }
}
