<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\Definitions;
use App\Traits\CurdBy;
use App\Enums\Attri;

use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectManager extends Model implements Auditable{
    use HasFactory, Definitions, CurdBy, \OwenIt\Auditing\Auditable, SoftDeletes;
    
}
