<?php
namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;  
use App\Models\admin\PropertyType;
use App\Models\admin\cities;  
use App\Models\admin\countries;
use App\Models\admin\PropertyStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // Add this to use Auth
use Illuminate\Database\Eloquent\Builder;

class Listing extends Model
{
    use HasFactory;

    
    protected $fillable = ['id', 'title', 'description','estimated_roi', 'city_id', 'country_id', 'property_type_id', 'property_status_id', 'listing_date', 'price', 'square_foot', 'parking', 'year_built', 'lot_size', 'longitude', 'latitude', 'school_district', 'walkability_score', 'crime_rate','gdrp_agreement', 'roi', 'area','monthly_rent','zip_code','geolocation_coordinates', 'cap_rate', 'address', 'bedrooms', 'bathrooms', 'half_bathrooms', 'arv', 'gross_margin','moa', 'user_id', 'is_featured','is_approved' , 'repair_cost' , 'wholesale_fee' , 'price_per_square_feet' , 'owner_full_name', 'owner_age', 
        'owner_contact_number', 'owner_email_address', 'owner_government_id_proof', 
        'owner_property_ownership_proof', 'owner_ownership_type', 'owner_property_documents' , 'lead_types_id'
  ];

    protected $appends = ['Owner_Property_Documents_Url'];
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


public function getGdrpAgreementAttribute($value)
{
    return $value ? url($value) : null;
}


public function getOwnerPropertyDocumentsAttribute($value)
{
    return $value ? asset('uploads/Listings/Owner_Property_Documents/' . $value) : null;
}


public function setOwnerPropertyDocumentsAttribute($value)
{
    if (is_file($value)) {
        $filename = time() . '_' . $value->getClientOriginalName();
        $value->move(public_path('uploads/Listings/Owner_Property_Documents'), $filename);
        $this->attributes['owner_property_documents'] = 'uploads/Listings/Owner_Property_Documents/' . $filename;
    } else {
        $this->attributes['owner_property_documents'] = $value;
    }
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
    return $this->hasMany(ListingMedia::class, 'listing_id');
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




public function getArvAttribute()
{
    $avgPricePerSqFt = Listing::where('city_id', $this->city_id)
        ->where('property_type_id', $this->property_type_id)
        ->where('square_foot', '>', 0)
        ->avg('price_per_square_feet');  // Using saved price_per_square_feet column

    return $avgPricePerSqFt ? ($avgPricePerSqFt * $this->square_foot) : null;
}



    // Accessor for MOA (Maximum Offer Price)
    public function getMoaAttribute()
    {
        $arv = $this->arv;
        if ($arv > 0) {
            return ($arv * 0.70) - (($this->repair_cost + $this->wholesale_fee) ?? 0);
        }
        return 0;
    }

    // Accessor for ROI (Return on Investment)
public function getRoiAttribute()
{
    $arv = $this->arv;
    $totalInvestmentCost = $this->price + ($this->repair_cost ?? 0) + ($this->wholesale_fee ?? 0);

    $profit = $arv - $totalInvestmentCost;

    return $arv && $totalInvestmentCost > 0 ? ($profit / $totalInvestmentCost) * 100 : null;
}





public function getOwnerPropertyDocumentsUrlAttribute()
{
    if ($this->owner_property_documents) {
        return asset('uploads/listings/Owner_Property_Documents/' . $this->Owner_Property_Documents);
    }
    return null;
}

public function getHidden()
{
    // Check if the user is not logged in or not an admin
    if (!Auth::check() || (Auth::user()->role !== 'admin')) {
        // Check if the user has paid for this listing
        $hasAccess = Auth::check() && Skiptrace::where('user_id', Auth::id())
            ->where('listing_id', $this->id)
            ->where('is_paid', true) // Ensure that the payment is completed
            ->exists();

        // If user does not have access, hide sensitive fields
        if (!$hasAccess) {
            return [
                'owner_age', 'owner_contact_number',
                'owner_email_address', 'owner_government_id_proof',
                'owner_property_ownership_proof', 'owner_ownership_type',
                'owner_property_documents'
            ];
        }
    }

    // If user is admin or has access via skiptrace, show all fields
    return [];
}











// Listing.php (Model)




protected static function booted()
    {
        parent::boot();

        // Automatically eager load 'leadtypes' when querying listings
        static::addGlobalScope('leadtypes', function (Builder $builder) {
            $builder->with('leadtypes');
        });
    }

    public function leadtypes()
    {
        return $this->belongsTo(LeadType::class, 'lead_types_id');
    }



}
