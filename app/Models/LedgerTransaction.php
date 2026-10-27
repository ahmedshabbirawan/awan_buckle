<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedgerTransaction extends Model
{
    use HasFactory;

    const REF_SALE = 'Sale';
    const REF_PURCHASE = 'Purchase';
    const REF_PAYMENT = 'Payment';
    const REF_RECEIPT = 'Receipt';
    const REF_ADJUSTMENT = 'Adjustment';

    protected $table = 'ledger_transactions';

    protected $fillable = [
        'transaction_date',
        'reference_type',
        'reference_id',
        'description',
        'shop_id',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'reference_id' => 'integer',
        'shop_id' => 'integer',
        'created_by' => 'integer',
    ];

    /**
     * The journal entries for this transaction
     */
    public function entries()
    {
        return $this->hasMany(LedgerEntry::class, 'ledger_transaction_id');
    }

    /**
     * User who created the transaction
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Associated Shop
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }
}
