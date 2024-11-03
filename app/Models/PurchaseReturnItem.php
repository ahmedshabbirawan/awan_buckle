<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnItem extends Model
{
    use HasFactory;
    protected $table        = "purchase_return_items";
    protected $fillable     = [ 'purchase_return_id', 'product_id', 'shop_id', 'unit_price','item_description', 'qty', 'status', 'created_by', 'updated_by', 'deleted_by', 'deleted_at', 'created_at', 'updated_at'];

    function product(){
        return $this->belongsTo(Product::class);
    }

}
