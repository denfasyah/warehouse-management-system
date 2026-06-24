<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    // ─── Relationships ───────────────────────────────────────

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    // ─── Accessors ───────────────────────────────────────────

    public function getItemsCountAttribute(): int
    {
        return $this->items()->count();
    }
}
