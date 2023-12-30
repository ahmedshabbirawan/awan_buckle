<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ProductCategory extends Model
{
    use HasFactory,SoftDeletes;
    protected $table="product_category";

    public function parentCategoryName(){
        return  $this->belongsTo('App\Models\ParentCategory','parentCategoryId','id');
     }
     public function parentSubCategoryName(){
        return  $this->belongsTo('App\Models\SubCategory','subCategoryId','id');
     }
     public function ProductCategoryAttributes(){
        return  $this->hasMany('App\Models\ProductCategoryAttribute','productCategoryId','id');
     }



}
