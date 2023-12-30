<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\Definitions;
use App\Traits\CurdBy;
use App\Models\ReturnDetail;
use App\Models\User;

class IssuanceItem extends Model implements Auditable{

    use HasFactory, Definitions, CurdBy, \OwenIt\Auditing\Auditable, SoftDeletes;
    protected $table    =   "issuance_item";
    protected $fillable =   ['item_id','project_id','emp_id','product_id','product_cat_id',
    'is_loan','is_data_center','remarks','issue_key','loan_return_date','return_date','return_condition_status',
    'qty','sub_cat_id','serial_number','issue_date', 'status','created_at','updated_at'];

 //    protected $appends = ['status_label'];

 protected $appends  =   ['return_condition_status_label'];




function product(){
    return $this->belongsTo('App\Models\Product','product_id');
}

function employee(){
    return $this->belongsTo('App\Models\Employee','emp_id');
}

function project(){
    return $this->belongsTo('App\Models\Project','project_id');
}

function subCategory(){
    return $this->belongsTo('App\Models\SubCategory','sub_cat_id');
}

function returnDetail(){
    return $this->belongsTo(ReturnDetail::class,'return_detail_id');
}

function issuedBy(){
    return self::belongsTo(User::class,'created_by');
}

function receivedBy(){
    return self::belongsTo(User::class,'updated_by');
}


public function getReturnConditionStatusLabelAttribute($value){
    return self::conditionLabel($this->return_condition_status);
}

protected static function booted() {
    parent::boot();
    self::curdBy();
  //   self::issuedBy();
}


}
