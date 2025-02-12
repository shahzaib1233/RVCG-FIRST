<?php
namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;  
use App\Models\admin\PropertyType;
use App\Models\admin\cities;  
use App\Models\admin\countries;
use App\Models\admin\PropertyStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'title', 'description','estimated_roi', 'city_id', 'country_id', 'property_type_id', 'property_status_id', 'listing_date', 'price', 'square_foot', 'parking', 'year_built', 'lot_size', 'longitude', 'latitude', 'school_district', 'walkability_score', 'crime_rate','gdrp_agreement', 'roi', 'area','monthly_rent','zip_code','geolocation_coordinates', 'cap_rate', 'address', 'bedrooms', 'bathrooms', 'half_bathrooms', 'arv', 'gross_margin','moa', 'user_id', 'is_featured','is_approved' , 'repair_cost' , 'wholesale_fee' , 'price_per_square_feet'];


public function city()
{
    return $this->belongsTo(Cities::class, 'city_id')->select('id', 'city_name');
}
protected $casts = [
    'is_approved' => 'boolean',
];
public function user()
{
    return $this->belongsTo(User::class, 'user_id')->select('id', 'name');
}

public function country()
{
    return $this->belongsTo(Countries::class, 'country_id')->select('id', 'country_name');
}

public function propertyType()
{
    return $this->belongsTo(PropertyType::class, 'property_type_id')->select('id', 'title');
}

public function propertyStatus()
{
    return $this->belongsTo(PropertyStatus::class, 'property_status_id')->select('id', 'status');
}

public function offers()
{
    return $this->hasMany(Offer::class, 'listing_id');
}


public function media()
{
    return $this->belongsTo(ListingMedia::class, 'listing_id')->select('id', 'listing_id', 'file_name', 'file_url');
}

public function features()
{
    return $this->belongsToMany(OtherFeature::class, 'property_feature', 'listings_id', 'feature_id');
}
// In Listing.php model
public function propertyFeatures()
{
    return $this->belongsToMany(propertyFeatures::class, 'property_feature', 'listings_id', 'feature_id');
}


public function kpis()
{
    return $this->hasOne(PropertyKpi::class);
}



}
