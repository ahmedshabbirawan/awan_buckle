<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Definitions;
use App\Traits\CurdBy;

use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends  Model implements Auditable{
    
    use HasFactory, Definitions, CurdBy, \OwenIt\Auditing\Auditable, SoftDeletes;

    protected $fillable = ['name','code','detail','status','loc_id','type','created_by','updated_by','deleted_by','deleted_at','created_at','updated_at'];

    protected $appends = ['type_label','status_label'];

    protected static function booted() {
        parent::boot();
        self::curdBy();
    }

    public function parentLocation(){
        return $this->belongsTo(self::class,'loc_id');
    }

    public function getStatusLabelAttribute($value){
        return self::statusLabel($this->status);
    }

    public function getTypeLabelAttribute($value){
        if($this->type == 'CO'){ 
            return 'Country';
        }elseif( $this->type == 'PR' ){ 
            return 'Province';
        }else{ 
            return 'City'; 
        }
    }

}
