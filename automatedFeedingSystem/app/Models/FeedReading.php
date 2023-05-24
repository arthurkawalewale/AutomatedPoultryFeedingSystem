<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
* Class FeedReading
 * @package App\Models\FeedReading
* @property int $id
* @property string $trough_reading
* @property string $reservoir_reading
*/

class FeedReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'trough_reading','reservoir_reading'
    ];

    public function feedModel(): BelongsTo
    {
        return $this->belongsTo(FeedModel::class);
    }
}
