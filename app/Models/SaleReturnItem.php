<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class SaleReturnItem extends Model
{
    use HasFactory;
    protected $table        = "sale_return_items";
    protected $fillable     = ['sale_return_id', 'product_id', 'shop_id', 'qty', 'status', 'created_by', 'updated_by', 'deleted_by', 'deleted_at', 'created_at', 'updated_at'];


    public function product(){
        return $this->belongsTo(Product::class);
    }

}
