<?php

namespace YourVendor\Seat\EspionageDetector\Models;

use Illuminate\Database\Eloquent\Model;
use Seat\Eveapi\Models\Character\CharacterInfo;

class ScreeningResult extends Model
{
    protected $table = 'espionage_screening_results';

    protected $fillable = [
        'character_id',
        'entity_id',
        'entity_type',
        'entry_type',
        'entry_date',
        'context'
    ];

    public function character()
    {
        return $this->belongsTo(CharacterInfo::class, 'character_id', 'character_id');
    }
}