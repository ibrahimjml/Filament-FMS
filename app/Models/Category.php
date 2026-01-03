<?php

namespace App\Models;

use App\Enums\CategoryType;
use App\Traits\CategoryTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
  use HasFactory, SoftDeletes, CategoryTranslations;
  protected $table = "categories";
  protected $primaryKey = 'category_id';
  protected $dates = [
    'deleted_at',
    'created_at',
    'updated_at'
  ];
  protected $casts = [
    'category_type' => CategoryType::class
  ];
  protected $fillable = ['category_name', 'category_type'];

    public function subcategories()
  {
      return $this->hasMany(Subcategory::class, 'category_id');
  }
}
