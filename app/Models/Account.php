<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    const TYPE_ASSET = 'asset';
    const TYPE_LIABILITY = 'liability';
    const TYPE_EQUITY = 'equity';
    const TYPE_REVENUE = 'revenue';
    const TYPE_EXPENSE = 'expense';

    const HOLDER_CUSTOMER = 'Customer';
    const HOLDER_SUPPLIER = 'Supplier';
    const HOLDER_INTERNAL = 'Internal';

    protected $table = 'accounts';

    protected $fillable = [
        'name',
        'code',
        'type',
        'holder_type',
        'holder_id',
        'balance',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'status' => 'integer',
        'holder_id' => 'integer',
    ];

    /**
     * Ledger entries for this account
     */
    public function entries()
    {
        return $this->hasMany(LedgerEntry::class, 'account_id');
    }

    /**
     * Associated customer (if holder_type is Customer)
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'holder_id');
    }

    /**
     * Associated supplier (if holder_type is Supplier)
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'holder_id');
    }

    /**
     * Dynamically compute current balance based on double-entry accounting rules:
     * - Assets & Expenses: Debit - Credit (Normal balance = Debit)
     * - Liabilities, Equities, Revenues: Credit - Debit (Normal balance = Credit)
     *
     * @return float
     */
    public function calculateBalance(): float
    {
        $totals = $this->entries()
            ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
            ->first();

        $debit = (float) ($totals->total_debit ?? 0);
        $credit = (float) ($totals->total_credit ?? 0);

        if (in_array($this->type, [self::TYPE_ASSET, self::TYPE_EXPENSE])) {
            return round($debit - $credit, 2);
        } else {
            return round($credit - $debit, 2);
        }
    }

    /**
     * Sync cached balance column with calculated balance
     *
     * @return float
     */
    public function syncBalance(): float
    {
        $calculated = $this->calculateBalance();
        $this->balance = $calculated;
        $this->save();
        return $calculated;
    }
}
