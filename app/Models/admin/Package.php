<?php
namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price','is_featured','show_on_site','sequence', 'duration', 'created_at', 'updated_at'];

    public function items()
    {
        return $this->hasMany(PackageItem::class);
    }
}
