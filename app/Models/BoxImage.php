<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoxImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'box_id',
        'image_path',
    ];

    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }
}
