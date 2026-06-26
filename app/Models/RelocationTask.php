<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelocationTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'from_location_id',
        'to_location_id',
        'quantity',
        'status',
        'assigned_to',
        'completed_by',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function fromLocation()
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation()
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
