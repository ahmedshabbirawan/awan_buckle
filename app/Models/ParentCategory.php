<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\SubCategory;
use App\Models\StockItem;



class ParentCategory extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "parent_category";



    function subCategories(){
        return $this->hasMany(SubCategory::class,'parentCategoryId');
    }

    function subCategoriesHasItems(){
        // return $this->hasMany(SubCategory::class,'parentCategoryId')->with('stockItems');
        return $this->hasManyThrough(SubCategory::class, StockItem::class, 'sub_category_id','id')->withCount('stockItems');
    }


   
}
