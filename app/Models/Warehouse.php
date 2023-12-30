<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Definitions;

class Warehouse extends Model
{
    use HasFactory, Definitions;





protected $fillable = ['name', 'code','address','city_id', 'status','created_at','updated_at'];  
protected $appends = ['status_label'];



    public function city(){
        return $this->belongsTo(\App\Models\Location::class,'city_id');
    }


    public function getStatusLabelAttribute(){
        return self::statusLabel($this->status);
    }




}
