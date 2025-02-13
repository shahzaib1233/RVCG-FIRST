<?php

namespace App\Models\admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class SavedProperty extends Model
{
    protected $fillable = ['user_id', 'listing_id', 'is_favourite'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
    
    public function cities()
    {
        return $this->belongsTo(Cities::class);
    }
}
