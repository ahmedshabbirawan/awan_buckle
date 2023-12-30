<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Product;
use App\Models\SubCategory;
use App\Models\IssuanceItem;
use App\Models\Shop;

use App\Traits\Definitions;

class ProductAvailable extends Model{


    use HasFactory, Definitions;

    protected $table    =   "product_available";
  //   protected $appends  =   ['condition_status_label']; 
    


    protected $fillable = ['qty','product_id','shop_id']; 


    function product(){
        return $this->belongsTo(Product::class,'product_id');
    }

    function shop(){
        return $this->belongsTo(Shop::class,'shop_id');
    }
}
