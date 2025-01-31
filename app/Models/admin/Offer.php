<?php

namespace App\Models\admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $table = "offers";

    protected $fillable = [
        'listing_id',
        'user_id',
        'offer_price',
        'offer_date',
        'status',
        'message',
        'expiry_date',
        'payment_method',
        'negotiation_comments',
        'accepted_price',
        'closing_date'
    ];
    
    public function listing()
{
    return $this->belongsTo(Listing::class, 'listing_id');
}

public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

}
