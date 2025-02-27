<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyValuation extends Model {
    use HasFactory;

    protected $fillable = [
        'title', 'address', 'city', 'country', 'property_type', 'price',
        'square_foot', 'bedrooms', 'bathrooms', 'property_images',
        'owner_name', 'owner_age', 'ownership_type', 'owner_email',
        'govt_id_proof', 'owner_contact'
    ];

    protected $casts = [
        'property_images' => 'array',
    ];



    public function getPropertyImagesAttribute($value)
{
    if (!$value) {
        return [];
    }

    // Decode the JSON (handle cases where it is double-encoded)
    $decoded = json_decode($value, true);

    // If it's still a string, decode it again
    if (is_string($decoded)) {
        $decoded = json_decode($decoded, true);
    }

    // Ensure it's an array and map URLs
    return is_array($decoded) ? array_map(fn($image) => url($image), $decoded) : [];
}



}
