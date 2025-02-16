<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    //
    protected $table = "payments";
protected $fillable = [
    'user_id', 
    'amount', 
    'payment_status', 
    'transaction_id', 
    'created_at', 
    'updated_at'
];
}
