<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Definitions;
use App\Traits\CurdBy;
use App\Models\stockItem;



class Project extends Model implements Auditable{

   //  \OwenIt\Auditing\Auditable;

    use HasFactory, \OwenIt\Auditing\Auditable, SoftDeletes, Definitions, CurdBy;


    protected $fillable = ['name','dg','manager_id','code','detail','org_id', 'status','created_by','updated_by','deleted_by','deleted_at','created_at','updated_at']; 
    protected $appends  = ['status_label']; 

    protected static function booted() {
        parent::boot();
        self::curdBy();
    }

    public function organization(){
        return $this->belongsTo(\App\Models\Organization::class,'org_id');
    }

    public function manager(){
        return $this->belongsTo(\App\Models\Manager::class,'manager_id');
        
    }

    public function getStatusLabelAttribute($value){
        return self::statusLabel($this->status);
    }

    public function stockItems(){
        return $this->hasMany(StockItem::class,'project_id');
    }

    // protected static function booted(){
    //     static::updated(function ($project) {
    //         echo 'as'; exit;
    //         $project->updated_by = auth()->user->id;
    //         $project->save();
    //     });
    // }



}
