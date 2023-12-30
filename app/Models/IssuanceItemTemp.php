<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\Definitions;
use App\Traits\CurdBy;

class IssuanceItemTemp extends Model
{
    use HasFactory, Definitions, CurdBy;


    protected $table    =   "issuance_item_temp";

    protected $fillable =   ['item_id','project_id','emp_id','product_id',
    'product_cat_id','qty','sub_cat_id','serial_number','issue_date',
    'is_loan','is_data_center','remarks','loan_return_date','return_date',
    'status','created_at','updated_at'];

 //    protected $appends = ['status_label'];


 function product(){
    return $this->belongsTo('App\Models\Product','product_id');
}


protected static function booted() {
    parent::boot();
    self::curdBy();
}


}
