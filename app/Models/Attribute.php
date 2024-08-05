<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AttributeValue;

use App\Traits\Definitions;
use App\Traits\CurdBy;

class Attribute extends Model{
    use HasFactory, Definitions, CurdBy;

    protected $fillable = ['name', 'status','created_by','updated_by','deleted_by','deleted_at','created_at','updated_at'];   

    protected static function booted() {
        parent::boot();
        self::curdBy();
    }


    // public function userData(){
    //     return $this->hasMany('App\Models\AttributeValue','attributeName','id');
    // }


    // public function attributeValue(){
    //     return $this->hasMany(AttributeValue::class,'type_id');
    // }
}
