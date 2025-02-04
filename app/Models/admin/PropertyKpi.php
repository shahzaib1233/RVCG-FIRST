<?php

namespace App\Models\admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class PropertyKpi extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'views',
        'inquiries',
        'clicks',
        'conversion_rate',
        'users_id',
    ];

    // Relationship with Listing model
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
