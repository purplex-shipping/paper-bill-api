<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['wallet_id', 'type', 'amount', 'description','payment_gateway','transaction_id', 'transaction_type'];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
    
}
