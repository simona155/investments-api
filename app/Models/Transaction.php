<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'account_id',
        'type',
        'amount',
        'instrument',
        'quantity',
        'price',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
