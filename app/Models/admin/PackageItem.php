<?php

namespace App\Models\admin;
use App\Models\admin\Package;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageItem extends Model
{
    use HasFactory;
    protected $fillable = ['package_id', 'item_name'];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
