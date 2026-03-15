<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'action', 'module', 'reference_id', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $action, string $module, ?int $referenceId = null, ?string $description = null): self
    {
        return self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'module' => $module,
            'reference_id' => $referenceId,
            'description' => $description,
        ]);
    }
}
