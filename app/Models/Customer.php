<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

use App\Traits\Definitions;
use App\Traits\CurdBy;
use App\Enums\Attri;

use App\Models\StockDelivery;

class Customer extends Model{
    use HasFactory, Definitions, CurdBy;
    protected $fillable = ['name', 'mobile','email','cnic', 'address','info','status','created_by','updated_by','deleted_by','deleted_at','created_at','updated_at'];   
    protected $appends = ['status_label'];

    protected static function booted() {
        parent::boot();
        self::curdBy();
    }


    public function getStatusLabelAttribute($value){
        return self::statusLabel($this->status);
    }

    public function account()
    {
        return $this->hasOne(Account::class, 'holder_id')
            ->where('holder_type', Account::HOLDER_CUSTOMER);
    }

    public function sales()
    {
        return $this->hasMany(SaleOrder::class, 'customer_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'party_id')
            ->where('party_type', Payment::PARTY_CUSTOMER);
    }

    public function getCurrentBalanceAttribute(): float
    {
        $account = $this->account;
        return $account ? $account->calculateBalance() : 0.00;
    }

}
