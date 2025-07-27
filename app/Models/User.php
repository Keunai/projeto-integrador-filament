<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'password',
        'created_by',
        'updated_by',
        'deleted_by',
        'active',
        'responsabilities',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'active' => 'boolean',
        'password' => 'hashed',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'admin' && auth()->check();
    }

    public function creator()
    {
        return $this->belongsTo(self::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(self::class, 'updated_by');
    }

    public function remover() {
        return $this->belongsTo(self::class, 'deleted_by');
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