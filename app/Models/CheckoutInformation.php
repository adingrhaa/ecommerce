<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckoutInformation extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'ringkasan_belanja' => 'array',
    ];
}
