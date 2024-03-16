<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

use App\Traits\Definitions;
use App\Traits\CurdBy;
use App\Enums\Attri;
use Spatie\Permission\Models\Role;
use App\Models\Shop;


use App\Models\StockDelivery;

class ShopAdmin extends Model{
    
    use HasFactory, Definitions, CurdBy;

    protected $table    = "shops_admin";
    protected $fillable = ['shop_id', 'role_id','user_id', 'desc','status'];   
    protected $appends = ['status_label'];

     

    public function role(){
        return $this->belongsTo(Role::class,'role_id');
    }

    public function shop(){
        return $this->belongsTo(Shop::class,'shop_id');
    }

    

}
