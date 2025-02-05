<?php

namespace App\Models\admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class OfferHistory extends Model
{
    //
    use HasFactory;

    protected $table = "offer_history";
    protected $fillable = [
        'offer_id', 
        'user_id', 
        'listing_owner_id', 
        'negotiation_comments', 
        'negotiated_price', 
        'status'
    ];

    // Relationships
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function listingOwner()
    {
        return $this->belongsTo(User::class, 'listing_owner_id');
    }
}
