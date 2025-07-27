<?php

namespace App\Models;

use Filament\Support\Concerns\HasColor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Status extends Model
{
    protected $table = 'statuses';

    protected $fillable = [
        'created_by',
        'updated_by',
        'name',
        'description',
    ];

    public function products()
    {
        return $this->hasMany(Status::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected static function booted()
    {
        static::creating(fn ($model) => $model->created_by = auth()->user()?->id);
        static::updating(fn ($model) => $model->updated_by = auth()->user()?->id);
    }
}