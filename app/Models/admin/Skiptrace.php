<?php

namespace App\Models\admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Skiptrace extends Model
{
    protected $table = 'skiptrace';

    protected $fillable = [
        'listing_id',
        'user_id',
        'owner_name',
        'owner_contact',
        'owner_email',
        'is_paid',
    ];

    /**
     * Relationship with Listing
     */
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
