<?php

namespace App\Models;

use App\Models\admin\Package;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    protected $table = 'subscriptions';

    protected $fillable = [
        'user_id',
        'package_id',
        'paid_amount',
        'date_of_subscription',
        'subscription_expiry_date',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the package that the subscription is for.
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
