<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\Definitions;

use App\Traits\CurdBy;

class Organization extends Model implements Auditable{
    use HasFactory, \OwenIt\Auditing\Auditable;
    use SoftDeletes, Definitions;

    protected $fillable = ['name', 'code','email', 'phone', 'fax','address','city_id', 'status','created_by','updated_by','deleted_by', 'deleted_at', 'created_at','updated_at'];   
    
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $appends = ['status_label'];


    protected static function booted() {
        parent::boot();
        // self::curdBy();
    }


    
    public function getStatusLabelAttribute($value){
        return self::statusLabel($this->status);
    }


    static function organizationSelect($name,  $selectedVal = '', $attribute=''){
        $list = self::where('status', 1)->get()->pluck('name','id');
        return view('form.select',['name' => $name, 'list' => $list, 'selectedVal' => $selectedVal, 'attribute' => $attribute]);
    }


      /**
     * get specified product by id
     * @param int $id
     * @param bool $withTrashed
     * @return array
     */
    public function getOrganizationById(int $id, $withTrashed = false) {
        $org = $this->newQuery()
            ->where('id', $id)
            ->when($withTrashed, function ($query){
                $query->withTrashed();
            })->firstOrFail(); //->get();

        $data = [];
        if ($org){
            $data['status'] = true;
            $data['org'] = $org;
        }else{
            $data['status'] = false;
            $data['org'] = [];
        }
        return $data;
    }


}
