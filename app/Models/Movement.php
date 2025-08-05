<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Movement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'created_by',
        'updated_by',
        'deleted_by',
        'product_id',
        'origin_loc_type',
        'origin_loc_id',
        'destiny_loc_type',
        'destiny_loc_id',
        'type',
        'amount',
    ];

    /**
     * Relationships
     */

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function originLoc(): MorphTo
    {
        return $this->morphTo();
    }

    public function destinyLoc(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected static function booted()
    {
        static::creating(fn ($model) => $model->created_by = auth()->user()?->id);
        static::updating(fn ($model) => $model->updated_by = auth()->user()?->id);
    }
}