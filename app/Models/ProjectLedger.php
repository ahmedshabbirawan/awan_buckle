<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\Definitions;
use App\Traits\CurdBy;
use App\Enums\Attri;

use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectLedger extends MModel implements Auditable{
    use HasFactory, Definitions, CurdBy, \OwenIt\Auditing\Auditable, SoftDeletes;

    protected $table="project_ledgers";

    protected $fillable = ['name','project_id', 'status','created_by','updated_by','deleted_by','deleted_at','created_at','updated_at']; 
   //  protected $appends  = ['status_label']; 
}
