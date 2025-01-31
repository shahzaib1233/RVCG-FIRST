<?php

namespace App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class LeadSource extends Model
{
    use HasFactory;

    protected $fillable = ['title'];

    // A lead source can have many leads
    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}
