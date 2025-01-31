<?php

namespace App\Models\admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SearchHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'price_min',
        'price_max',
        'city',
        'area_min',
        'area_max',
    ];

    // Relationship to User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


