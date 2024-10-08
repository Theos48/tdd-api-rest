<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plate extends Model {
    use HasFactory;

    protected $fillable = [
        'name',
        'restaurant_id',
        'name',
        'price',
        'description',
    ];
}
