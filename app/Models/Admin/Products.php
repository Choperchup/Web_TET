<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';

    protected $casts = [
        'is_featured' => 'boolean',
        'is_on_sale' => 'boolean',
    ];

    protected $fillable = [
        'category_id',
        'user_id',
        'name',
        'slug',
        'short_description',
        'content',
        'sku',
        'price',
        'sale_price',
        'stock',
        'thumbnail',
        'status',
        'is_featured',
        'meta_title',
        'meta_description',
        'is_on_sale',
    ];

    /**
     * Định nghĩa mối quan hệ: Một sản phẩm thuộc về một danh mục.
     */
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Định nghĩa mối quan hệ: Sản phẩm được tạo bởi một người dùng (Admin).
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
