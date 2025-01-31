<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class propertyFeatures extends Model
{
    protected $table = "property_feature";
    protected $fillable = ['listings_id','feature_id'];
}
