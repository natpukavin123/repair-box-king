<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    protected $fillable = ['backup_type', 'file_path', 'file_size', 'status'];
}
