<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\AttributeType;
use App\Models\AttributeValue;

class ProductAttribute extends Model{
    use HasFactory;

    protected $table = "product_attributes";

    protected $fillable = ['product_id','value_id','type_id','updated_at','created_at'];


    


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
