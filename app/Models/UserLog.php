<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'login_at',
        'ip_address',
        'user_agent',
        'location',
    ];

    // Optional: Define the relationship to the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
