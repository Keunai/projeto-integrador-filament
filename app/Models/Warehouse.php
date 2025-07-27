<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by',
        'updated_by',
        'deleted_by',
        'name',
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

    protected static function booted()
    {
        static::creating(fn ($model) => $model->created_by = auth()->user()?->id);
        static::updating(fn ($model) => $model->updated_by = auth()->user()?->id);
        static::deleting(function ($model) {
            if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive(static::class))) {
                $model->forceFill(['deleted_by' => auth()->user()?->id])
                    ->save();
            }
        });
    }
}