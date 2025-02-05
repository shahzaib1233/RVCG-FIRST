<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Admin\EmailCampaign;
use App\Models\admin\Offer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'referrer_id',
        'referral_code',
        'phone_number',  // Add phone number
        'address',  // Add address
        'social_media_profiles',  // Add social media profiles
        'bankruptcy_details',  // Add bankruptcy details
        'liens_details',  // Add liens details
        'contact_email',  // Add contact email
        'dob',  // Add date of birth
        'income_level', // Add income level
        'referrers',
        'reward_points',
        'is_active',
    ];
    protected $casts = [
        'social_media_profiles' => 'array', // Social profiles stored as JSON
        'referrers' => 'array', // Referrers stored as JSON
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

public function posts()
{
    return $this->hasMany(Post::class);
}

    
public function offers()
{
    return $this->hasMany(Offer::class, 'user_id');
}
public function emailCampaigns()
{
    return $this->hasMany(EmailCampaign::class, 'user_id');
}

}
