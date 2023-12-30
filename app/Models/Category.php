<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class Category extends Model{
    use HasFactory;
    protected $fillable = ['name','code','detail','status','parent_id','type','created_at','updated_at'];



    public function parentCategory(){
        return $this->belongsTo(self::class,'parent_id');//  ->with('parentCategory');
    }

    public function getStatusLabelAttribute($value){
        if($this->status == 0){
            return 'InActive';
        }elseif($this->status == 1){
            return 'Active';
        }
    }


    public function childs() {
        return $this->hasMany('App\Models\Category','id','parent_id') ;
    }


    public function children() {
        return $this->hasMany(Category::class,'parent_id')->with('children');
    }


    function getAllParents($parent_id=0, $idz = true){


        $idz = [$parent_id];
        if($parent_id != 0 && $parent_id != null){
            $loopStatus = true;
            $index = 0;
            while($loopStatus){
                $dd =  DB::table('categories')->where( 'id', $parent_id )->get();
                if( (count($dd) > 0) && ($dd[0]->parent_id != 0) ){
                    $idz[] = $dd[0]->parent_id;
                    $idz[] = $dd[0]->id;
                    $parent_id = $dd[0]->parent_id;
                    $index++;
                }else{
                    break;
                }

                    if($index == 3){
                        break;
                    }
                
            }
        }
        return $idz;
    }


    static function categoriesSelect($name,  $selectedVal = '', $attribute=''){
        $list = self::where('status', 1)->get()->pluck('name','id');
        return view('form.select',['name' => $name, 'list' => $list, 'selectedVal' => $selectedVal, 'attribute' => $attribute]);
    }



}
