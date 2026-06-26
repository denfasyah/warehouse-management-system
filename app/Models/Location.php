<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'zone',
        'rack',
        'bin',
        'code',
        'storage_class',
        'capacity',
        'current_fill',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    // ─── Relationships ───────────────────────────────────────

    public function items()
    {
        return $this->belongsToMany(Item::class)->withPivot('quantity')->withTimestamps();
    }

    public function incomingGoods()
    {
        return $this->hasMany(IncomingGood::class);
    }

    public function outgoingGoods()
    {
        return $this->hasMany(OutgoingGood::class);
    }

    // ─── Accessors ───────────────────────────────────────────

    /** Persentase pengisian lokasi (0–100) */
    public function getFillPercentageAttribute(): int
    {
        if ($this->capacity === 0) return 0;
        return (int) min(100, round(($this->current_fill / $this->capacity) * 100));
    }

    /** Apakah lokasi sudah penuh */
    public function getIsFullAttribute(): bool
    {
        return $this->current_fill >= $this->capacity;
    }

    /** Label warna kelas CBS untuk UI */
    public function getClassColorAttribute(): string
    {
        return match ($this->storage_class) {
            'fast'    => 'red',
            'medium'  => 'yellow',
            'slow'    => 'green',
            'general' => 'blue',
            default   => 'gray',
        };
    }

    /** Apakah ini zona Bulk Storage (Timbunan Overflow) */
    public function getIsBulkZoneAttribute(): bool
    {
        return $this->zone === 'BLK';
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByClass($query, string $class)
    {
        return $query->where('storage_class', $class);
    }

    public function scopeNotFull($query)
    {
        return $query->whereColumn('current_fill', '<', 'capacity');
    }

    // ─── Static Helpers ──────────────────────────────────────

    /** Generate kode lokasi dari zone, rack, bin */
    public static function generateCode(string $zone, string $rack, ?string $bin = ''): string
    {
        $code = strtoupper($zone) . '-' . str_pad($rack, 2, '0', STR_PAD_LEFT);
        if ($bin && $bin !== '') {
            $code .= '-' . str_pad($bin, 2, '0', STR_PAD_LEFT);
        }
        return $code;
    }
}
