<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Customer;
use App\Models\LedgerEntry;
use App\Models\LedgerTransaction;
use App\Models\Payment;
use App\Models\Product;
use App\Models\SaleOrder;
use App\Models\SaleOrderItem;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CustomerProductSalesReportService
{
    protected LedgerService $ledgerService;

    public function __construct(LedgerService $ledgerService)
    {
        $this->ledgerService = $ledgerService;
    }

    /**
     * Generate Customer-wise Product & Transaction Sales Report
     *
     * @param int|Customer $customer
     * @param string|null $startDate (Y-m-d)
     * @param string|null $endDate (Y-m-d)
     * @return array
     */
    public function generateReport($customer, ?string $startDate = null, ?string $endDate = null): array
    {
        if (is_numeric($customer)) {
            $customer = Customer::findOrFail($customer);
        }

        $startSql = $startDate ? Carbon::parse($startDate)->toDateString() : null;
        $endSql   = $endDate ? Carbon::parse($endDate)->toDateString() : null;

        $account = $this->ledgerService->getOrCreateAccount(Account::HOLDER_CUSTOMER, $customer->id);

        // 1. Calculate Opening Balance as of Start Date
        $openingBalance = 0.00;
        if ($startSql) {
            // A. Prior Sales net receivable (due_amount)
            $priorSales = SaleOrder::where('customer_id', $customer->id)
                ->whereDate('created_at', '<', $startSql)
                ->get();

            $priorDue = 0.00;
            foreach ($priorSales as $ps) {
                $pTotal  = (float) ($ps->total_amount > 0 ? $ps->total_amount : ((float) $ps->total_price > 0 ? $ps->total_price : 0));
                $pPaid   = (float) ($ps->paid_amount ?? 0);
                $pDue    = (float) ($ps->due_amount ?? 0);
                $pMethod = strtolower($ps->payment_method ?? 'cash');

                if ($pTotal > 0 && $pPaid == 0 && $pDue == 0) {
                    if ($pMethod === 'credit') {
                        $pDue = $pTotal;
                    } elseif ($pMethod === 'cash' || $pMethod === 'bank_transfer') {
                        $pPaid = $pTotal;
                    } else {
                        $pPaid = $pTotal;
                    }
                }
                $priorDue += $pDue;
            }

            // B. Prior Customer Payment Vouchers
            $priorReceipts = (float) Payment::where('party_type', Payment::PARTY_CUSTOMER)
                ->where('party_id', $customer->id)
                ->whereDate('payment_date', '<', $startSql)
                ->sum('amount');

            // C. Prior Sale Returns
            $priorReturns = (float) (SaleReturn::where('sale_return.customer_id', $customer->id)
                ->whereDate('sale_return.created_at', '<', $startSql)
                ->join('sale_return_items', 'sale_return.id', '=', 'sale_return_items.sale_return_id')
                ->join('products', 'sale_return_items.product_id', '=', 'products.id')
                ->selectRaw('COALESCE(SUM(sale_return_items.qty * products.price), 0) as total')
                ->value('total') ?? 0);

            // D. Prior Manual Ledger Adjustments
            $priorAdjustments = (float) (LedgerEntry::where('account_id', $account->id)
                ->whereHas('transaction', function ($q) use ($startSql) {
                    $q->whereDate('transaction_date', '<', $startSql)
                      ->whereNotIn('reference_type', [
                          LedgerTransaction::REF_SALE,
                          LedgerTransaction::REF_RECEIPT,
                          LedgerTransaction::REF_PAYMENT
                      ]);
                })
                ->selectRaw('COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) as net_adj')
                ->value('net_adj') ?? 0);

            $openingBalance = round($priorDue - $priorReceipts - $priorReturns + $priorAdjustments, 2);
        }

        // 2. Fetch Sales within Date Range
        $salesQuery = SaleOrder::with(['saleOrderItem.product', 'shop'])
            ->where('customer_id', $customer->id);

        if ($startSql) {
            $salesQuery->whereDate('created_at', '>=', $startSql);
        }
        if ($endSql) {
            $salesQuery->whereDate('created_at', '<=', $endSql);
        }

        $sales = $salesQuery->orderBy('created_at', 'asc')->orderBy('id', 'asc')->get();

        // 3. Fetch Payment Vouchers within Date Range
        $paymentsQuery = Payment::where('party_type', Payment::PARTY_CUSTOMER)
            ->where('party_id', $customer->id);

        if ($startSql) {
            $paymentsQuery->whereDate('payment_date', '>=', $startSql);
        }
        if ($endSql) {
            $paymentsQuery->whereDate('payment_date', '<=', $endSql);
        }

        $payments = $paymentsQuery->orderBy('payment_date', 'asc')->orderBy('id', 'asc')->get();

        // 4. Fetch Sale Returns within Date Range
        $returnsQuery = SaleReturn::where('customer_id', $customer->id);

        if ($startSql) {
            $returnsQuery->whereDate('created_at', '>=', $startSql);
        }
        if ($endSql) {
            $returnsQuery->whereDate('created_at', '<=', $endSql);
        }

        $returns = $returnsQuery->orderBy('created_at', 'asc')->orderBy('id', 'asc')->get();

        // 5. Fetch Ledger Adjustments within Date Range
        $adjustmentsQuery = LedgerEntry::where('account_id', $account->id)
            ->whereHas('transaction', function ($q) use ($startSql, $endSql) {
                $q->whereNotIn('reference_type', [
                    LedgerTransaction::REF_SALE,
                    LedgerTransaction::REF_RECEIPT,
                    LedgerTransaction::REF_PAYMENT
                ]);
                if ($startSql) {
                    $q->whereDate('transaction_date', '>=', $startSql);
                }
                if ($endSql) {
                    $q->whereDate('transaction_date', '<=', $endSql);
                }
            });

        $adjustments = $adjustmentsQuery->with('transaction')->get();

        // 6. Build Raw Product-Wise Rows & Transactions
        $rawRows = [];

        foreach ($sales as $order) {
            $orderDateStr = $order->created_at ? $order->created_at->toDateString() : now()->toDateString();
            $orderDateFmt = $order->created_at ? $order->created_at->format('d/m/Y') : Carbon::parse($orderDateStr)->format('d/m/Y');
            $orderIdDisplay = (string) ($order->id);
            $vehicleNo = $order->vehicle_no ?: ($order->vehicle_number ?: '-');
            $biltyNo   = $order->bilty_no ?: ($order->bilty_number ?: '-');

            $orderItems = $order->saleOrderItem;

            // If order has line items, create 1 row per product item
            if ($orderItems && count($orderItems) > 0) {
                foreach ($orderItems as $item) {
                    $productName = optional($item->product)->name ?: ('Product #' . $item->product_id);
                    $qty         = (float) $item->qty;
                    $rate        = (float) ($item->offer_price > 0 ? $item->offer_price : ($item->price > 0 ? $item->price : 0));
                    $totalSale   = round($rate * $qty, 2);

                    $commPct = (float) ($item->commission_percentage ?? 0);
                    if ($item->is_commission_applied && $item->commission_amount > 0) {
                        $commAmount = (float) $item->commission_amount;
                    } elseif ($commPct > 0) {
                        $commAmount = round(($rate * $commPct / 100) * $qty, 2);
                    } else {
                        $commAmount = 0.00;
                    }

                    $discountUnit = (float) ($item->discount ?? 0);
                    $discountAmount = round($discountUnit * $qty, 2);
                    $discountRate = ($rate > 0 && $discountUnit > 0) ? round(($discountUnit / $rate) * 100, 2) : 0.00;

                    $lineDebit = round($totalSale - $commAmount - $discountAmount, 2);

                    $rawRows[] = [
                        'sort_date'             => $orderDateStr,
                        'sort_order'            => 1,
                        'order_id_num'          => $order->id,
                        'row_id'                => 'item_' . $item->id,
                        'date'                  => $orderDateFmt,
                        'particulars'           => $productName,
                        'order_id'              => $orderIdDisplay,
                        'vehicle_no'            => $vehicleNo,
                        'bilty_no'              => $biltyNo,
                        'qty'                   => $qty,
                        'rate'                  => $rate,
                        'total_sale'            => $totalSale,
                        'commission_percentage' => $commPct,
                        'commission_amount'     => $commAmount,
                        'discount_rate'         => $discountRate,
                        'discount_amount'       => $discountAmount,
                        'debit'                 => $lineDebit,
                        'credit'                => 0.00,
                        'row_type'              => 'sale_product',
                        'raw_obj'               => $item,
                    ];
                }
            } else {
                // Fallback for orders without separate items
                $totalAmt = (float) ($order->total_amount > 0 ? $order->total_amount : ((float) $order->total_price > 0 ? $order->total_price : 0));
                $rawRows[] = [
                    'sort_date'             => $orderDateStr,
                    'sort_order'            => 1,
                    'order_id_num'          => $order->id,
                    'row_id'                => 'order_' . $order->id,
                    'date'                  => $orderDateFmt,
                    'particulars'           => 'Sale Order #' . $order->id . ($order->sale_key ? ' (' . $order->sale_key . ')' : ''),
                    'order_id'              => $orderIdDisplay,
                    'vehicle_no'            => $vehicleNo,
                    'bilty_no'              => $biltyNo,
                    'qty'                   => (float) $order->item_count,
                    'rate'                  => $totalAmt,
                    'total_sale'            => $totalAmt,
                    'commission_percentage' => 0.00,
                    'commission_amount'     => (float) ($order->total_commission ?? 0),
                    'discount_rate'         => 0.00,
                    'discount_amount'       => (float) ($order->total_discount ?? 0),
                    'debit'                 => $totalAmt,
                    'credit'                => 0.00,
                    'row_type'              => 'sale_product',
                    'raw_obj'               => $order,
                ];
            }

            // Payment made at POS counter for this order (Cash, Bank Transfer, or Split Paid portion)
            $paidAmt = (float) ($order->paid_amount ?? 0);
            $totalOrderAmt = (float) ($order->total_amount > 0 ? $order->total_amount : ((float) $order->total_price > 0 ? $order->total_price : 0));
            $orderMethod = strtolower($order->payment_method ?? 'cash');

            // Safety check for legacy orders
            if ($totalOrderAmt > 0 && $paidAmt == 0 && ($order->due_amount ?? 0) == 0 && $orderMethod !== 'credit') {
                $paidAmt = $totalOrderAmt;
            }

            if ($paidAmt > 0) {
                $methodLabel = ($orderMethod === 'bank_transfer') ? 'Bank Transfer / Online' : (($orderMethod === 'split') ? 'Split Payment' : 'Cash');
                $rawRows[] = [
                    'sort_date'             => $orderDateStr,
                    'sort_order'            => 2,
                    'order_id_num'          => $order->id,
                    'row_id'                => 'order_paid_' . $order->id,
                    'date'                  => $orderDateFmt,
                    'particulars'           => "Payment Received ({$methodLabel}) - Order #{$order->id}",
                    'order_id'              => $orderIdDisplay,
                    'vehicle_no'            => '-',
                    'bilty_no'              => '-',
                    'qty'                   => null,
                    'rate'                  => null,
                    'total_sale'            => 0.00,
                    'commission_percentage' => 0.00,
                    'commission_amount'     => 0.00,
                    'discount_rate'         => 0.00,
                    'discount_amount'       => 0.00,
                    'debit'                 => 0.00,
                    'credit'                => $paidAmt,
                    'row_type'              => 'order_payment',
                    'raw_obj'               => $order,
                ];
            }
        }

        // Add Customer Collection Vouchers (Payments)
        foreach ($payments as $pmt) {
            $pmtDateStr = $pmt->payment_date ? Carbon::parse($pmt->payment_date)->toDateString() : ($pmt->created_at ? $pmt->created_at->toDateString() : now()->toDateString());
            $pmtDateFmt = Carbon::parse($pmtDateStr)->format('d/m/Y');
            $vchNo      = $pmt->voucher_no ?: ('RCP-' . str_pad($pmt->id, 5, '0', STR_PAD_LEFT));
            $method     = strtolower($pmt->payment_method ?? 'cash');
            $methodText = ($method === 'bank_transfer') ? 'Bank Transfer / Online' : 'Cash';
            $amount     = (float) $pmt->amount;

            $particulars = "Customer Receipt {$vchNo} ({$methodText})";
            if (!empty($pmt->reference_no)) {
                $particulars .= " Ref: " . $pmt->reference_no;
            }
            if (!empty($pmt->notes)) {
                $particulars .= " - " . $pmt->notes;
            }

            $rawRows[] = [
                'sort_date'             => $pmtDateStr,
                'sort_order'            => 3,
                'order_id_num'          => 0,
                'row_id'                => 'payment_' . $pmt->id,
                'date'                  => $pmtDateFmt,
                'particulars'           => $particulars,
                'order_id'              => $vchNo,
                'vehicle_no'            => '-',
                'bilty_no'              => '-',
                'qty'                   => null,
                'rate'                  => null,
                'total_sale'            => 0.00,
                'commission_percentage' => 0.00,
                'commission_amount'     => 0.00,
                'discount_rate'         => 0.00,
                'discount_amount'       => 0.00,
                'debit'                 => 0.00,
                'credit'                => $amount,
                'row_type'              => 'voucher_payment',
                'raw_obj'               => $pmt,
            ];
        }

        // Add Sale Returns
        foreach ($returns as $ret) {
            $retDateStr = $ret->created_at ? $ret->created_at->toDateString() : now()->toDateString();
            $retDateFmt = Carbon::parse($retDateStr)->format('d/m/Y');
            $retItems = SaleReturnItem::with('product')->where('sale_return_id', $ret->id)->get();

            foreach ($retItems as $ritem) {
                $pName = optional($ritem->product)->name ?: ('Product #' . $ritem->product_id);
                $pPrice = (float) (optional($ritem->product)->price ?? 0);
                $retVal = round($ritem->qty * $pPrice, 2);

                $rawRows[] = [
                    'sort_date'             => $retDateStr,
                    'sort_order'            => 4,
                    'order_id_num'          => $ret->order_id ?: 0,
                    'row_id'                => 'return_' . $ritem->id,
                    'date'                  => $retDateFmt,
                    'particulars'           => "Sale Return: {$pName}" . ($ret->description ? " ({$ret->description})" : ""),
                    'order_id'              => $ret->order_id ? ('Order #' . $ret->order_id) : ('SR-' . $ret->id),
                    'vehicle_no'            => '-',
                    'bilty_no'              => '-',
                    'qty'                   => (float) $ritem->qty,
                    'rate'                  => $pPrice,
                    'total_sale'            => 0.00,
                    'commission_percentage' => 0.00,
                    'commission_amount'     => 0.00,
                    'discount_rate'         => 0.00,
                    'discount_amount'       => 0.00,
                    'debit'                 => 0.00,
                    'credit'                => $retVal,
                    'row_type'              => 'sale_return',
                    'raw_obj'               => $ritem,
                ];
            }
        }

        // Add Manual Adjustments
        foreach ($adjustments as $adj) {
            $tx = $adj->transaction;
            $txDateStr = $tx ? Carbon::parse($tx->transaction_date)->toDateString() : now()->toDateString();
            $txDateFmt = Carbon::parse($txDateStr)->format('d/m/Y');
            $deb = (float) $adj->debit;
            $crd = (float) $adj->credit;

            $rawRows[] = [
                'sort_date'             => $txDateStr,
                'sort_order'            => 5,
                'order_id_num'          => 0,
                'row_id'                => 'adj_' . $adj->id,
                'date'                  => $txDateFmt,
                'particulars'           => "Adjustment: " . ($tx ? ($tx->description ?: $tx->reference_type) : 'General Adjustment'),
                'order_id'              => $tx ? ($tx->reference_type . ' #' . ($tx->reference_id ?? $tx->id)) : ('ADJ-' . $adj->id),
                'vehicle_no'            => '-',
                'bilty_no'              => '-',
                'qty'                   => null,
                'rate'                  => null,
                'total_sale'            => 0.00,
                'commission_percentage' => 0.00,
                'commission_amount'     => 0.00,
                'discount_rate'         => 0.00,
                'discount_amount'       => 0.00,
                'debit'                 => $deb,
                'credit'                => $crd,
                'row_type'              => 'adjustment',
                'raw_obj'               => $adj,
            ];
        }

        // 7. Sort Chronologically
        usort($rawRows, function ($a, $b) {
            $cmp = strcmp($a['sort_date'], $b['sort_date']);
            if ($cmp !== 0) {
                return $cmp;
            }
            $ordCmp = $a['sort_order'] <=> $b['sort_order'];
            if ($ordCmp !== 0) {
                return $ordCmp;
            }
            $idCmp = ($a['order_id_num'] ?? 0) <=> ($b['order_id_num'] ?? 0);
            if ($idCmp !== 0) {
                return $idCmp;
            }
            return strcmp((string) $a['row_id'], (string) $b['row_id']);
        });

        // 8. Compute Running Balance and Summary Totals
        $runningBalance = $openingBalance;
        $processedRows  = [];

        $totalQuantitySold     = 0.0;
        $totalSaleAmount       = 0.00;
        $totalCommissionAmount = 0.00;
        $totalDiscountAmount   = 0.00;
        $totalDebit            = 0.00;
        $totalCredit           = 0.00;

        foreach ($rawRows as $row) {
            $deb = (float) $row['debit'];
            $crd = (float) $row['credit'];

            $totalDebit  += $deb;
            $totalCredit += $crd;

            if ($row['row_type'] === 'sale_product') {
                $totalQuantitySold     += (float) ($row['qty'] ?? 0);
                $totalSaleAmount       += (float) ($row['total_sale'] ?? 0);
                $totalCommissionAmount += (float) ($row['commission_amount'] ?? 0);
                $totalDiscountAmount   += (float) ($row['discount_amount'] ?? 0);
            }

            $runningBalance = round($runningBalance + $deb - $crd, 2);

            $row['running_balance'] = $runningBalance;
            $row['balance_dr_cr']   = $runningBalance >= 0 ? 'DR' : 'CR';
            $row['balance_amount']  = abs($runningBalance);

            $processedRows[] = $row;
        }

        $closingBalance = $runningBalance;
        $closingBalanceDrCr = $closingBalance >= 0 ? 'DR' : 'CR';
        $closingBalanceAmount = abs($closingBalance);

        $openingBalanceDrCr = $openingBalance >= 0 ? 'DR' : 'CR';
        $openingBalanceAmount = abs($openingBalance);

        return [
            'customer'                 => $customer,
            'account'                  => $account,
            'start_date'               => $startSql,
            'end_date'                 => $endSql,
            'opening_balance'          => $openingBalance,
            'opening_balance_dr_cr'    => $openingBalanceDrCr,
            'opening_balance_amount'   => $openingBalanceAmount,
            'rows'                     => $processedRows,
            'total_quantity_sold'      => round($totalQuantitySold, 2),
            'total_sale_amount'        => round($totalSaleAmount, 2),
            'total_commission_amount'  => round($totalCommissionAmount, 2),
            'total_discount_amount'    => round($totalDiscountAmount, 2),
            'total_debit'              => round($totalDebit, 2),
            'total_credit'             => round($totalCredit, 2),
            'closing_balance'          => $closingBalance,
            'closing_balance_dr_cr'    => $closingBalanceDrCr,
            'closing_balance_amount'   => $closingBalanceAmount,
            'generated_at'             => now()->format('d/m/Y h:i A'),
        ];
    }
}
