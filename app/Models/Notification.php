<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['type', 'recipient', 'message', 'status', 'sent_at'];

    protected $casts = ['sent_at' => 'datetime'];
}
