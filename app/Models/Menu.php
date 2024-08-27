<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Menu extends Model {
    use HasFactory;

    protected $fillable = [
        'name',
        'restaurant_id',
        'description',
    ];

    public function plates(): BelongsToMany {
        return $this->belongsToMany(Plate::class, 'menus_plates');
    }
}
