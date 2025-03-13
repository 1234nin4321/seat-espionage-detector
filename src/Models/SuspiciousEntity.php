<?php

namespace YourVendor\Seat\EspionageDetector\Models;

use Illuminate\Database\Eloquent\Model;

class SuspiciousEntity extends Model
{
    protected $table = 'espionage_suspicious_entities';

    protected $fillable = [
        'entity_id', 
        'entity_type',
        'notes'
    ];

    protected $casts = [
        'entity_id' => 'integer'
    ];
}