<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockDeliveryItem extends Model
{
    use HasFactory;

    protected $table="stock_deliveries_items";


    function product(){
        return $this->belongsTo('App\Models\Product','product_id');
    }


    function uom(){
        return $this->belongsTo('App\Models\Uom','uom_id');
    }

}
