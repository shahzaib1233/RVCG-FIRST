<?php
namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cities extends Model
{
    use HasFactory;
    protected $table = 'cities';

    protected $fillable = ['city_name', 'country_id', 'latitude', 'longitude' , 'img'];

    public function country()
    {
        return $this->belongsTo(\App\Models\admin\countries::class, 'country_id');
    }


    public function getImgAttribute($value)
    {
        return $value ? url($value) : null;
    }

    public function listings()
    {
        return $this->hasMany(Listing::class, 'city_id');
    }
    


}

