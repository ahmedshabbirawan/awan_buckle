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


    public static function manageStockByShopAndProductId(int $chageQuantity, $action, $productId, $shopId , $objectType = null, $objectId = null){
        $oldStock = ProductAvailable::where([
            'shop_id' => $shopId,
            'product_id' => $productId
        ])->first();
        $newQty = 0;
        $oldQty = ($oldStock)?$oldStock->qty:0;

        if($action == StockAudit::STOCK_ACTION_ADD){
            $newQty = $oldQty + $chageQuantity;
        }elseif($action == StockAudit::STOCK_ACTION_MINUS){
            $newQty = $oldQty - $chageQuantity;
        }
        if($oldStock){
            $newStock = ProductAvailable::where([
                'shop_id' => $shopId,
                'product_id' => $productId
            ])->first();
            $newStock->qty = $newQty;
            $newStock->save();
        }else{
            $newStock = ProductAvailable::create([
                'qty' => $chageQuantity,
                'product_id' => $productId,
                'shop_id' => $shopId 
            ]);
        }    

        $oldValue = json_encode(($oldStock)?$oldStock->toArray():null);
        $newValue = json_encode(($newStock)?$newStock->toArray():null);

        if($newStock){
            StockAudit::create([
                'product_id' => $productId,
                'shop_id' => $shopId,
                'old_qty' => $oldQty,
                'update_qty' => $chageQuantity,
                'current_qty' => $newStock->qty,
                'action' => $action,
                'stock_old_value' => $oldValue,
                'stock_new_value' => $newValue,
                'object_type' => $objectType,
                'object_id' => $objectId
            ]);
        }
        
        return $newStock;
    }

    public static function incrementStock( Array $whereArr, $qty ){
        return static::query()->where($whereArr)->increment('qty', $qty);
    }



}
