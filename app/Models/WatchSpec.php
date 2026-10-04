<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatchSpec extends Model
{
    use HasFactory;

    protected $fillable = [
        'watch_id',
        'movement',
        'case_material',
        'case_diameter',
        'case_thickness',
        'dial_color',
        'crystal',
        'water_resistance',
        'power_reserve',
        'bracelet_material',
        'weight',
    ];

    public function watch(): BelongsTo
    {
        return $this->belongsTo(Watch::class);
    }
}
