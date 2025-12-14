<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Amenity extends Model
{
    /** @use HasFactory<\Database\Factories\AmenityFactory> */
    use HasFactory;

    protected $fillable = ['name', 'description', 'capacity'];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
