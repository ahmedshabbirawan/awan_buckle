<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Product;
use App\Models\Shop;

use App\Traits\Definitions;

class ProductAvailable extends Model{


    use HasFactory, Definitions;

    protected $table    =   "product_available";
    
    protected $fillable = ['qty','product_id','shop_id']; 

    function product(){
        return $this->belongsTo(Product::class,'product_id');
    }

    function shop(){
        return $this->belongsTo(Shop::class,'shop_id');
    }


    public static function manageStockByShopAndProductId(int $chageQuantity, $action, $productId, $shopId , $objectType = null, $objectId = null, $description = null, bool $allowNegative = false){
        return \App\Services\InventoryService::mutateStock(
            (int)$productId,
            (int)$shopId,
            (int)$chageQuantity,
            $action,
            $objectType ?? 'Manual',
            $objectId ? (int)$objectId : null,
            $description,
            $allowNegative
        );
    }



}
