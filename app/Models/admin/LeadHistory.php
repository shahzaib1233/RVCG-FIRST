<?php

namespace App\Models\admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadHistory extends Model
{
    use HasFactory;

    protected $table="lead_history";
    protected $fillable = [
        'lead_id',
        'contact_date',
        'note',
        'follow_up_date',
    ];

    // Relationship with Lead
    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }
}