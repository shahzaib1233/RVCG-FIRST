<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempData extends Model
{
    protected $fillable = ['file_name', 'file_url', 'file_type'];

}
