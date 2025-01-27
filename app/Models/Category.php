<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $appends = ['category_count'];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    // Accessor for category_count
    public function getCategoryCountAttribute()
    {
        return $this->products()->count();
    }
}
