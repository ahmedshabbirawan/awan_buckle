<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

use App\Traits\Definitions;
use App\Traits\CurdBy;
use App\Enums\Attri;

use App\Models\StockDelivery;

class Shop extends Model{
    use HasFactory, Definitions, CurdBy;
    protected $fillable = ['name', 'code','description', 'address','manager_id','status','created_by','updated_by','deleted_by','deleted_at','created_at','updated_at'];   
    protected $appends = ['status_label'];

    protected static function booted() {
        parent::boot();
        self::curdBy();
    }


    public function getStatusLabelAttribute($value){
        return self::statusLabel($this->status);
    }

}
