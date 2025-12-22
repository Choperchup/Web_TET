<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'message',
        'sender_role',
        'is_read',
    ];
}
