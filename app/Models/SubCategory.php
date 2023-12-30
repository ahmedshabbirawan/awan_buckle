<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\StockItem;


class SubCategory extends Model
{
    use HasFactory,SoftDeletes;

    protected $table="sub_category";

    public function parentCategoryName(){
       return  $this->belongsTo('App\Models\ParentCategory','parentCategoryId','id');
    }


    public function stockItems(){
        return $this->hasMany(StockItem::class, 'sub_category_id');
    }


    



}
