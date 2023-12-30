<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Product;
use App\Models\SubCategory;
use App\Models\IssuanceItem;
use App\Models\Shop;

use App\Traits\Definitions;

class StockItem extends Model{


    use HasFactory, Definitions;

    protected $table    =   "stock_items";
    protected $appends  =   ['condition_status_label']; 
    


    protected $fillable = ['stock_delivery_id','available_qty','qty','stock_delivery_product_id','sub_category_id','project_id',
    'stock_delivery_item_id','product_cat_id','sub_category','product_id','serial_number','tag', 'status','condition_status','shop_id',
    'created_by','updated_by','deleted_by','deleted_at','created_at','updated_at']; 


    protected static function booted() {
        parent::boot();

        static::created(function (StockItem $stockItem) {
            $proAvail = ProductAvailable::where(
                ['product_id' => $stockItem->product_id, 'shop_id' => $stockItem->shop_id]
            )->first();

            if($proAvail){
                $proAvail->qty = $proAvail->qty + $stockItem->qty;
                $proAvail->save();
            }else{
                ProductAvailable::create(
                    ['product_id' => $stockItem->product_id, 'shop_id' => $stockItem->shop_id, 'qty' => $stockItem->qty]
                );
            }
        });

        // static::creating(function (&$model) {
        //     //$model->created_by = auth()->user()->id;
        // });

        // static::updating(function (&$model) {
        //     // $model->updated_by = auth()->user()->id;
        // });
        // static::deleting(function (&$model) {
        //     // $model->delete_by = auth()->user()->id;
        // });
    }

    function product(){
        return $this->belongsTo(Product::class,'product_id');
    }

    function project(){
        return $this->belongsTo(Project::class,'project_id');
    }

    function subCategory(){
        return $this->belongsTo(SubCategory::class,'sub_category_id');
    }


    function shop(){
        return $this->belongsTo(Shop::class,'shop_id');
    }

    function itemProduct(){
        return $this->belongsTo(StockDeliveryProduct::class,'stock_delivery_product_id');
    }


    function issuanceItem(){
        return $this->hasMany(IssuanceItem::class,'item_id');
    }

    function holderInfo(){
        return $this->hasOne(IssuanceItem::class,'item_id')->whereNull('return_date');
    }

    function stockDelivery(){
        return $this->belongsTo(StockDelivery::class,'stock_delivery_id');
    }


    public function getConditionStatusLabelAttribute($value){
        return self::conditionLabel($this->condition_status);
    }
    

}
