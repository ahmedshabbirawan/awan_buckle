<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleCart extends Model{
    use HasFactory;


    protected $table        =   "sale_carts";
    protected $fillable     =   ['product_id', 'product_name', 'item_id', 'sale_key', 'qty','customer_id','shop_id', 'vehicle_no', 'bilty_no', 'vehicle_number', 'bilty_number', 'orignal_price','offer_price', 'discount', 'commission_percentage', 'commission_amount', 'is_commission_applied', 'final_price', 'status', 'created_by','updated_by','deleted_by' ];

    public function getVehicleNumberAttribute()
    {
        return $this->vehicle_no;
    }

    public function setVehicleNumberAttribute($value)
    {
        $this->attributes['vehicle_no'] = $value;
    }

    public function getBiltyNumberAttribute()
    {
        return $this->bilty_no;
    }

    public function setBiltyNumberAttribute($value)
    {
        $this->attributes['bilty_no'] = $value;
    }

    protected $casts = [
        'commission_percentage' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'is_commission_applied' => 'boolean',
    ];

}
