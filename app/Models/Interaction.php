<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interaction extends Model
{
    /** @use HasFactory<\Database\Factories\InteractionFactory> */
    use HasFactory;

    public const TYPES = ['call', 'email', 'meeting', 'whatsapp', 'note'];

    public const TYPE_LABELS = [
        'call' => 'Ligação',
        'email' => 'E-mail',
        'meeting' => 'Reunião',
        'whatsapp' => 'WhatsApp',
        'note' => 'Anotação',
    ];

    protected $fillable = [
        'client_id', 'type', 'notes', 'happened_at',
    ];

    protected $casts = [
        'happened_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
