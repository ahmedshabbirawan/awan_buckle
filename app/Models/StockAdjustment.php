<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\Definitions;
use App\Traits\CurdBy;
use App\Enums\Attri;

use App\Models\ProductAvailable;

use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockAdjustment extends Model implements Auditable{
    use HasFactory, Definitions, CurdBy, \OwenIt\Auditing\Auditable, SoftDeletes;

    protected $table    = "stock_adjustments";
    protected $fillable = ['product_id', 'shop_id', 'user_id', 'qty_when_adjust', 'qty', 'stock_type','created_by','description'
    ,'updated_by','deleted_by','deleted_at','created_at','updated_at']; 
    
    function product(){
        return $this->belongsTo('App\Models\Product','product_id');
    }

}