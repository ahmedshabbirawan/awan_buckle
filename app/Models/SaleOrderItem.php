<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleOrderItem extends Model{
    
    use HasFactory;
    protected $table        = "sale_order_items";
    protected $fillable     = ['product_id', 'item_id', 'sale_key', 'order_id', 'customer_id', 'qty', 'offer_price', 'discount','cost_price', 'price', 'status']; 


    function product(){
        return $this->belongsTo(Product::class);
    }


    

}
