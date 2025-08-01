<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_code',
        'name',
        'is_active',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($channel) {
            if (empty($channel->channel_code)) {
                $channel->channel_code = static::generateChannelCode();
            }
        });
    }

    public static function generateChannelCode()
    {
        $lastChannel = static::orderBy('id', 'desc')->first();
        $nextNumber = $lastChannel ? ((int) substr($lastChannel->channel_code, 1)) + 1 : 1;
        return 'C' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function salesmen()
    {
        return $this->hasMany(Salesman::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_channels');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
} 