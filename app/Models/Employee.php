<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Traits\Definitions;
use App\Traits\CurdBy;

use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;


class Employee extends Model implements Auditable{

    use HasFactory, Definitions, CurdBy,  \OwenIt\Auditing\Auditable, SoftDeletes;

   


    protected $fillable = ['id',	
    'org_id',
    'emp_code',	
    'first_name',
    'last_name',
    'full_name',
    'project',
    'project_id',
    'project_code',
    'department',
    'emp_id',	
    'cnic',	
    'phone',
    'mobile',	
    'email',
    'designation',	
    'designation_id',
    'address',	
    'city_id',
    'source',
    'status','created_by','updated_by','deleted_by', 'deleted_at',	
    'created_at',	
    'updated_at'];


    protected $appends = ['status_label','full_name','profile_pic'];

    


    public function city(){
        return $this->belongsTo(\App\Models\Location::class,'city_id');
    }


    public function getStatusLabelAttribute($value){
        if($this->status == 0){
            return 'InActive';
        }elseif($this->status == 1){
            return 'Active';
        }
    }

    public function getFullNameAttribute($value){
        return $this->first_name.' '.$this->last_name;
    }


    public function getProfilePicAttribute($value){
        if(!(isset($this->cnic))){
            return '';
        }
        $num = substr($this->cnic,-1) % 2;
        if($num == 1){
            // return 'Male';
           return asset('assets/images/avatars/male.jpg');
        }else{
            // return 'Female';
           return  asset('assets/images/avatars/female.png');
        }
    }


    protected static function booted() {
        parent::boot();
        self::creating(function ($model) {

            // print_r($model); exit;

            if($model->source != ' '){
                $statement          =   DB::select("SHOW TABLE STATUS LIKE 'employees'");
                $nextId             =   isset($statement[0]) ? $statement[0]->Auto_increment : $this->max('id') + 1;
                $model->emp_code    =   'ims-'.$nextId;  //(string)Uuid::generate();    
            }
        });
        self::curdBy();
    }

    	


}
