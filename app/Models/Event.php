<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'name',
        'event_date',
        'total_stalls',
        'status',
    ];

    public function groups(): HasMany
    {
        return $this->hasMany(ParticipantGroup::class, "event_id", "id");
    }
}
