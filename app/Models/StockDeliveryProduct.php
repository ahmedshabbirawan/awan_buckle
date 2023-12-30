<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\Definitions;
use App\Traits\CurdBy;
use App\Enums\Attri;

use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockDeliveryProduct extends Model implements Auditable{
    use HasFactory, Definitions, CurdBy, \OwenIt\Auditing\Auditable, SoftDeletes;

    protected $table="stock_deliveries_products";

    protected $fillable = ['stock_delivery_id','shop_id','parent_cat_id','sub_cat_id','product_cat_id','product_id','project_id','uom_id','uom_code','quantity','unit_price',
    'warranty_date','serial_numbers', 'status','issue_qty',
    'created_by','updated_by','deleted_by','deleted_at','created_at','updated_at']; 


    function product(){
        return $this->belongsTo('App\Models\Product','product_id');
    }


    function uom(){
        return $this->belongsTo('App\Models\Uom','uom_id');
    }

    // serial_numbers
}
