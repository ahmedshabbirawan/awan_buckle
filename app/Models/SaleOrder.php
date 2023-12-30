<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleOrder extends Model
{
    use HasFactory;
    protected $table        = "sale_orders";
    protected $fillable     = ['customer_id', 'sale_key','item_count', 'total_offer_price', 'total_discount', 'total_price', 'status']; 
    


}
