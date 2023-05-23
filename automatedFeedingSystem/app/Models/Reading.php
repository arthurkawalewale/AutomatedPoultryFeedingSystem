<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * Class Reading
 * @package App\Models\Reading
 * @property int $id
 * @property string $reading
 */
class Reading extends Model
{
    use HasFactory;

    protected $fillable = [
        'reading',
    ];

    public function reservoir(): BelongsTo
    {
        return $this->belongsTo(Reservoir::class);
    }
}
