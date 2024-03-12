<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use App\Models\Shop;
use Spatie\Permission\Models\Role;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'shop_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getImageAttribute(){
        return ($this->attributes['image'] != null) ? Storage::disk('public')->url($this->attributes['image']) : URL::to('/assets/images/avatars/avatar2.png');
    }


    function shop(){
        return $this->belongsTo(Shop::class,'shop_id');
    }

    /*
    public function can_manage($shop_id) : bool
    {

        #Backdoor for the system admin
        // if ($this->hasRole(['System Admin'])) {
        //     return true;
        // }


        #now we check if the user has the role for that page
        $role = Role::select('name')
                        ->join("shops_admin", 'shops_admin.role_id', '=', 'roles.id')
                        ->where("shop_id", $shop_id)
                        ->where("user_id",   $this->id)
                        ->first();


        #return true if roles was found
        if (!empty($role)) {
            return true;
        }


        #else false
        return false;


    }

    */



}
