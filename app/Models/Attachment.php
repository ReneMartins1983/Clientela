<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    /** @use HasFactory<\Database\Factories\AttachmentFactory> */
    use HasFactory;

    protected $fillable = ['client_id', 'name', 'path', 'size', 'mime'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /** Tamanho legível (KB/MB). */
    public function humanSize(): string
    {
        $bytes = $this->size;
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1).' MB';
        }

        return max(1, round($bytes / 1024)).' KB';
    }
}
