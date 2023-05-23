<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * Class Reservoir
 * @package App\Models\Reservoir
 * @property int $id
 * @property string $name
 */
class Reservoir extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function Readings(): HasMany
    {
        return $this->hasMany(Reading::class);
    }
}
