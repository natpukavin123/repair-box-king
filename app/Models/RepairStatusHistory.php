<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairStatusHistory extends Model
{
    protected $table = 'repair_status_history';

    protected $fillable = ['repair_id', 'status', 'notes', 'updated_by'];

    public function repair()
    {
        return $this->belongsTo(Repair::class);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
