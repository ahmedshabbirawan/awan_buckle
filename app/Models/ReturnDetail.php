<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\Definitions;
use App\Traits\CurdBy;
use App\Enums\Attri;

use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\IssuanceItem;

class ReturnDetail extends Model implements Auditable{
    use HasFactory, Definitions, CurdBy, \OwenIt\Auditing\Auditable, SoftDeletes;

    protected $table    =   "return_details";
    protected $fillable =   ['emp_id','item_return_count','item_return_ids','return_report_file','remarks','status'];

 //    protected $appends = ['status_label'];


    function employee(){
        return $this->belongsTo('App\Models\Employee','emp_id');
    }

    function returnItem(){
        return $this->hasMany(IssuanceItem::class,'return_detail_id')   ;
    }

    function receivedBy(){
        return self::belongsTo(User::class,'created_by');
    }

    protected static function booted() {
        parent::boot();
        self::curdBy();
      //   self::issuedBy();
    }

}