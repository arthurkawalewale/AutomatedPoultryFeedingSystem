<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'reading',
    ];

    public function feedModel(): BelongsTo
    {
        return $this->belongsTo(FeedModel::class);
    }
}
