<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Supplier;

use App\Traits\Definitions;
use App\Traits\CurdBy;
use App\Enums\Attri;

use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

// stock_deliveries_products

use App\Models\StockDeliveryProduct;

class StockDelivery extends Model implements Auditable{
    use HasFactory, Definitions, CurdBy, \OwenIt\Auditing\Auditable, SoftDeletes;

 

    protected $table="stock_deliveries";
    protected $fillable = ['rec_by_name', 'rec_by_designation', 'rec_by_cnic', 'rec_by_phone', 
                'hand_name', 'hand_designation','hand_cnic', 'hand_phone',
                'purchased_date', 'project_id', 'project_dg', 
                'po_loa_loi','amount_category','delivery_amount','supplier_id','delivery_challan_no','warehouse_id',
                'stock_ledger_reference',
                'delivery_challan_copy_file' , 'ledger_copy_file', 'purschase_copy_file', 'inspection_report_copy_file'
                ];



    function Supplier(){
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    function deliveryProduct(){
        return $this->hasMany(StockDeliveryProduct::class,'stock_delivery_id');
    }






}
