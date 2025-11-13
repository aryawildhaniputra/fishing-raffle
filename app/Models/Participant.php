<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Participant extends Model
{
    protected $fillable = [
        'name',
        'participant_groups_id',
        'stall_number',
    ];

    public function participantGroup(): BelongsTo
    {
        return $this->belongsTo(ParticipantGroup::class, 'group_id', 'id');
    }
}
