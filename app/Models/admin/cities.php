<?php
namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cities extends Model
{
    use HasFactory;

    protected $fillable = ['city_name', 'country_id']; // country_id is now fillable

    public function country()
    {
        return $this->belongsTo(\App\Models\admin\countries::class); // Define the relationship to Countries
    }
}

