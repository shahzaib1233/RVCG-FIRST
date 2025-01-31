<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 
        'title', 
        'message', 
        'send_to_all', 
        'scheduled_at', 
        'is_sent'
    ];
}
