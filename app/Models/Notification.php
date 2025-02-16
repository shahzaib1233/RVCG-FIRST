<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    // Table Name (if different from model name)
    protected $table = 'notifications';

    // Fillable Fields
    protected $fillable = [
        'heading',
        'title',
        'read',
        'redirect_link'
    ];
}
