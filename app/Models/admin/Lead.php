<?php

namespace App\Models\admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'added_by',
        'assigned_to',
        'special_notes',
        'status',
        'lead_type_id',
        'source',
        'tags',
        'position',
        'email',
        'website',
        'phone',
        'lead_value',
        'company',
        'address',
        'city',
        'state',
        'country',
        'zip_code',
        'default_language',
        'description',
        'contacted_today',
        'lead_source_id',
    ];

    // Cast tags to array
    protected $casts = [
        'tags' => 'array',
        'contacted_today' => 'boolean',
    ];

    // Relationship with User (Added by)
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    // Relationship with User (Assigned to)
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Relationship with LeadType
    public function leadType()
    {
        return $this->belongsTo(LeadType::class, 'lead_type_id');
    }

    // Relationship with LeadHistory
    public function leadHistories()
    {
        return $this->hasMany(LeadHistory::class, 'lead_id');
    }


    public function leadSource()
    {
        return $this->belongsTo(LeadSource::class,'lead_source_id');
    }


}
