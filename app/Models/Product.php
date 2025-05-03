<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'created_by',
        'updated_by',
        'deleted_by',
        'category_id',
        'locationable_id',
        'locationable_type',
        'name',
        'rotation',
        'type',
        'amount',
        'code',
        'description'
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
}