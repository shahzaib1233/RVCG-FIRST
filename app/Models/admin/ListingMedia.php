<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ListingMedia extends Model
{
    use HasFactory;
    protected $table = "listing_media";

    protected $fillable = ['listing_id', 'file_name', 'file_url', 'media_type'];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
