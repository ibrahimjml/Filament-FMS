<?php

namespace App\Models;

use App\Enums\CategoryType;
use App\Traits\SubcategoryTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subcategory extends Model
{
    use HasFactory, SoftDeletes, SubcategoryTranslations;

    protected $table = 'subcategories';

    protected $primaryKey = 'subcategory_id';

    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $fillable = ['sub_name', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function income(): HasMany
    {
        return $this->hasMany(Income::class, 'subcategory_id');
    }

    public function scopeIncome(Builder $query)
    {
        return $query->whereHas('category', fn ($q) => $q->where('category_type', CategoryType::INCOME->value)
        );
    }

    public function scopeOutcome(Builder $query)
    {
        return $query->whereHas('category', fn ($q) => $q->where('category_type', CategoryType::OUTCOME->value)
        );
    }
     public static function searchByCategoryName(string $search): Builder
    {
        return static::query()
            ->whereHas('category', function (Builder $q) use ($search) {
                $q->where('category_type', CategoryType::INCOME)
                  ->where('name', 'like', "%{$search}%");
            })
            ->with('category');
    }
}
