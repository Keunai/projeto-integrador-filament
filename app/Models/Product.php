<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'created_by',
        'updated_by',
        'deleted_by',
        'category_id',
        'locationable_id',
        'locationable_type',
        'batch_id',
        'status_id',
        'name',
        'rotation',
        'rotation_type',
        'type',
        'amount',
        'code',
        'description',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function locationable()
    {
        return $this->morphTo();
    }

    public function batch()
    {
        return $this->belongsTo(self::class, 'batch_id');
    }

    public function units()
    {
        return $this->hasMany(self::class, 'batch_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    protected static function booted()
    {
        static::saving(function ($model) {
            if ($model->type === \App\Enums\ProductTypes::UNIT) {
                $model->amount = 1;
            }
        });

        static::creating(fn ($model) => $model->created_by = auth()->user()?->id);
        static::updating(fn ($model) => $model->updated_by = auth()->user()?->id);
        static::deleting(function ($model) {
            if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive(static::class))) {
                $model->forceFill(['deleted_by' => auth()->user()?->id])
                    ->save();
            }
        });
    }

    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }
}