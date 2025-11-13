<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParticipantGroup extends Model
{
    protected $fillable = [
        'name',
        'event_id',
        'total_member',
    ];


    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id', 'id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class, "participant_group_id", "id");
    }
}
