<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'wallet_id',
        'tx_amount',
        'rx_amount',
        'rx_symbol',
        'tx_symbol',
        'rx_email',
    ];

    public function wallet()
    {
        return $this->belongsTo(wallet::class, 'wallet_id', 'id');
    }
}
