<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleOrder extends Model
{
    use HasFactory;
    protected $table        = "sale_orders";
    protected $fillable     = ['customer_id', 'sale_key','cash_received','cash_returned','item_count', 'total_offer_price', 'total_discount','shop_id', 'total_price', 'status', 'created_by']; 

    function shop(){
        return $this->belongsTo('App\Models\Shop','shop_id');
    }

    function saleOrderItem(){
        return $this->hasMany('App\Models\SaleOrderItem','order_id');
    }

}
