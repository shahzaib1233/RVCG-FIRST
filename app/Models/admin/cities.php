<?php
namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cities extends Model
{
    use HasFactory;
    protected $table = 'cities';

    protected $fillable = ['city_name', 'country_id', 'latitude', 'longitude'];

    public function country()
    {
        return $this->belongsTo(\App\Models\admin\countries::class, 'country_id');
    }
    
}

