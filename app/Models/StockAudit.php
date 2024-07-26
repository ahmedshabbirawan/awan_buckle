<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Definitions;
use App\Traits\CurdBy;


class StockAudit extends Model{
    use HasFactory, Definitions, CurdBy, 
    // \OwenIt\Auditing\Auditable, 
    SoftDeletes;

    protected $table    = "stock_audits";
    protected $fillable = ['product_id','shop_id','action','old_qty','update_qty','current_qty','object_type','object_id','stock_old_value','stock_new_value','description','extra_value','created_at','updated_at'];
    protected $appends  = [];

    const STOCK_OBJECT_TYPE_PURCHASE = 'StockPurchase';
    const STOCK_OBJECT_TYPE_PURCHASE_RETURN = 'StockPurchaseReturn';

    const STOCK_OBJECT_TYPE_SALE = 'Sale';
    const STOCK_OBJECT_TYPE_SALE_RETURN = 'SaleReturn';

    const STOCK_OBJECT_TYPE_EXCHANGE = 'StockExchange';
    const STOCK_OBJECT_TYPE_ADJUSTMENT = 'StockAdjustment';



    const STOCK_ACTION_ADD = 'cart.stock_action.add';
    const STOCK_ACTION_MINUS = 'cart.stock_action.minus';


    
}
