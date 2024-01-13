<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleCart extends Model{
    use HasFactory;


    protected $table        =   "sale_carts";
    protected $fillable     =   ['product_id', 'product_name', 'item_id', 'sale_key', 'qty','customer_id','shop_id', 'orignal_price','offer_price', 'final_price', 'status', 'created_by','updated_by','deleted_by' ];

}
