<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckoutHistory extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'ringkasan_belanja' => 'array', 
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function checkoutInformation()
    {
        return $this->belongsTo(CheckoutInformation::class);
    }
}
