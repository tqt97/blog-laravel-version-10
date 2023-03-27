<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    use HasTranslations;

    public $translatable = [];

    protected $fillable = ['name', 'slug', 'active', 'description', 'banner', 'parent_id', 'meta_title', 'meta_description'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
