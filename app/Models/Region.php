<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'region_code',
        'name',
        'is_active',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($region) {
            if (empty($region->region_code)) {
                $region->region_code = static::generateRegionCode();
            }
        });
    }

    public static function generateRegionCode()
    {
        $lastRegion = static::orderBy('id', 'desc')->first();
        $nextNumber = $lastRegion ? ((int) substr($lastRegion->region_code, 1)) + 1 : 1;
        return 'R' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function channels()
    {
        return $this->hasMany(Channel::class);
    }

    public function salesmen()
    {
        return $this->hasMany(Salesman::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_regions');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
} 