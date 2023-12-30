<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Definitions;
use App\Traits\CurdBy;
use App\Models\Organization;
use App\Models\Project;

class Manager extends  Model implements Auditable{

    use HasFactory, Definitions, CurdBy, \OwenIt\Auditing\Auditable, SoftDeletes;
    
    protected $table    = "managers";
    protected $fillable = ['name','code','org_id','manager_id','email','mobile', 'status','created_at','updated_at'];
    protected $appends  = ['status_label'];

    public function getStatusLabelAttribute($value){
        return self::statusLabel($this->status);
    }


    public function project(){
        return $this->hasOne(Project::class,'manager_id');
    }


    public function organization(){
        return $this->belongsTo(Organization::class,'org_id');
    }

    protected static function booted() {
        parent::boot();
        self::curdBy();
    }



}
