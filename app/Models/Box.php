<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Box extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'code',
        'qr_uuid',
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

    /**
     * @return array<int, int>
     */
    public function descendantIds(): array
    {
        $this->loadMissing('children');

        return $this->children
            ->flatMap(fn (Box $child): array => [$child->id, ...$child->descendantIds()])
            ->all();
    }

    /**
     * @return Collection<int, Box>
     */
    public function ancestorTrail(): Collection
    {
        $ancestors = collect();
        $parent = $this->parent;

        while ($parent) {
            $ancestors->prepend($parent);
            $parent = $parent->parent;
        }

        return $ancestors;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'unpacked' => __('boxes.unpacked'),
            default => __('boxes.packed'),
        };
    }
}
