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

class Supplier extends Model{
    use HasFactory, Definitions, CurdBy;
    protected $fillable = ['name', 'code','email','ntn', 'phone', 'fax','address','city_id','province_id','country_id', 'status','created_by','updated_by','deleted_by','deleted_at','created_at','updated_at'];   
    protected $appends = ['status_label'];



    public function getStatusLabelAttribute($value){
        return self::statusLabel($this->status);
    }

    // public function getAttri(){
    //     return Attri::Active;
    // }

    public function city(){
        return $this->belongsTo(\App\Models\Location::class,'city_id');
    }

    public function deliveries(){
        return $this->hasMany(StockDelivery::class,'supplier_id');
    }


    public function country_city(){
        // $loc = array();
        // $loc['city'] = $city = $this->belongsTo(\App\Models\Location::class,'city_id');
        // $loc['city'] = $city = $this->belongsTo($city,'parent_id');
    }


    protected static function booted() {
        parent::boot();
        self::curdBy();
    }

}
