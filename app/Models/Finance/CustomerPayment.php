<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Definitions;
use App\Traits\CurdBy;

class CustomerPayment extends  Model implements Auditable{

    use HasFactory, Definitions, CurdBy, \OwenIt\Auditing\Auditable, SoftDeletes;
    
    protected $table    = "finance_customer_payments";
    protected $fillable = ['customer_id','order_id','title','amount','type','detail','status', 'created_by','updated_by','updated_at','deleted_by'];

    // type == ['received', 'pay']
    



}
