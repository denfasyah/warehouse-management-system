<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'barcode',
        'barcode_image',
        'unit',
        'stock',
        'min_stock',
        'storage_class',
        'frequency_score',
        'description',
    ];

    /**
     * Gunakan slug sebagai route key (bukan id)
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ─── Relationships ───────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class)->withPivot('quantity')->withTimestamps();
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

    /** Apakah stok di bawah minimum */
    public function getIsLowStockAttribute(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    /** Label kelas CBS untuk tampilan UI */
    public function getClassLabelAttribute(): string
    {
        return match ($this->storage_class) {
            'fast'         => 'Fast Moving',
            'medium'       => 'Medium Moving',
            'slow'         => 'Slow Moving',
            default        => 'Belum Diklasifikasi',
        };
    }

    /** Warna badge kelas CBS */
    public function getClassColorAttribute(): string
    {
        return match ($this->storage_class) {
            'fast'    => 'red',
            'medium'  => 'yellow',
            'slow'    => 'green',
            default   => 'gray',
        };
    }

    /** Apakah lokasi barang sesuai dengan kelas CBS-nya */
    public function getIsLocationMismatchAttribute(): bool
    {
        if ($this->locations->isEmpty() || $this->storage_class === 'unclassified') {
            return false;
        }
        
        // If any location doesn't match the storage class, return true (mismatch)
        foreach ($this->locations as $loc) {
            if ($loc->storage_class !== $this->storage_class) {
                return true;
            }
        }
        return false;
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'min_stock');
    }

    public function scopeByClass($query, string $class)
    {
        return $query->where('storage_class', $class);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('sku', 'like', "%{$term}%")
              ->orWhere('barcode', 'like', "%{$term}%");
        });
    }

    // ─── Static Helpers ──────────────────────────────────────

    /**
     * Generate SKU otomatis: {KODE_KAT}-{6_DIGIT_RANDOM}
     * Contoh: ELK-738291
     */
    public static function generateSku(string $categoryCode): string
    {
        do {
            $sku = strtoupper($categoryCode) . '-' . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (static::where('sku', $sku)->exists());

        return $sku;
    }

    /**
     * Generate slug unik dari nama barang
     */
    public static function generateSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = \Illuminate\Support\Str::slug($name);
        $originalSlug = $slug;
        $count = 1;
        while (static::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        return $slug;
    }

    /**
     * Generate nilai barcode: WMS{12_DIGIT_RANDOM}
     * Format Code128 — selalu unik
     */
    public static function generateBarcode(): string
    {
        do {
            $barcode = 'WMS' . str_pad(random_int(0, 999999999999), 12, '0', STR_PAD_LEFT);
        } while (static::where('barcode', $barcode)->exists());

        return $barcode;
    }
}
