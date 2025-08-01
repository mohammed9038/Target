<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActiveMonthYear extends Model
{
    use HasFactory;

    protected $table = 'active_months_years';

    protected $fillable = [
        'year',
        'month',
        'is_open',
    ];

    protected $casts = [
        'is_open' => 'boolean',
    ];

    public function scopeOpen($query)
    {
        return $query->where('is_open', true);
    }

    public function scopeClosed($query)
    {
        return $query->where('is_open', false);
    }

    public function getPeriodAttribute()
    {
        return sprintf('%04d-%02d', $this->year, $this->month);
    }
} 