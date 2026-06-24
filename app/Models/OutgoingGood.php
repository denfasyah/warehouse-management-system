<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutgoingGood extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'requested_by',
        'approved_by',
        'location_id',
        'quantity',
        'status',
        'reject_reason',
        'destination',
        'note',
        'requested_at',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'requested_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    // ─── Relationships ───────────────────────────────────────

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
