<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailCampaign extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'message', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
}
