<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use HasFactory;

    public const STATUSES = ['lead', 'active', 'inactive'];

    public const STATUS_LABELS = [
        'lead' => 'Lead',
        'active' => 'Ativo',
        'inactive' => 'Inativo',
    ];

    protected $fillable = [
        'user_id', 'name', 'email', 'phone', 'company', 'status', 'notes', 'next_followup_at',
    ];

    protected $casts = [
        'next_followup_at' => 'datetime',
    ];

    /** Tem follow-up agendado e já venceu? */
    public function isFollowupOverdue(): bool
    {
        return $this->next_followup_at !== null && $this->next_followup_at->isPast();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class)->latest('happened_at');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class)->latest();
    }
}
