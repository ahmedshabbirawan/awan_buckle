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
    const REF_CUSTOMER_CREDIT = 'CustomerCredit';
    const REF_SUPPLIER_DEBT = 'SupplierDebt';

    const CATEGORY_RECEIVABLE = 'receivable';
    const CATEGORY_BORROWED_MONEY = 'borrowed_money';
    const CATEGORY_BORROWED_GOODS = 'borrowed_goods';
    const CATEGORY_OTHER_PAYABLE = 'other_payable';

    protected $table = 'ledger_transactions';

    protected $fillable = [
        'transaction_date',
        'reference_type',
        'reference_id',
        'description',
        'metadata',
        'shop_id',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'reference_id' => 'integer',
        'metadata' => 'array',
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

    /**
     * Associated Customer (when reference_type is CustomerCredit)
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'reference_id');
    }

    /**
     * Associated Supplier (when reference_type is SupplierDebt)
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'reference_id');
    }

    /**
     * Decode metadata from dedicated metadata column (or description if JSON formatted)
     */
    public function getMetadataAttribute(): array
    {
        $meta = $this->attributes['metadata'] ?? null;
        if (!empty($meta)) {
            if (is_array($meta)) {
                return $meta;
            }
            if (is_string($meta)) {
                $decoded = json_decode($meta, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded;
                }
            }
        }

        // Backward compatibility fallback if description had JSON
        $raw = $this->attributes['description'] ?? '';
        if (is_string($raw) && (str_starts_with($raw, '{') || str_starts_with($raw, '['))) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }
        return ['description' => $raw];
    }

    /**
     * Unique Transaction Code e.g. CR-20261104-0001 or DB-20261104-0001
     */
    public function getTransactionIdAttribute(): string
    {
        return $this->metadata['tx_code'] ?? ($this->reference_type . ' #' . $this->id);
    }

    /**
     * Transaction Category
     */
    public function getCategoryAttribute(): string
    {
        return $this->metadata['category'] ?? ($this->reference_type === self::REF_CUSTOMER_CREDIT ? self::CATEGORY_RECEIVABLE : self::CATEGORY_OTHER_PAYABLE);
    }

    /**
     * Category Label
     */
    public function getCategoryLabelAttribute(): string
    {
        $labels = [
            self::CATEGORY_RECEIVABLE => 'Customer Receivable',
            self::CATEGORY_BORROWED_MONEY => 'Borrowed Money',
            self::CATEGORY_BORROWED_GOODS => 'Borrowed Goods',
            self::CATEGORY_OTHER_PAYABLE => 'Other Payable',
        ];

        return $labels[$this->category] ?? ucwords(str_replace('_', ' ', $this->category));
    }

    /**
     * Transaction Type: credit or debt
     */
    public function getTypeAttribute(): string
    {
        if ($this->reference_type === self::REF_CUSTOMER_CREDIT) {
            return 'credit';
        }
        if ($this->reference_type === self::REF_SUPPLIER_DEBT) {
            return 'debt';
        }
        return strtolower($this->reference_type);
    }

    /**
     * Type Label
     */
    public function getTypeLabelAttribute(): string
    {
        if ($this->reference_type === self::REF_CUSTOMER_CREDIT) {
            return 'Credit / Amount Receivable';
        }
        if ($this->reference_type === self::REF_SUPPLIER_DEBT) {
            return 'Debt / Amount Payable';
        }
        return $this->reference_type;
    }

    /**
     * Amount
     */
    public function getAmountAttribute(): float
    {
        if (isset($this->metadata['amount'])) {
            return (float) $this->metadata['amount'];
        }

        // Fallback to entry debit/credit
        if ($this->relationLoaded('entries') && $this->entries->isNotEmpty()) {
            return (float) max($this->entries->max('debit'), $this->entries->max('credit'));
        }

        return 0.00;
    }

    /**
     * Running balance after this transaction
     */
    public function getBalanceAfterAttribute(): float
    {
        return (float) ($this->metadata['balance_after'] ?? 0.00);
    }

    /**
     * Reference number
     */
    public function getReferenceNoAttribute(): ?string
    {
        return $this->metadata['reference_no'] ?? null;
    }

    /**
     * Description / Remarks
     */
    public function getRemarksAttribute(): ?string
    {
        return $this->metadata['remarks'] ?? $this->metadata['description'] ?? ($this->attributes['description'] ?? null);
    }

    /**
     * Human readable description (unpacks JSON if credit/debt transaction)
     */
    public function getDescriptionAttribute(): ?string
    {
        $raw = $this->attributes['description'] ?? null;
        if (is_string($raw) && (str_starts_with($raw, '{') || str_starts_with($raw, '['))) {
            return $this->metadata['description'] ?? null;
        }
        return $raw;
    }

    /**
     * Display description for table/statement
     */
    public function getDisplayDescriptionAttribute(): string
    {
        $desc = $this->remarks ?: $this->category_label;
        if ($this->reference_no) {
            $desc .= " (Ref: {$this->reference_no})";
        }
        return $desc;
    }

    /**
     * Attachment file path
     */
    public function getAttachmentAttribute(): ?string
    {
        return $this->metadata['attachment'] ?? null;
    }

    /**
     * Attachment original file name
     */
    public function getAttachmentNameAttribute(): ?string
    {
        return $this->metadata['attachment_name'] ?? null;
    }

    /**
     * Debit Amount
     */
    public function getDebitAmountAttribute(): float
    {
        if ($this->reference_type === self::REF_CUSTOMER_CREDIT) {
            return $this->amount;
        }
        return 0.00;
    }

    /**
     * Credit Amount
     */
    public function getCreditAmountAttribute(): float
    {
        if ($this->reference_type === self::REF_SUPPLIER_DEBT) {
            return $this->amount;
        }
        return 0.00;
    }
}
