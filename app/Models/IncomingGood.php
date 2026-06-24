<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingGood extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'user_id',
        'location_id',
        'quantity',
        'note',
        'received_at',
    ];

    protected function casts(): array
    {
        return ['received_at' => 'datetime'];
    }

    // ─── Relationships ───────────────────────────────────────

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeToday($query)
    {
        return $query->whereDate('received_at', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('received_at', now()->month)
                     ->whereYear('received_at', now()->year);
    }
}
