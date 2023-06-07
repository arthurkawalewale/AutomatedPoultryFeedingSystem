<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WaterModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function waterReadings(): HasMany
    {
        return $this->hasMany(WaterReading::class);
    }
}
