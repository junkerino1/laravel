<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'account_number',
        'amount',
        'transaction_type',
        'transaction_date',
        'platform',
        'status',
    ];
}
