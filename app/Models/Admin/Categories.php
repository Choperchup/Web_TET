<?php

namespace App\Models\Admin;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Categories extends Model
{
    use SoftDeletes, HasFactory;
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Tự động tạo slug từ tên (name) trước khi lưu.
     *
     * @param string $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        // Cần kiểm tra và xử lý trùng lặp slug ở đây trong môi trường thực tế
        $this->attributes['slug'] = Str::slug($value);
    }


    /**
     * Định nghĩa mối quan hệ 1-n với Post.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
