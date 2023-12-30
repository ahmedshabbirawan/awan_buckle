<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use App\Traits\Definitions;
use Illuminate\Support\Collection;

class Uom extends Model{
    use HasFactory;


    use HasFactory, Definitions;

    protected $table="measure_units";

    protected $fillable = ['name','code','status','created_by','updated_by','deleted_by','deleted_at'];  

    public static $type      = 'UOM'; // self::ATTRIBUTE_RAM;
    public static $label     = 'Unit of Measure'; // Definitions::$ATTRIBUTE_RAM_STRING;


    protected $appends = ['status_label'];


    protected static function booted(){
        // static::addGlobalScope('ancient', function (Builder $builder) {
        //     $builder->where('at_type', self::$type );
        // });
    }


    
    public function getStatusLabelAttribute(){
        return self::statusLabel($this->status);
    }

    public static function getDefault(){
        $obj = self::where('code','Pair')->first();
        if($obj){
            return $obj;
        }else{
            $obj = self::where('code','No.')->first();
            if($obj){
                return $obj;
            }else{
                return collect(['id' => 0, 'name' => 'N/A', 'code' => 'N/A']);
            }
        }
    }


}
