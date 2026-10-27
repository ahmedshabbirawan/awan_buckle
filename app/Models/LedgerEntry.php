<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    use HasFactory;

    protected $table = 'ledger_entries';

    protected $fillable = [
        'ledger_transaction_id',
        'account_id',
        'debit',
        'credit',
    ];

    protected $casts = [
        'ledger_transaction_id' => 'integer',
        'account_id' => 'integer',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    /**
     * Parent transaction
     */
    public function transaction()
    {
        return $this->belongsTo(LedgerTransaction::class, 'ledger_transaction_id');
    }

    /**
     * Associated account
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
