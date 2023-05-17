<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class WaterTank
 * @package App\Models\WaterTank
 * @property int $id
 * @property string $name
 */
class WaterTank extends Model
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
