<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CurdBy;
use App\Traits\Definitions;
use App\Models\AttributeType;

use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttributeValue extends Model implements Auditable{
    use HasFactory, Definitions, CurdBy, \OwenIt\Auditing\Auditable, SoftDeletes;


    protected $appends = ['status_label'];

    public function getStatusLabelAttribute(){
        return self::statusLabel($this->status);
    }

    protected static function booted() {
        parent::boot();
        self::curdBy();
    }


    public function attribute_type(){
        return $this->belongsTo(AttributeType::class,'type_id');
    }


}
