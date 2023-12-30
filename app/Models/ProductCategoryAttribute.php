<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategoryAttribute extends Model
{
    use HasFactory,SoftDeletes;
    protected $table="product_category_attributes";

    public function ProductCategoryAttributeName(){
        return $this->belongsTo('App\Models\AttributeType','attributeName','id');
        }

}
