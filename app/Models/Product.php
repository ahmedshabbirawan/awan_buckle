<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Definitions;
use App\Traits\CurdBy;
use App\Enums\Attri;
use App\Models\ProductCategory;

use App\Models\ProductAvailable;

use App\Models\ProductAttribute;
use App\Models\AttributeValue;
use App\Models\AttributeType;
use Illuminate\Support\Facades\Storage;


class Product extends Model implements Auditable{
    use HasFactory, Definitions, CurdBy, \OwenIt\Auditing\Auditable, SoftDeletes;
    protected $table="products";

    protected $fillable = ['name','code','description','cost_price','product_cat_id','price','uom_id','threshold_qty','status','created_at','updated_at','image'];

    protected $appends = ['status_label','image_url'];

    
    public function getStatusLabelAttribute($value){
        return self::statusLabel($this->status);
    }

    public function getImageUrlAttribute($value){
        if($this->image){
            return url(Storage::url('public/product_images/'.$this->image));
        }else{
            return asset('assets/images/placeholder/noimage.png');
        }
    }

    protected static function booted() {
        parent::boot();
        self::curdBy();
    }


    public function product_Category(){
        return $this->belongsTo('App\Models\ProductCategory','product_cat_id');
    }

    public function productQtyShopWise(){
        $shopID = auth()->user()->shop_id;
        return $this->hasOne(ProductAvailable::class,'product_id')->where('shop_id',$shopID);
    }


    //////------------------------------------------------------------------------

    public function productAvailable(){
        return $this->hasMany(ProductAvailable::class,'product_id'); // ->where('shop_id',$shopID);
    }


    public function StockDeliveryProduct(){
        return $this->hasMany(StockDeliveryProduct::class,'product_id');
    }


    

    //////------------------------------------------------------------------------

    public function categories_tree(){
        
        $product = $this->belongsTo('App\Models\ProductCategory','product_cat_id')->first();
        if(empty($product)){
                $emptyObj = ['id' => '-' , 'name' => '-'];
             $arr['product_category'] = $emptyObj;
             $arr['sub_category'] =  $emptyObj;
             $arr['category'] = $emptyObj;
             return $arr; 
        }
        $arr['product_category']    = (empty($product->name))?null:['id' => $product->id , 'name' =>$product->name];
        $subCat = $product->parentSubCategoryName;
        $arr['sub_category']        = (empty($subCat->name))?null:['id' => $subCat->id, 'name' =>  $subCat->name];
        $category = $subCat->parentCategoryName;
        $arr['category']            = (empty($category->name))?null:['id' => $category->id, 'name' => $category->name];
        return $arr;
    }


    // public function product_Attribute(){
    //     return $this->belongsToMany('App\Models\ProductCategory','product_cat_id');
    // }

    public function product_Attribute_Value(){
        return $this->belongsToMany(AttributeValue::class,'product_attributes',null,'value_id');
    }

    function uom(){
        return $this->belongsTo('App\Models\Uom','uom_id');
    }


    function attributeHTML(){
        $attributes = array();
        foreach($this->product_attribute_value as $attr):
            $attributes[] = $attr->attribute_type->name.' : '.$attr->value;
        endforeach;
        if( count($attributes) > 0 ){
            return '<span class="label label-sm label-warning"> '. implode(' / ', $attributes).' </span>';
        }else{
            return '';
        }
    }


    function categoryHTML(){
        $catz = $this->categories_tree();
        $attributes=[];
        foreach($catz as $cat):
            $attributes[] = $cat['name'];
        endforeach;
        if( count($attributes) > 0 ){
            return '<span class="label label-sm label-success"> '. implode(' / ', $attributes).' </span>';
        }else{
            return '<b></b>';
        }
    }


//     public function trophies()
// {
//    //return $this->belongsToMany(RelatedModel, pivot_table_name, foreign_key_of_current_model_in_pivot_table, foreign_key_of_other_model_in_pivot_table);
//    return $this->belongsToMany(
//         Trop::class,
//         'trophies_users',
//         'user_id',
//         'trophy_id');
// }



    







}
