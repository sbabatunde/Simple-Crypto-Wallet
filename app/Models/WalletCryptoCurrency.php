<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletCryptoCurrency extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency',
        'wallet_id',
        'balance'
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id', 'id');
    }
}
