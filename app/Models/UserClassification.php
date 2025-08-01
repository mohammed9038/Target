<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserClassification extends Model
{
    protected $fillable = [
        'user_id',
        'classification',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}