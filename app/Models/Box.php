<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Box extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'code',
        'qr_uuid',
        'title',
        'description',
        'level',
        'status',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('code');
    }

    public function descendants(): HasMany
    {
        return $this->children()->with('descendants.images');
    }

    public function images(): HasMany
    {
        return $this->hasMany(BoxImage::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'moving' => __('boxes.moving'),
            'unpacked' => __('boxes.unpacked'),
            default => __('boxes.packed'),
        };
    }
}
