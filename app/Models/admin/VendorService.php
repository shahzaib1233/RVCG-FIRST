<?php

namespace App\Models\admin;

use App\Models\admin\Vendor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorService extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'service_name',
        'description',
        'price',
        'service_city',
    ];

    /**
     * Get the vendor that owns the service.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}