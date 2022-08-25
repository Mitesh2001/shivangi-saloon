<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'external_id',
        'name',
        'parent_id',
        'created_by',
        'updated_by'
    ];
 
    // public function product() 
    // {
    //     return $this->belongsTo(Product::class, 'id', 'category_id');
    // }
    
    public function products() {
        return $this->belongsToMany(Product::class, 'product_categories');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
