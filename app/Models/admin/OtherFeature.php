<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class OtherFeature extends Model
{
    protected $table = "other_features";
    protected $fillable = ['name'];
    //
    public function listings()
    {
        return $this->belongsToMany(Listing::class, 'property_feature', 'feature_id', 'listings_id');
    }
}
