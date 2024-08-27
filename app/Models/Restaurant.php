<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static find(int $int)
 */
class Restaurant extends Model {
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'slug',
        'description',
    ];

    public function plates(): HasMany {
        return $this->hasMany(Plate::class);
    }

    public function menus(): HasMany {
        return $this->hasMany(Menu::class);
    }
}
