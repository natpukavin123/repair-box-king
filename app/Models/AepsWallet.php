<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AepsWallet extends Model
{
    protected $table = 'aeps_wallet';

    protected $fillable = ['balance'];

    protected $casts = ['balance' => 'decimal:2'];

    public static function current(): self
    {
        return static::first() ?? static::create(['balance' => 0]);
    }

    public function credit(float $amount): void
    {
        $this->increment('balance', $amount);
    }

    public function debit(float $amount): void
    {
        $this->decrement('balance', $amount);
    }
}
