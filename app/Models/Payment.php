<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    const PARTY_CUSTOMER = 'Customer';
    const PARTY_SUPPLIER = 'Supplier';

    const TYPE_RECEIPT = 'receipt';
    const TYPE_PAYMENT = 'payment';

    const METHOD_CASH = 'cash';
    const METHOD_BANK = 'bank_transfer';
    const METHOD_CHEQUE = 'cheque';

    protected $table = 'payments';

    protected $fillable = [
        'voucher_no',
        'party_type',
        'party_id',
        'payment_type',
        'amount',
        'payment_method',
        'payment_date',
        'reference_no',
        'notes',
        'ledger_transaction_id',
        'shop_id',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'party_id' => 'integer',
        'ledger_transaction_id' => 'integer',
        'shop_id' => 'integer',
        'created_by' => 'integer',
    ];

    /**
     * Associated customer (if party_type is Customer)
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'party_id');
    }

    /**
     * Associated supplier (if party_type is Supplier)
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'party_id');
    }

    /**
     * Associated journal transaction
     */
    public function ledgerTransaction()
    {
        return $this->belongsTo(LedgerTransaction::class, 'ledger_transaction_id');
    }

    /**
     * User who recorded the payment
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Associated shop
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }
}
