<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleOrder extends Model
{
    use HasFactory;
    protected $table        = "sale_orders";
    protected $fillable     = [
        'customer_id', 
        'sale_key',
        'cash_received',
        'cash_returned',
        'item_count', 
        'total_offer_price', 
        'total_discount',
        'total_commission',
        'shop_id', 
        'total_price', 
        'total_amount',
        'paid_amount',
        'due_amount',
        'payment_status',
        'payment_method',
        'status', 
        'created_by'
    ]; 

    protected $casts = [
        'total_commission' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
    ];

    function customer(){
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    function shop(){
        return $this->belongsTo('App\Models\Shop','shop_id');
    }

    function saleOrderItem(){
        return $this->hasMany('App\Models\SaleOrderItem','order_id');
    }

    function ledgerTransaction(){
        return $this->hasOne(LedgerTransaction::class, 'reference_id')
            ->where('reference_type', LedgerTransaction::REF_SALE);
    }

}
