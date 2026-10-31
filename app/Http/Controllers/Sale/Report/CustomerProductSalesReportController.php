<?php

namespace App\Http\Controllers\Sale\Report;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\CustomerProductSalesReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerProductSalesReportController extends Controller
{
    protected CustomerProductSalesReportService $reportService;

    public function __construct(CustomerProductSalesReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display Customer-wise Product & Transaction Sales Report
     */
    public function index(Request $request)
    {
        $customers = Customer::orderBy('name', 'asc')->get();

        $selectedCustomerId = $request->get('customer_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $reportData = null;
        $customer = null;

        if ($selectedCustomerId) {
            $customer = Customer::find($selectedCustomerId);
            if ($customer) {
                $startSql = $this->parseDateToSql($startDate);
                $endSql   = $this->parseDateToSql($endDate);

                $reportData = $this->reportService->generateReport($customer, $startSql, $endSql);
            }
        }

        // Handle AJAX partial request if requested
        if ($request->ajax() && $request->get('format') === 'html') {
            return view('sale.report.customer_product_sales_partial', [
                'report'    => $reportData,
                'customer'  => $customer,
                'startDate' => $startDate,
                'endDate'   => $endDate,
            ])->render();
        }

        return view('sale.report.customer_product_sales_index', [
            'customers'          => $customers,
            'selectedCustomerId' => $selectedCustomerId,
            'customer'           => $customer,
            'startDate'          => $startDate,
            'endDate'            => $endDate,
            'report'             => $reportData,
            'isPrint'            => $request->get('view') === 'print',
        ]);
    }

    /**
     * Export Report to PDF
     */
    public function exportPdf(Request $request)
    {
        $customerId = $request->get('customer_id');
        if (!$customerId) {
            return redirect()->back()->with('error', 'Please select a customer to generate PDF.');
        }

        $customer = Customer::findOrFail($customerId);
        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');

        $startSql = $this->parseDateToSql($startDate);
        $endSql   = $this->parseDateToSql($endDate);

        $report = $this->reportService->generateReport($customer, $startSql, $endSql);

        $viewMode = $request->get('view', 'download'); // download, stream, or print_view

        if ($viewMode === 'print_view') {
            return view('sale.report.customer_product_sales_pdf', [
                'report'    => $report,
                'customer'  => $customer,
                'startDate' => $startDate,
                'endDate'   => $endDate,
                'isHtml'    => true,
            ]);
        }

        $custSafeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $customer->name);
        $filename = "Customer_Sales_Report_{$custSafeName}_" . date('Ymd_His') . ".pdf";

        $pdf = Pdf::loadView('sale.report.customer_product_sales_pdf', [
            'report'    => $report,
            'customer'  => $customer,
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'isHtml'    => false,
        ])
        ->setPaper('a4', 'landscape')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true,
            'defaultFont'          => 'sans-serif',
        ]);

        if ($viewMode === 'stream') {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    /**
     * Export Report to CSV
     */
    public function exportCsv(Request $request)
    {
        $customerId = $request->get('customer_id');
        if (!$customerId) {
            return redirect()->back()->with('error', 'Please select a customer to export CSV.');
        }

        $customer = Customer::findOrFail($customerId);
        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');

        $startSql = $this->parseDateToSql($startDate);
        $endSql   = $this->parseDateToSql($endDate);

        $report = $this->reportService->generateReport($customer, $startSql, $endSql);

        $custSafeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $customer->name);
        $filename = "Customer_Sales_Report_{$custSafeName}_" . date('Ymd_His') . ".csv";

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($customer, $report, $startDate, $endDate) {
            $file = fopen('php://output', 'w');

            // Header info
            fputcsv($file, ['AWAN AGRO - CUSTOMER PRODUCT & TRANSACTION SALES REPORT']);
            fputcsv($file, ['Customer Name:', $customer->name]);
            fputcsv($file, ['Customer Mobile:', $customer->mobile ?? 'N/A']);
            fputcsv($file, ['Address:', $customer->address ?? 'N/A']);
            fputcsv($file, ['Report Period:', ($startDate ?: 'Inception') . ' to ' . ($endDate ?: 'Present')]);
            fputcsv($file, ['Generated At:', $report['generated_at']]);
            fputcsv($file, []);

            // Columns
            fputcsv($file, [
                'Transaction Date',
                'Product / Transaction Name',
                'Order ID',
                'Vehicle Number',
                'Bilty Number',
                'Quantity',
                'Rate',
                'Total Sale',
                'Commission %',
                'Commission Amount',
                'Discount Rate %',
                'Discount Amount',
                'Debit',
                'Credit',
                'Balance (DR/CR)',
                'Balance Amount',
            ]);

            // Opening Balance row
            fputcsv($file, [
                $report['start_date'] ? date('d/m/Y', strtotime($report['start_date'])) : '-',
                'OPENING BALANCE',
                '-',
                '-',
                '-',
                '-',
                '-',
                '-',
                '-',
                '-',
                '-',
                '-',
                '-',
                '-',
                $report['opening_balance_dr_cr'],
                number_format($report['opening_balance_amount'], 2, '.', ''),
            ]);

            // Transaction rows
            foreach ($report['rows'] as $r) {
                fputcsv($file, [
                    $r['date'],
                    $r['particulars'],
                    $r['order_id'],
                    $r['vehicle_no'],
                    $r['bilty_no'],
                    $r['qty'] !== null ? $r['qty'] : '-',
                    $r['rate'] !== null ? number_format($r['rate'], 2, '.', '') : '-',
                    $r['total_sale'] > 0 ? number_format($r['total_sale'], 2, '.', '') : '0.00',
                    $r['commission_percentage'] > 0 ? number_format($r['commission_percentage'], 2, '.', '') . '%' : '0.00',
                    $r['commission_amount'] > 0 ? number_format($r['commission_amount'], 2, '.', '') : '0.00',
                    $r['discount_rate'] > 0 ? number_format($r['discount_rate'], 2, '.', '') . '%' : '0.00',
                    $r['discount_amount'] > 0 ? number_format($r['discount_amount'], 2, '.', '') : '0.00',
                    $r['debit'] > 0 ? number_format($r['debit'], 2, '.', '') : '0.00',
                    $r['credit'] > 0 ? number_format($r['credit'], 2, '.', '') : '0.00',
                    $r['balance_dr_cr'],
                    number_format($r['balance_amount'], 2, '.', ''),
                ]);
            }

            // Summary Totals row
            fputcsv($file, [
                'CLOSING SUMMARY',
                'Total Period Summary',
                '-',
                '-',
                '-',
                number_format($report['total_quantity_sold'], 2, '.', ''),
                '-',
                number_format($report['total_sale_amount'], 2, '.', ''),
                '-',
                number_format($report['total_commission_amount'], 2, '.', ''),
                '-',
                number_format($report['total_discount_amount'], 2, '.', ''),
                number_format($report['total_debit'], 2, '.', ''),
                number_format($report['total_credit'], 2, '.', ''),
                $report['closing_balance_dr_cr'],
                number_format($report['closing_balance_amount'], 2, '.', ''),
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Helper to parse date inputs (d/m/Y, Y-m-d, d-m-Y) to Y-m-d
     */
    protected function parseDateToSql(?string $dateStr): ?string
    {
        if (empty($dateStr)) {
            return null;
        }

        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y'];
        foreach ($formats as $fmt) {
            try {
                return Carbon::createFromFormat($fmt, trim($dateStr))->toDateString();
            } catch (\Exception $e) {
                // Try next format
            }
        }

        try {
            return Carbon::parse($dateStr)->toDateString();
        } catch (\Exception $e) {
            return null;
        }
    }
}
