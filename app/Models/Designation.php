<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\Definitions;
use App\Traits\CurdBy;
use App\Enums\Attri;

use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Designation extends Model implements Auditable{
    use HasFactory, Definitions, CurdBy, \OwenIt\Auditing\Auditable, SoftDeletes;
 

    protected $table="designations";

    protected $fillable = ['name','status','created_by','updated_by','deleted_by', 'deleted_at', 'created_at','updated_at'];

    protected $appends = ['status_label'];
    
    public function getStatusLabelAttribute($value){
        return self::statusLabel($this->status);
    }

    protected static function booted() {
        parent::boot();
        self::curdBy();
    }


}
