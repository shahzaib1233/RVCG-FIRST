<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Countries extends Model
{
    protected $fillable = ['country_name'];

    use HasFactory;

    public function cities()
    {
        return $this->hasMany(\App\Models\admin\Cities::class);
    }
}
