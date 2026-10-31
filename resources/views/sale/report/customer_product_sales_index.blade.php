@extends('Layout.master')

@section('title')
Customer-wise Product & Transaction Sales Report
@endsection

@section('content')
<style>
    @media print {
        .no-print { display: none !important; }
        .breadcrumbs { display: none !important; }
        .page-header { margin-bottom: 5px; }
        body { background-color: #fff; font-size: 10px; }
        .main-content { margin-left: 0 !important; }
        .widget-box { border: none !important; margin-bottom: 5px !important; }
        .widget-header { display: none !important; }
        .widget-body { border: none !important; }
        .table-report {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        .table-report th, .table-report td {
            border: 1px solid #444 !important;
            padding: 3px 4px !important;
            font-size: 9px !important;
        }
        @page {
            size: A4 landscape;
            margin: 8mm 6mm;
        }
        thead {
            display: table-header-group !important;
        }
        tfoot {
            display: table-footer-group !important;
        }
        tr {
            page-break-inside: avoid !important;
        }
    }

    /* KPI Summary Cards */
    .kpi-row {
        display: flex;
        flex-wrap: wrap;
        margin-left: -5px;
        margin-right: -5px;
        margin-bottom: 15px;
    }
    .kpi-col {
        flex: 1;
        min-width: 160px;
        padding-left: 5px;
        padding-right: 5px;
        margin-bottom: 8px;
    }
    .kpi-card {
        border-radius: 6px;
        padding: 12px 14px;
        color: #fff;
        height: 100%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        transition: transform 0.15s ease-in-out;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
    }
    .kpi-title {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.95;
        font-weight: 600;
    }
    .kpi-amount {
        font-size: 18px;
        font-weight: bold;
        margin-top: 5px;
        margin-bottom: 2px;
        white-space: nowrap;
    }
    .kpi-sub {
        font-size: 10px;
        opacity: 0.85;
    }

    /* Table Styles */
    .table-report thead th {
        background-color: #2c3e50 !important;
        color: #ffffff !important;
        font-size: 11px !important;
        text-align: center;
        vertical-align: middle !important;
        padding: 6px 4px !important;
        white-space: nowrap;
    }
    .table-report tbody td {
        font-size: 11px;
        padding: 5px 6px;
        vertical-align: middle;
    }
    .table-report tfoot td {
        font-size: 11px;
        font-weight: bold;
        background-color: #f1f4f7;
        vertical-align: middle;
        padding: 6px 4px;
    }

    .badge-dr {
        background-color: #d9534f !important;
        color: #fff;
        padding: 2px 6px;
        border-radius: 3px;
        font-weight: bold;
        font-size: 10px;
    }
    .badge-cr {
        background-color: #27ae60 !important;
        color: #fff;
        padding: 2px 6px;
        border-radius: 3px;
        font-weight: bold;
        font-size: 10px;
    }
    .select2-container .select2-selection--single {
        height: 34px !important;
        border: 1px solid #d5d5d5 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px !important;
    }
</style>

<div class="page-content">
    <div class="page-header no-print">
        <h1>
            <i class="ace-icon fa fa-file-text-o blue"></i> Customer-wise Product & Transaction Sales Report
            <small>
                <i class="ace-icon fa fa-angle-double-right"></i>
                Detailed product-level sales breakdown, commissions, discounts, debit/credit & running ledger balance
            </small>
        </h1>
    </div>

    <!-- Filter Card -->
    <div class="widget-box widget-color-blue2 no-print" style="margin-bottom: 20px;">
        <div class="widget-header">
            <h5 class="widget-title bigger lighter">
                <i class="ace-icon fa fa-filter"></i> Report Parameters & Filters
            </h5>
            <div class="widget-toolbar">
                <a href="#" data-action="collapse">
                    <i class="ace-icon fa fa-chevron-up"></i>
                </a>
            </div>
        </div>

        <div class="widget-body">
            <div class="widget-main" style="padding: 16px;">
                <form method="GET" action="{{ route('report.customer_sales') }}" id="report_filter_form" class="form-horizontal">
                    <div class="row">
                        <!-- Customer Dropdown (Searchable Select2) -->
                        <div class="col-md-5 col-sm-12">
                            <label for="customer_id" style="font-weight: 600; margin-bottom: 4px;">
                                <i class="fa fa-user blue"></i> Select Customer: <span class="text-danger">*</span>
                            </label>
                            <select name="customer_id" id="customer_id" class="form-control select2" style="width: 100%;" required>
                                <option value="">-- Choose or Search Customer by Name / Mobile --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" {{ (string)$selectedCustomerId === (string)$c->id ? 'selected' : '' }}>
                                        {{ $c->name }} {{ $c->mobile ? ' (' . $c->mobile . ')' : '' }} {{ $c->address ? ' - ' . \Illuminate\Support\Str::limit($c->address, 25) : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Start Date -->
                        <div class="col-md-3 col-sm-6">
                            <label for="start_date" style="font-weight: 600; margin-bottom: 4px;">
                                <i class="fa fa-calendar blue"></i> Start Date:
                            </label>
                            <input type="date" class="form-control" name="start_date" id="start_date" value="{{ $startDate }}">
                        </div>

                        <!-- End Date -->
                        <div class="col-md-3 col-sm-6">
                            <label for="end_date" style="font-weight: 600; margin-bottom: 4px;">
                                <i class="fa fa-calendar blue"></i> End Date:
                            </label>
                            <input type="date" class="form-control" name="end_date" id="end_date" value="{{ $endDate }}">
                        </div>

                        <!-- Action Button -->
                        <div class="col-md-1 col-sm-12 text-center" style="margin-top: 24px;">
                            <button type="submit" class="btn btn-sm btn-primary btn-block" title="Generate Report">
                                <i class="ace-icon fa fa-check bigger-110"></i> Generate
                            </button>
                        </div>
                    </div>

                    <!-- Quick Date Presets -->
                    <div class="row" style="margin-top: 12px; padding-top: 10px; border-top: 1px dashed #e2e8f0;">
                        <div class="col-xs-12">
                            <span class="text-muted" style="font-size: 11px; margin-right: 8px;"><strong>Quick Ranges:</strong></span>
                            <button type="button" class="btn btn-minier btn-white btn-default" onclick="setDateRange('today');">Today</button>
                            <button type="button" class="btn btn-minier btn-white btn-default" onclick="setDateRange('this_month');">This Month</button>
                            <button type="button" class="btn btn-minier btn-white btn-default" onclick="setDateRange('last_30_days');">Last 30 Days</button>
                            <button type="button" class="btn btn-minier btn-white btn-default" onclick="setDateRange('all_time');">All Time (Inception)</button>

                            @if($selectedCustomerId || $startDate || $endDate)
                                <a href="{{ route('report.customer_sales') }}" class="btn btn-minier btn-danger pull-right" style="margin-left: 8px;">
                                    <i class="fa fa-refresh"></i> Reset All Filters
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(!$selectedCustomerId)
        <!-- Welcome / Instructions Placeholder -->
        <div class="well well-lg text-center" style="background-color: #fcfcfc; border: 1px dashed #cbd5e1; margin-top: 20px; padding: 40px;">
            <i class="ace-icon fa fa-search fa-3x light-blue" style="margin-bottom: 15px;"></i>
            <h3 class="blue lighter" style="margin-top: 5px;">Select a Customer to Generate Sales Report</h3>
            <p class="text-muted" style="max-width: 650px; margin: 0 auto; font-size: 13px;">
                Choose any customer from the searchable dropdown above and specify an optional start and end date.
                The system will automatically compute the opening balance, list all individual product line items with their Vehicle & Bilty numbers, calculate applied commissions and discounts, and generate an exact running balance after every single transaction.
            </p>
        </div>
    @elseif($customer && $report)
        <!-- Customer Details & Action Bar -->
        <div class="widget-box widget-color-dark" style="margin-bottom: 15px;">
            <div class="widget-header">
                <h5 class="widget-title bigger lighter">
                    <i class="ace-icon fa fa-user"></i> {{ $customer->name }}
                    <small style="color: #cbd5e1; margin-left: 8px;">
                        [Code: {{ $report['account']->code }} | Mobile: {{ $customer->mobile ?: 'N/A' }} | CNIC: {{ $customer->cnic ?: 'N/A' }}]
                    </small>
                </h5>

                <div class="widget-toolbar no-print">
                    <!-- Download PDF -->
                    <a href="{{ route('report.customer_sales.pdf', ['customer_id' => $customer->id, 'start_date' => $startDate, 'end_date' => $endDate, 'view' => 'download']) }}" class="btn btn-xs btn-danger" title="Download Official PDF Report">
                        <i class="fa fa-file-pdf-o"></i> Download PDF
                    </a>

                    <!-- Print / PDF View -->
                    <a href="{{ route('report.customer_sales.pdf', ['customer_id' => $customer->id, 'start_date' => $startDate, 'end_date' => $endDate, 'view' => 'print_view']) }}" target="_blank" class="btn btn-xs btn-primary" title="Print Friendly View">
                        <i class="fa fa-print"></i> Print / Preview
                    </a>

                    <!-- Export CSV -->
                    <a href="{{ route('report.customer_sales.csv', ['customer_id' => $customer->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-xs btn-success" title="Export to Excel / CSV">
                        <i class="fa fa-file-excel-o"></i> Export CSV
                    </a>

                    <!-- Link to Customer Ledger Statement -->
                    <a href="{{ route('customer.ledger.show', $customer->id) }}" target="_blank" class="btn btn-xs btn-info" title="View Master Ledger Statement">
                        <i class="fa fa-book"></i> Ledger Statement
                    </a>
                </div>
            </div>

            <div class="widget-body">
                <div class="widget-main" style="padding: 10px 15px; background-color: #fafbfc;">
                    <div class="row" style="font-size: 12px;">
                        <div class="col-sm-4">
                            <strong>Customer Name:</strong> {{ $customer->name }}<br>
                            <strong>Contact / Mobile:</strong> {{ $customer->mobile ?: 'N/A' }}<br>
                            <strong>CNIC Number:</strong> {{ $customer->cnic ?: 'N/A' }}
                        </div>
                        <div class="col-sm-4">
                            <strong>Shipping / Street Address:</strong> {{ $customer->address ?: 'N/A' }}<br>
                            <strong>Email Address:</strong> {{ $customer->email ?: 'N/A' }}<br>
                            <strong>Account Code:</strong> <code>{{ $report['account']->code }}</code>
                        </div>
                        <div class="col-sm-4 text-right">
                            <strong>Statement Period:</strong><br>
                            <span class="badge badge-info" style="font-size: 11px;">
                                <i class="fa fa-calendar"></i>
                                {{ $report['start_date'] ? date('d-M-Y', strtotime($report['start_date'])) : 'Inception' }}
                                &nbsp;to&nbsp;
                                {{ $report['end_date'] ? date('d-M-Y', strtotime($report['end_date'])) : 'Present' }}
                            </span><br>
                            <span class="text-muted" style="font-size: 10px;">Generated At: {{ $report['generated_at'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7 KPI Summary Cards -->
        <div class="kpi-row no-print">
            <!-- Opening Balance Card -->
            <div class="kpi-col">
                <div class="kpi-card" style="background: linear-gradient(135deg, #475569, #334155);">
                    <div class="kpi-title"><i class="fa fa-hourglass-start"></i> Opening Balance</div>
                    <div class="kpi-amount">
                        PKR {{ number_format($report['opening_balance_amount'], 2) }}
                    </div>
                    <div class="kpi-sub">
                        Status: <span class="badge {{ $report['opening_balance'] >= 0 ? 'badge-dr' : 'badge-cr' }}">{{ $report['opening_balance_dr_cr'] }}</span>
                        as of {{ $report['start_date'] ? date('d/m/Y', strtotime($report['start_date'])) : 'Inception' }}
                    </div>
                </div>
            </div>

            <!-- Total Quantity Sold -->
            <div class="kpi-col">
                <div class="kpi-card" style="background: linear-gradient(135deg, #0284c7, #0369a1);">
                    <div class="kpi-title"><i class="fa fa-cubes"></i> Total Qty Sold</div>
                    <div class="kpi-amount">{{ number_format($report['total_quantity_sold']) }} Bags</div>
                    <div class="kpi-sub">Across All Order Line Items</div>
                </div>
            </div>

            <!-- Total Sale Amount -->
            <div class="kpi-col">
                <div class="kpi-card" style="background: linear-gradient(135deg, #7c3aed, #6d28d9);">
                    <div class="kpi-title"><i class="fa fa-shopping-cart"></i> Total Sale Amount</div>
                    <div class="kpi-amount">PKR {{ number_format($report['total_sale_amount'], 2) }}</div>
                    <div class="kpi-sub">Gross Product Sales (Qty &times; Rate)</div>
                </div>
            </div>

            <!-- Commission & Discount -->
            <div class="kpi-col">
                <div class="kpi-card" style="background: linear-gradient(135deg, #d97706, #b45309);">
                    <div class="kpi-title"><i class="fa fa-percent"></i> Comm. & Discount</div>
                    <div class="kpi-amount">PKR {{ number_format($report['total_commission_amount'] + $report['total_discount_amount'], 2) }}</div>
                    <div class="kpi-sub">Comm: {{ number_format($report['total_commission_amount'], 2) }} | Disc: {{ number_format($report['total_discount_amount'], 2) }}</div>
                </div>
            </div>

            <!-- Total Debit Charged -->
            <div class="kpi-col">
                <div class="kpi-card" style="background: linear-gradient(135deg, #dc2626, #b91c1c);">
                    <div class="kpi-title"><i class="fa fa-arrow-up"></i> Total Debit</div>
                    <div class="kpi-amount">PKR {{ number_format($report['total_debit'], 2) }}</div>
                    <div class="kpi-sub">Net Sales Billed to Customer</div>
                </div>
            </div>

            <!-- Total Credit Received -->
            <div class="kpi-col">
                <div class="kpi-card" style="background: linear-gradient(135deg, #059669, #047857);">
                    <div class="kpi-title"><i class="fa fa-arrow-down"></i> Total Credit</div>
                    <div class="kpi-amount">PKR {{ number_format($report['total_credit'], 2) }}</div>
                    <div class="kpi-sub">Payments & Returns Received</div>
                </div>
            </div>

            <!-- Closing Balance Card -->
            <div class="kpi-col">
                <div class="kpi-card" style="background: linear-gradient(135deg, {{ $report['closing_balance'] > 0 ? '#b91c1c, #991b1b' : '#059669, #047857' }});">
                    <div class="kpi-title"><i class="fa fa-balance-scale"></i> Closing Balance</div>
                    <div class="kpi-amount">
                        PKR {{ number_format($report['closing_balance_amount'], 2) }}
                    </div>
                    <div class="kpi-sub">
                        Status: <span class="badge {{ $report['closing_balance'] >= 0 ? 'badge-dr' : 'badge-cr' }}">{{ $report['closing_balance_dr_cr'] }}</span>
                        ({{ $report['closing_balance'] > 0 ? 'Customer Debt Receivable' : ($report['closing_balance'] < 0 ? 'Advance Credit' : 'Zero Due') }})
                    </div>
                </div>
            </div>
        </div>

        <!-- The Detailed 16-Column Sales Report Table -->
        <div class="widget-box widget-color-blue" style="margin-top: 10px;">
            <div class="widget-header">
                <h5 class="widget-title bigger lighter">
                    <i class="ace-icon fa fa-table"></i> Product-wise Transaction Sales Ledger
                    <small style="color: #fff; margin-left: 8px;">({{ count($report['rows']) }} transactions in period)</small>
                </h5>
                <div class="widget-toolbar no-print">
                    <button class="btn btn-xs btn-default" onclick="window.print();" title="Print this Table">
                        <i class="fa fa-print"></i> Quick Print
                    </button>
                </div>
            </div>

            <div class="widget-body">
                <div class="widget-main no-padding">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover table-report" id="customer_sales_report_table" style="margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">Trans. Date</th>
                                    <th>Product / Transaction Name</th>
                                    <th style="width: 75px;">Order ID</th>
                                    <th style="width: 85px;">Vehicle #</th>
                                    <th style="width: 75px;">Bilty #</th>
                                    <th style="width: 60px;">Quantity</th>
                                    <th style="width: 70px;">Rate</th>
                                    <th style="width: 85px;">Total Sale</th>
                                    <th style="width: 65px;">Comm. %</th>
                                    <th style="width: 80px;">Comm. Amount</th>
                                    <th style="width: 65px;">Disc. Rate</th>
                                    <th style="width: 75px;">Disc. Amount</th>
                                    <th style="width: 90px;">Debit</th>
                                    <th style="width: 90px;">Credit</th>
                                    <th style="width: 65px;">Balance (DR/CR)</th>
                                    <th style="width: 105px;">Balance Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Opening Balance Row -->
                                <tr style="background-color: #fff9e6; font-weight: bold; border-bottom: 2px solid #e2e8f0;">
                                    <td class="text-center">{{ $report['start_date'] ? date('d/m/Y', strtotime($report['start_date'])) : '-' }}</td>
                                    <td colspan="11">
                                        <i class="fa fa-flag text-warning"></i>
                                        <strong>OPENING BALANCE</strong>
                                        <span class="text-muted" style="font-size: 11px; font-weight: normal; margin-left: 6px;">
                                            (Outstanding balance carried forward as of {{ $report['start_date'] ? date('d/m/Y', strtotime($report['start_date'])) : 'Account Inception' }})
                                        </span>
                                    </td>
                                    <td class="text-right">-</td>
                                    <td class="text-right">-</td>
                                    <td class="text-center">
                                        <span class="badge {{ $report['opening_balance'] >= 0 ? 'badge-dr' : 'badge-cr' }}">
                                            {{ $report['opening_balance_dr_cr'] }}
                                        </span>
                                    </td>
                                    <td class="text-right" style="font-weight: bold; color: {{ $report['opening_balance'] > 0 ? '#c53030' : '#27ae60' }}; font-size: 12px;">
                                        PKR {{ number_format($report['opening_balance_amount'], 2) }}
                                    </td>
                                </tr>

                                @forelse($report['rows'] as $r)
                                    <tr style="{{ $r['row_type'] === 'order_payment' || $r['row_type'] === 'voucher_payment' ? 'background-color: #f0fdf4;' : ($r['row_type'] === 'sale_return' ? 'background-color: #fef2f2;' : '') }}">
                                        <!-- 1. Transaction Date -->
                                        <td class="text-center" style="white-space: nowrap;">
                                            {{ $r['date'] }}
                                        </td>

                                        <!-- 2. Product / Transaction Name -->
                                        <td>
                                            @if($r['row_type'] === 'sale_product')
                                                <strong>{{ $r['particulars'] }}</strong>
                                            @elseif($r['row_type'] === 'order_payment')
                                                <span class="text-success"><i class="fa fa-money"></i> {{ $r['particulars'] }}</span>
                                            @elseif($r['row_type'] === 'voucher_payment')
                                                <span class="text-primary"><i class="fa fa-receipt"></i> {{ $r['particulars'] }}</span>
                                            @elseif($r['row_type'] === 'sale_return')
                                                <span class="text-danger"><i class="fa fa-undo"></i> {{ $r['particulars'] }}</span>
                                            @else
                                                <span>{{ $r['particulars'] }}</span>
                                            @endif
                                        </td>

                                        <!-- 3. Order ID -->
                                        <td class="text-center">
                                            @if(!empty($r['order_id_num']) && $r['order_id_num'] > 0)
                                                <a href="{{ route('sale.order.detail', $r['order_id_num']) }}" target="_blank" title="View Order Details" class="no-print">
                                                    #{{ $r['order_id'] }} <i class="fa fa-external-link" style="font-size: 9px;"></i>
                                                </a>
                                                <span class="print-only" style="display: none;">#{{ $r['order_id'] }}</span>
                                            @else
                                                {{ $r['order_id'] }}
                                            @endif
                                        </td>

                                        <!-- 4. Vehicle Number -->
                                        <td class="text-center" style="white-space: nowrap;">
                                            {{ $r['vehicle_no'] }}
                                        </td>

                                        <!-- 5. Bilty Number -->
                                        <td class="text-center" style="white-space: nowrap;">
                                            {{ $r['bilty_no'] }}
                                        </td>

                                        <!-- 6. Quantity -->
                                        <td class="text-center" style="font-weight: {{ $r['qty'] !== null ? '600' : 'normal' }};">
                                            {{ $r['qty'] !== null ? number_format($r['qty']) : '-' }}
                                        </td>

                                        <!-- 7. Rate -->
                                        <td class="text-right">
                                            {{ $r['rate'] !== null && $r['rate'] > 0 ? number_format($r['rate'], 2) : '-' }}
                                        </td>

                                        <!-- 8. Total Sale -->
                                        <td class="text-right" style="font-weight: {{ $r['total_sale'] > 0 ? '600' : 'normal' }};">
                                            {{ $r['total_sale'] > 0 ? number_format($r['total_sale'], 2) : '-' }}
                                        </td>

                                        <!-- 9. Commission % -->
                                        <td class="text-center">
                                            {{ $r['commission_percentage'] > 0 ? number_format($r['commission_percentage'], 2) . '%' : '-' }}
                                        </td>

                                        <!-- 10. Commission Amount -->
                                        <td class="text-right" style="color: {{ $r['commission_amount'] > 0 ? '#059669' : 'inherit' }};">
                                            {{ $r['commission_amount'] > 0 ? number_format($r['commission_amount'], 2) : '-' }}
                                        </td>

                                        <!-- 11. Discount Rate -->
                                        <td class="text-center">
                                            {{ $r['discount_rate'] > 0 ? number_format($r['discount_rate'], 2) . '%' : '-' }}
                                        </td>

                                        <!-- 12. Discount Amount -->
                                        <td class="text-right" style="color: {{ $r['discount_amount'] > 0 ? '#d97706' : 'inherit' }};">
                                            {{ $r['discount_amount'] > 0 ? number_format($r['discount_amount'], 2) : '-' }}
                                        </td>

                                        <!-- 13. Debit -->
                                        <td class="text-right" style="font-weight: {{ $r['debit'] > 0 ? 'bold' : 'normal' }}; color: {{ $r['debit'] > 0 ? '#b91c1c' : 'inherit' }};">
                                            {{ $r['debit'] > 0 ? number_format($r['debit'], 2) : '-' }}
                                        </td>

                                        <!-- 14. Credit -->
                                        <td class="text-right" style="font-weight: {{ $r['credit'] > 0 ? 'bold' : 'normal' }}; color: {{ $r['credit'] > 0 ? '#047857' : 'inherit' }};">
                                            {{ $r['credit'] > 0 ? number_format($r['credit'], 2) : '-' }}
                                        </td>

                                        <!-- 15. Balance (DR/CR) -->
                                        <td class="text-center">
                                            <span class="badge {{ $r['balance_dr_cr'] === 'DR' ? 'badge-dr' : 'badge-cr' }}">
                                                {{ $r['balance_dr_cr'] }}
                                            </span>
                                        </td>

                                        <!-- 16. Balance Amount -->
                                        <td class="text-right" style="font-weight: bold; color: {{ $r['running_balance'] > 0 ? '#b91c1c' : '#047857' }};">
                                            {{ number_format($r['balance_amount'], 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="16" class="text-center text-muted" style="padding: 35px;">
                                            <i class="fa fa-info-circle fa-2x"></i><br>
                                            No sales, products, or payment transactions found for <strong>{{ $customer->name }}</strong> in the selected period.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <!-- Summary Footer Row -->
                                <tr style="background-color: #2c3e50; color: #ffffff;">
                                    <td colspan="5" class="text-right" style="color: #ffffff; font-size: 12px;">
                                        <strong>PERIOD TOTALS:</strong>
                                    </td>
                                    <td class="text-center" style="color: #ffffff;">
                                        <strong>{{ number_format($report['total_quantity_sold']) }}</strong>
                                    </td>
                                    <td></td>
                                    <td class="text-right" style="color: #ffffff;">
                                        <strong>{{ number_format($report['total_sale_amount'], 2) }}</strong>
                                    </td>
                                    <td></td>
                                    <td class="text-right" style="color: #a7f3d0;">
                                        <strong>{{ number_format($report['total_commission_amount'], 2) }}</strong>
                                    </td>
                                    <td></td>
                                    <td class="text-right" style="color: #fde68a;">
                                        <strong>{{ number_format($report['total_discount_amount'], 2) }}</strong>
                                    </td>
                                    <td class="text-right" style="color: #fca5a5;">
                                        <strong>{{ number_format($report['total_debit'], 2) }}</strong>
                                    </td>
                                    <td class="text-right" style="color: #86efac;">
                                        <strong>{{ number_format($report['total_credit'], 2) }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $report['closing_balance_dr_cr'] === 'DR' ? 'badge-dr' : 'badge-cr' }}">
                                            {{ $report['closing_balance_dr_cr'] }}
                                        </span>
                                    </td>
                                    <td class="text-right" style="color: #ffffff; font-size: 13px;">
                                        <strong>{{ number_format($report['closing_balance_amount'], 2) }}</strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4 Report Summary Box -->
        <div class="widget-box widget-color-blue" style="margin-top: 20px;">
            <div class="widget-header">
                <h5 class="widget-title bigger lighter">
                    <i class="ace-icon fa fa-calculator"></i> Customer Sales Report Summary & Balance Reconciliation
                </h5>
            </div>

            <div class="widget-body">
                <div class="widget-main" style="padding: 16px;">
                    <div class="row">
                        <div class="col-sm-6">
                            <table class="table table-bordered table-striped" style="margin-bottom: 0;">
                                <tr>
                                    <td style="width: 55%;"><strong>Opening Balance:</strong></td>
                                    <td class="text-right">
                                        <span class="badge {{ $report['opening_balance'] >= 0 ? 'badge-dr' : 'badge-cr' }}">{{ $report['opening_balance_dr_cr'] }}</span>
                                        <strong>PKR {{ number_format($report['opening_balance_amount'], 2) }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Quantity Sold (Bags / Units):</strong></td>
                                    <td class="text-right"><strong>{{ number_format($report['total_quantity_sold']) }} Bags</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Total Sale Amount (Gross):</strong></td>
                                    <td class="text-right" style="color: #7c3aed;"><strong>PKR {{ number_format($report['total_sale_amount'], 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Total Commission Amount:</strong></td>
                                    <td class="text-right text-success">PKR {{ number_format($report['total_commission_amount'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Discount Amount:</strong></td>
                                    <td class="text-right text-warning">PKR {{ number_format($report['total_discount_amount'], 2) }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-sm-6">
                            <table class="table table-bordered table-striped" style="margin-bottom: 0;">
                                <tr>
                                    <td style="width: 55%;"><strong>Total Debit (Net Billed):</strong></td>
                                    <td class="text-right text-danger" style="font-size: 13px;">
                                        <strong>PKR {{ number_format($report['total_debit'], 2) }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Credit (Payments & Returns):</strong></td>
                                    <td class="text-right text-success" style="font-size: 13px;">
                                        <strong>PKR {{ number_format($report['total_credit'], 2) }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Net Period Movement (Debit - Credit):</strong></td>
                                    <td class="text-right">
                                        <strong>PKR {{ number_format($report['total_debit'] - $report['total_credit'], 2) }}</strong>
                                    </td>
                                </tr>
                                <tr style="background-color: #f1f5f9; border-top: 2px solid #cbd5e1;">
                                    <td>
                                        <strong style="font-size: 14px;">Closing Balance:</strong><br>
                                        <small class="text-muted">(Opening + Total Debit - Total Credit)</small>
                                    </td>
                                    <td class="text-right" style="font-size: 16px; font-weight: bold; color: {{ $report['closing_balance'] > 0 ? '#b91c1c' : '#047857' }};">
                                        <span class="badge {{ $report['closing_balance_dr_cr'] === 'DR' ? 'badge-dr' : 'badge-cr' }}" style="font-size: 12px; margin-right: 4px;">
                                            {{ $report['closing_balance_dr_cr'] }}
                                        </span>
                                        PKR {{ number_format($report['closing_balance_amount'], 2) }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('script')
<script type="text/javascript">
$(document).ready(function() {
    // Initialize Select2 on customer dropdown
    if ($.fn.select2) {
        $('#customer_id').select2({
            placeholder: "-- Search Customer by Name or Mobile --",
            allowClear: true,
            width: '100%'
        });
    }

    @if($isPrint)
        window.print();
    @endif
});

function setDateRange(type) {
    var today = new Date();
    var yyyy = today.getFullYear();
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var dd = String(today.getDate()).padStart(2, '0');
    var todayStr = yyyy + '-' + mm + '-' + dd;

    if (type === 'today') {
        $('#start_date').val(todayStr);
        $('#end_date').val(todayStr);
    } else if (type === 'this_month') {
        var firstDay = yyyy + '-' + mm + '-01';
        $('#start_date').val(firstDay);
        $('#end_date').val(todayStr);
    } else if (type === 'last_30_days') {
        var past30 = new Date();
        past30.setDate(past30.getDate() - 30);
        var pYyyy = past30.getFullYear();
        var pMm = String(past30.getMonth() + 1).padStart(2, '0');
        var pDd = String(past30.getDate()).padStart(2, '0');
        $('#start_date').val(pYyyy + '-' + pMm + '-' + pDd);
        $('#end_date').val(todayStr);
    } else if (type === 'all_time') {
        $('#start_date').val('');
        $('#end_date').val('');
    }
}
</script>
@endsection
