<?php

namespace App\Http\Controllers\Sale\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SaleOrder;
use App\Models\SaleOrderItem;
use App\Models\SaleReturnItem;
use App\Models\Payment;
use App\Models\Account;
use App\Models\Shop;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DailySalesReportController extends Controller
{
    /**
     * Display the Daily Sales Summary Report view
     */
    public function index(Request $request)
    {
        $data['shops'] = Shop::all()->pluck('name', 'id');
        $data['default_start_date'] = Carbon::now()->startOfMonth()->format('d/m/Y');
        $data['default_end_date'] = Carbon::now()->format('d/m/Y');

        return view('sale.report.daily_sales_summary', $data);
    }

    /**
     * Fetch daily aggregated data for the table and summary KPI cards
     */
    public function data(Request $request)
    {
        $shopIds = $request->get('shop_ids');
        if (is_string($shopIds)) {
            $shopIds = array_filter(explode(',', $shopIds));
        }

        $dateRange = $request->get('date_range');
        $parsedDates = $this->parseDateRange($dateRange);
        $startDate = $parsedDates['start'];
        $endDate = $parsedDates['end'];

        // 1. Sales Query grouped by date
        $salesQuery = SaleOrder::query();
        if ($shopIds && count($shopIds) > 0) {
            $salesQuery->whereIn('shop_id', $shopIds);
        }
        if ($startDate && $endDate) {
            $salesQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $sales = $salesQuery->selectRaw('
            DATE(created_at) as sale_date,
            COUNT(id) as order_count,
            SUM(COALESCE(total_amount, total_price, 0)) as total_sales,
            SUM(CASE 
                WHEN payment_method = "cash" OR payment_method IS NULL THEN COALESCE(paid_amount, total_amount, total_price, 0)
                WHEN payment_method = "split" THEN COALESCE(paid_amount, 0)
                ELSE 0 
            END) as cash_sales,
            SUM(CASE 
                WHEN payment_method = "bank_transfer" THEN COALESCE(paid_amount, total_amount, total_price, 0)
                ELSE 0 
            END) as online_sales,
            SUM(CASE 
                WHEN payment_method = "credit" THEN COALESCE(due_amount, total_amount, total_price, 0)
                WHEN payment_method = "split" THEN COALESCE(due_amount, 0)
                ELSE COALESCE(due_amount, 0)
            END) as credit_sales,
            SUM(COALESCE(item_count, 0)) as items_sold
        ')
        ->groupBy('sale_date')
        ->get()
        ->keyBy('sale_date');

        // 2. Customer Collections (Receipt Vouchers) grouped by date
        $paymentsQuery = Payment::where('party_type', Payment::PARTY_CUSTOMER)
            ->where('payment_type', Payment::TYPE_RECEIPT);
        if ($shopIds && count($shopIds) > 0) {
            $paymentsQuery->whereIn('shop_id', $shopIds);
        }
        if ($startDate && $endDate) {
            $paymentsQuery->whereBetween('payment_date', [$startDate->toDateString(), $endDate->toDateString()]);
        }

        $collections = $paymentsQuery->selectRaw('
            DATE(payment_date) as p_date,
            COUNT(id) as receipt_count,
            SUM(CASE WHEN payment_method = "cash" THEN amount ELSE 0 END) as cash_collected,
            SUM(CASE WHEN payment_method = "bank_transfer" THEN amount ELSE 0 END) as online_collected,
            SUM(amount) as total_collected
        ')
        ->groupBy('p_date')
        ->get()
        ->keyBy('p_date');

        // 3. Sales Returns grouped by date
        $returnsQuery = SaleReturnItem::query()
            ->leftJoin('products', 'products.id', '=', 'sale_return_items.product_id');
        if ($shopIds && count($shopIds) > 0) {
            $returnsQuery->whereIn('sale_return_items.shop_id', $shopIds);
        }
        if ($startDate && $endDate) {
            $returnsQuery->whereBetween('sale_return_items.created_at', [$startDate, $endDate]);
        }

        $returns = $returnsQuery->selectRaw('
            DATE(sale_return_items.created_at) as r_date,
            SUM(sale_return_items.qty) as return_items,
            SUM(sale_return_items.qty * COALESCE(products.price, 0)) as return_amount
        ')
        ->groupBy('r_date')
        ->get()
        ->keyBy('r_date');

        // Union of all distinct dates across sales, collections, and returns
        $allDates = collect()
            ->merge($sales->keys())
            ->merge($collections->keys())
            ->merge($returns->keys())
            ->unique()
            ->sortDesc()
            ->values();

        $dailyRows = [];
        $grandTotals = [
            'days_count' => count($allDates),
            'order_count' => 0,
            'total_sales' => 0.0,
            'cash_sales' => 0.0,
            'online_sales' => 0.0,
            'credit_sales' => 0.0,
            'credit_pending' => 0.0,
            'credit_collected' => 0.0,
            'customer_cash_collected' => 0.0,
            'customer_online_collected' => 0.0,
            'total_collected' => 0.0,
            'total_cash_inflow' => 0.0,
            'total_online_inflow' => 0.0,
            'return_amount' => 0.0,
            'net_sales' => 0.0,
            'items_sold' => 0,
            'items_returned' => 0,
            'net_stock_movement' => 0,
        ];

        foreach ($allDates as $dateStr) {
            $s = $sales->get($dateStr);
            $c = $collections->get($dateStr);
            $r = $returns->get($dateStr);

            $orderCount = (int)($s ? $s->order_count : 0);
            $totalSales = (float)($s ? $s->total_sales : 0);
            $cashSales = (float)($s ? $s->cash_sales : 0);
            $onlineSales = (float)($s ? $s->online_sales : 0);
            $creditSales = (float)($s ? $s->credit_sales : 0);
            $itemsSold = (int)($s ? $s->items_sold : 0);

            $cashCollected = (float)($c ? $c->cash_collected : 0);
            $onlineCollected = (float)($c ? $c->online_collected : 0);
            $totalCollected = (float)($c ? $c->total_collected : 0);
            $receiptCount = (int)($c ? $c->receipt_count : 0);

            $returnItems = (int)($r ? $r->return_items : 0);
            $returnAmount = (float)($r ? $r->return_amount : 0);

            // Compute credit still pending collection for credit orders on this date
            $creditPending = 0.0;
            if ($creditSales > 0) {
                $creditOrders = SaleOrder::whereDate('created_at', $dateStr)
                    ->where('due_amount', '>', 0)
                    ->when($shopIds, fn($q) => $q->whereIn('shop_id', $shopIds))
                    ->get();

                foreach ($creditOrders as $order) {
                    $dueAmt = (float)$order->due_amount;
                    $paidRef = (float)Payment::where(function($q) use ($order) {
                        $q->where('reference_no', (string)$order->id)
                          ->orWhere('reference_no', (string)$order->sale_key);
                    })->where('party_type', Payment::PARTY_CUSTOMER)
                      ->where('payment_type', Payment::TYPE_RECEIPT)
                      ->sum('amount');

                    $custAcc = Account::where('holder_type', Account::HOLDER_CUSTOMER)
                        ->where('holder_id', $order->customer_id)
                        ->first();
                    $custBal = $custAcc ? (float)$custAcc->balance : 0.00;

                    if ($paidRef >= $dueAmt || $custBal <= 0) {
                        $p = 0.0;
                    } else {
                        $p = min($dueAmt - $paidRef, $custBal);
                    }
                    $creditPending += $p;
                }
            }
            $creditBilledSettled = max(0.0, $creditSales - $creditPending);

            $totalCashInflow = $cashSales + $cashCollected;
            $totalOnlineInflow = $onlineSales + $onlineCollected;
            $netSales = max(0.0, $totalSales - $returnAmount);
            $netStock = $itemsSold - $returnItems;

            $carbonDate = Carbon::parse($dateStr);

            $row = [
                'date' => $dateStr,
                'formatted_date' => $carbonDate->format('d-M-Y'),
                'day_name' => $carbonDate->format('l'),
                'order_count' => $orderCount,
                'receipt_count' => $receiptCount,
                'total_sales' => round($totalSales, 2),
                'cash_sales' => round($cashSales, 2),
                'online_sales' => round($onlineSales, 2),
                'credit_sales' => round($creditSales, 2),
                'credit_pending' => round($creditPending, 2),
                'credit_collected' => round($creditBilledSettled, 2),
                'customer_cash_collected' => round($cashCollected, 2),
                'customer_online_collected' => round($onlineCollected, 2),
                'total_collected' => round($totalCollected, 2),
                'total_cash_inflow' => round($totalCashInflow, 2),
                'total_online_inflow' => round($totalOnlineInflow, 2),
                'return_amount' => round($returnAmount, 2),
                'net_sales' => round($netSales, 2),
                'items_sold' => $itemsSold,
                'items_returned' => $returnItems,
                'net_stock_movement' => $netStock,
            ];

            $dailyRows[] = $row;

            // Grand totals
            $grandTotals['order_count'] += $orderCount;
            $grandTotals['total_sales'] += $totalSales;
            $grandTotals['cash_sales'] += $cashSales;
            $grandTotals['online_sales'] += $onlineSales;
            $grandTotals['credit_sales'] += $creditSales;
            $grandTotals['credit_pending'] += $creditPending;
            $grandTotals['credit_collected'] += $creditBilledSettled;
            $grandTotals['customer_cash_collected'] += $cashCollected;
            $grandTotals['customer_online_collected'] += $onlineCollected;
            $grandTotals['total_collected'] += $totalCollected;
            $grandTotals['total_cash_inflow'] += $totalCashInflow;
            $grandTotals['total_online_inflow'] += $totalOnlineInflow;
            $grandTotals['return_amount'] += $returnAmount;
            $grandTotals['net_sales'] += $netSales;
            $grandTotals['items_sold'] += $itemsSold;
            $grandTotals['items_returned'] += $returnItems;
            $grandTotals['net_stock_movement'] += $netStock;
        }

        // Format grand totals to 2 decimal places
        foreach (['total_sales', 'cash_sales', 'online_sales', 'credit_sales', 'credit_pending', 'credit_collected', 'customer_cash_collected', 'customer_online_collected', 'total_collected', 'total_cash_inflow', 'total_online_inflow', 'return_amount', 'net_sales'] as $key) {
            $grandTotals[$key] = round($grandTotals[$key], 2);
        }

        $shopsName = ($shopIds && count($shopIds) > 0) ? Shop::whereIn('id', $shopIds)->pluck('name')->implode(', ') : 'All Shops';
        $dateLabel = ($startDate && $endDate) 
            ? $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y') 
            : 'All Time';

        return response()->json([
            'status' => true,
            'data' => $dailyRows,
            'grand_totals' => $grandTotals,
            'filter_info' => [
                'shops_name' => $shopsName,
                'date_label' => $dateLabel,
                'days_count' => count($dailyRows),
            ]
        ]);
    }

    /**
     * Retrieve transaction details for a specific day for reconciliation and verification
     */
    public function details(Request $request)
    {
        $date = $request->get('date');
        if (!$date) {
            return response()->json(['status' => false, 'message' => 'Date is required.'], 400);
        }

        $shopIds = $request->get('shop_ids');
        if (is_string($shopIds)) {
            $shopIds = array_filter(explode(',', $shopIds));
        }

        // 1. Sales orders for this day
        $orders = SaleOrder::with(['customer', 'shop', 'saleOrderItem.product'])
            ->whereDate('created_at', $date)
            ->when($shopIds && count($shopIds) > 0, fn($q) => $q->whereIn('shop_id', $shopIds))
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Customer Receipt Vouchers for this day
        $receipts = Payment::with(['customer', 'shop', 'creator'])
            ->where('party_type', Payment::PARTY_CUSTOMER)
            ->where('payment_type', Payment::TYPE_RECEIPT)
            ->whereDate('payment_date', $date)
            ->when($shopIds && count($shopIds) > 0, fn($q) => $q->whereIn('shop_id', $shopIds))
            ->orderBy('created_at', 'desc')
            ->get();

        // 3. Sales Returns for this day
        $returns = SaleReturnItem::with('product')
            ->join('sale_return', 'sale_return.id', '=', 'sale_return_items.sale_return_id')
            ->whereDate('sale_return_items.created_at', $date)
            ->when($shopIds && count($shopIds) > 0, fn($q) => $q->whereIn('sale_return_items.shop_id', $shopIds))
            ->select('sale_return_items.*', 'sale_return.order_id', 'sale_return.customer_id')
            ->orderBy('sale_return_items.created_at', 'desc')
            ->get();

        // 4. Stock Movement by product for this day
        $soldProducts = SaleOrderItem::whereIn('order_id', $orders->pluck('id'))
            ->join('products', 'products.id', '=', 'sale_order_items.product_id')
            ->selectRaw('products.id, products.name, SUM(sale_order_items.qty) as qty_sold, SUM(sale_order_items.qty * sale_order_items.price) as revenue')
            ->groupBy('products.id', 'products.name')
            ->get()
            ->keyBy('id');

        $returnedProducts = SaleReturnItem::whereDate('sale_return_items.created_at', $date)
            ->when($shopIds && count($shopIds) > 0, fn($q) => $q->whereIn('sale_return_items.shop_id', $shopIds))
            ->join('products', 'products.id', '=', 'sale_return_items.product_id')
            ->selectRaw('products.id, products.name, SUM(sale_return_items.qty) as qty_returned')
            ->groupBy('products.id', 'products.name')
            ->get()
            ->keyBy('id');

        // Merge stock movement
        $productStockMovements = [];
        $allProductIds = $soldProducts->keys()->merge($returnedProducts->keys())->unique();
        foreach ($allProductIds as $pId) {
            $sold = $soldProducts->get($pId);
            $ret = $returnedProducts->get($pId);
            $qtySold = (int)($sold ? $sold->qty_sold : 0);
            $qtyReturned = (int)($ret ? $ret->qty_returned : 0);
            $productStockMovements[] = [
                'product_id' => $pId,
                'product_name' => $sold ? $sold->name : ($ret ? $ret->name : 'Product #' . $pId),
                'qty_sold' => $qtySold,
                'revenue' => (float)($sold ? $sold->revenue : 0),
                'qty_returned' => $qtyReturned,
                'net_movement' => $qtySold - $qtyReturned,
            ];
        }

        $formattedDate = Carbon::parse($date)->format('d-M-Y (l)');

        if ($request->ajax() && $request->get('format') === 'html') {
            return view('sale.report.daily_sales_details_partial', [
                'date' => $date,
                'formattedDate' => $formattedDate,
                'orders' => $orders,
                'receipts' => $receipts,
                'returns' => $returns,
                'stockMovements' => $productStockMovements,
            ])->render();
        }

        return response()->json([
            'status' => true,
            'date' => $date,
            'formatted_date' => $formattedDate,
            'orders' => $orders,
            'receipts' => $receipts,
            'returns' => $returns,
            'stock_movements' => $productStockMovements,
        ]);
    }

    /**
     * Export the daily summary report to CSV
     */
    public function exportCsv(Request $request)
    {
        $shopIds = $request->get('shop_ids');
        if (is_string($shopIds)) {
            $shopIds = array_filter(explode(',', $shopIds));
        }

        $dateRange = $request->get('date_range');
        $parsedDates = $this->parseDateRange($dateRange);
        $startDate = $parsedDates['start'];
        $endDate = $parsedDates['end'];

        $dataResponse = $this->data($request);
        $jsonData = $dataResponse->getData(true);
        $dailyRows = $jsonData['data'] ?? [];
        $grandTotals = $jsonData['grand_totals'] ?? [];

        $filename = "daily_sales_summary_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Date',
            'Day',
            'Orders',
            'Total Sales (Rs)',
            'Cash Sales (Rs)',
            'Online Sales (Rs)',
            'Credit Sales Billed (Rs)',
            'Credit Still Pending (Rs)',
            'Credit Collected (Rs)',
            'Customer Collections (Rs)',
            'Total Cash Inflow (Rs)',
            'Sales Returns (Rs)',
            'Net Sales (Rs)',
            'Items Sold',
            'Items Returned',
            'Net Stock Movement',
        ];

        $callback = function() use ($dailyRows, $grandTotals, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($dailyRows as $row) {
                fputcsv($file, [
                    $row['formatted_date'],
                    $row['day_name'],
                    $row['order_count'],
                    number_format($row['total_sales'], 2, '.', ''),
                    number_format($row['cash_sales'], 2, '.', ''),
                    number_format($row['online_sales'], 2, '.', ''),
                    number_format($row['credit_sales'], 2, '.', ''),
                    number_format($row['credit_pending'], 2, '.', ''),
                    number_format($row['credit_collected'], 2, '.', ''),
                    number_format($row['total_collected'], 2, '.', ''),
                    number_format($row['total_cash_inflow'], 2, '.', ''),
                    number_format($row['return_amount'], 2, '.', ''),
                    number_format($row['net_sales'], 2, '.', ''),
                    $row['items_sold'],
                    $row['items_returned'],
                    $row['net_stock_movement'],
                ]);
            }

            // Summary Totals row
            fputcsv($file, [
                'GRAND TOTAL',
                $grandTotals['days_count'] . ' Days',
                $grandTotals['order_count'],
                number_format($grandTotals['total_sales'] ?? 0, 2, '.', ''),
                number_format($grandTotals['cash_sales'] ?? 0, 2, '.', ''),
                number_format($grandTotals['online_sales'] ?? 0, 2, '.', ''),
                number_format($grandTotals['credit_sales'] ?? 0, 2, '.', ''),
                number_format($grandTotals['credit_pending'] ?? 0, 2, '.', ''),
                number_format($grandTotals['credit_collected'] ?? 0, 2, '.', ''),
                number_format($grandTotals['total_collected'] ?? 0, 2, '.', ''),
                number_format($grandTotals['total_cash_inflow'] ?? 0, 2, '.', ''),
                number_format($grandTotals['return_amount'] ?? 0, 2, '.', ''),
                number_format($grandTotals['net_sales'] ?? 0, 2, '.', ''),
                $grandTotals['items_sold'] ?? 0,
                $grandTotals['items_returned'] ?? 0,
                $grandTotals['net_stock_movement'] ?? 0,
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Parse date range string into start and end Carbon dates
     */
    protected function parseDateRange(?string $dateRange): array
    {
        $startDate = null;
        $endDate = null;

        if ($dateRange) {
            if (strpos($dateRange, '-') !== false) {
                $parts = explode('-', $dateRange);
                $startDate = $this->parseSingleDate(trim($parts[0]), true);
                $endDate = $this->parseSingleDate(trim($parts[1]), false);
            } else {
                $startDate = $this->parseSingleDate(trim($dateRange), true);
                $endDate = $this->parseSingleDate(trim($dateRange), false);
            }
        }

        return ['start' => $startDate, 'end' => $endDate];
    }

    /**
     * Parse single date string with various supported formats
     */
    protected function parseSingleDate(string $dateStr, bool $isStart): ?Carbon
    {
        $timePart = $isStart ? ' 00:00:00' : ' 23:59:59';
        $formats = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'm/d/Y'];

        foreach ($formats as $fmt) {
            try {
                return Carbon::createFromFormat($fmt . ' H:i:s', $dateStr . $timePart);
            } catch (\Exception $e) {
                // Try next format
            }
        }

        try {
            $parsed = Carbon::parse($dateStr);
            return $isStart ? $parsed->startOfDay() : $parsed->endOfDay();
        } catch (\Exception $e) {
            return null;
        }
    }
}
