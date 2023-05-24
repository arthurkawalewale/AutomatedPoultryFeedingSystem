<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class WaterReading
 * @package App\Models\WaterReading
 * @property int $id
 * @property string $reading
 */
class WaterReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'trough_reading','reservoir_reading'
    ];

    public function waterModel(): BelongsTo
    {
        return $this->belongsTo(WaterModel::class);
    }
}
