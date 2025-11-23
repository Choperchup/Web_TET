<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Admin\Categories;

class Posts extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'posts';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'thumbnail',
        'category_id',
        'user_id',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
        'view_count',
        'featured',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected $dates = ['published_at', 'deleted_at'];

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
