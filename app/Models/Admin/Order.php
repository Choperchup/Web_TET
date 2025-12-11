<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\OrderDetail;
use App\Models\User;

class Order extends Model
{
    use HasFactory;

    protected $casts = [
        'confirmed_at' => 'datetime',
        'canceled_at' => 'datetime',
        // Mặc dù created_at và updated_at thường tự động cast,
        // việc thêm chúng vào đây là an toàn và rõ ràng.
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_address',
        'customer_email',
        'total_amount',
        'payment_method',
        'status',
        'notes',
        'admin_notes',
        'confirmed_at',
        'canceled_at',
    ];

    // Một đơn hàng có nhiều chi tiết
    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    // Một đơn hàng thuộc về một người dùng
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
