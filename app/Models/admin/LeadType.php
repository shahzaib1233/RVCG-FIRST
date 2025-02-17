<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadType extends Model
{
    use HasFactory;

    protected $fillable = ['type_name' , 'description'];

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}

