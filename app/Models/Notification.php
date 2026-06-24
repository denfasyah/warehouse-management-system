<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'data'    => 'array',
            'read_at' => 'datetime',
        ];
    }

    // ─── Relationships ───────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Accessors ───────────────────────────────────────────

    public function getIsReadAttribute(): bool
    {
        return $this->read_at !== null;
    }

    public function getIconAttribute(): string
    {
        return match ($this->type) {
            'approval_request' => 'pending_actions',
            'approved'         => 'check_circle',
            'rejected'         => 'cancel',
            'low_stock'        => 'warning',
            'reclassified'     => 'swap_horiz',
            default            => 'notifications',
        };
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ─── Static Helpers ──────────────────────────────────────

    /**
     * Buat notifikasi baru dengan mudah
     *
     * @param int    $userId  ID penerima
     * @param string $type    Tipe notifikasi
     * @param string $title   Judul notifikasi
     * @param string $message Isi pesan
     * @param array  $data    Data payload tambahan (opsional)
     */
    public static function send(int $userId, string $type, string $title, string $message, array $data = []): self
    {
        return static::create([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'data'    => $data,
        ]);
    }
}
