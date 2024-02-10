<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    use HasFactory;
    protected $table        = "purchase_return";
    protected $fillable     = [ 'supplier_id', 'purchase_id', 'shop_id', 'total_amount', 'description', 'status', 'created_by', 'updated_by', 'deleted_by', 'deleted_at', 'created_at', 'updated_at'];

}
