<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesmanClassification extends Model
{
    protected $fillable = [
        'salesman_id',
        'classification',
    ];

    public function salesman()
    {
        return $this->belongsTo(Salesman::class);
    }
}