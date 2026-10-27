@extends('Layout.master')

@php 
    $title = 'Daily Sales Summary Report';
@endphp

@section('title')
{{ $title }}
@endsection

@section('content')
<div class="page-content">
    <div class="page-header" style="min-height: 40px; margin-bottom: 15px; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px;">
        <div class="pull-left">
            <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0;">
                <i class="menu-icon fa fa-calendar-check-o text-primary" style="margin-right: 8px;"></i>
                {{ $title }}
            </h1>
            <p class="text-muted" style="margin: 4px 0 0 0; font-size: 13px;">
                Reconcile daily sales, cash receipts, online bank transfers, outstanding credit collections, and inventory stock movements.
            </p>
        </div>
        <div class="pull-right" style="margin-top: 5px;">
            <button class="btn btn-sm btn-white btn-default btn-round" onclick="window.printReport();" title="Print Reconciliation Sheet">
                <i class="ace-icon fa fa-print bigger-120 text-info"></i> Print Report
            </button>
            <a href="javascript:void(0);" onclick="exportReportCsv();" class="btn btn-sm btn-white btn-success btn-round" style="margin-left: 5px;" title="Export to CSV">
                <i class="ace-icon fa fa-file-excel-o bigger-120 text-success"></i> Export CSV
            </a>
        </div>
        <div class="clearfix"></div>
    </div>

    <!-- Filter Section -->
    <div class="row">
        <div class="col-xs-12">
            <div class="widget-box" id="filter-widget" style="border: 1px solid #dce4ec; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div class="widget-header widget-header-small" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                    <h5 class="widget-title" style="color: #334155; font-weight: 600;">
                        <i class="ace-icon fa fa-filter text-primary"></i> Report Filters & Quick Presets
                    </h5>
                    <div class="widget-toolbar">
                        <a href="#" data-action="collapse">
                            <i class="ace-icon fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="widget-body">
                    <div class="widget-main" style="padding: 15px;">
                        <form id="filterForm">
                            <div class="row">
                                <div class="col-md-3 col-sm-6">
                                    <label style="font-weight: 600; color: #475569; font-size: 12px;">Shops</label>
                                    <select name="shop_ids[]" id="shop_ids" data-placeholder="All Shops" class="col-xs-12 chosen-multi-select" multiple>
                                        @foreach($shops as $shop_id => $shop_name)
                                            <option value="{{ $shop_id }}">{{ $shop_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 col-sm-6">
                                    <label style="font-weight: 600; color: #475569; font-size: 12px;">Date Range</label>
                                    <div class="input-group">
                                        <span class="input-group-addon" style="background: #f1f5f9; border-color: #cbd5e1;">
                                            <i class="fa fa-calendar bigger-110 text-primary"></i>
                                        </span>
                                        <input type="text" class="form-control" name="date_range" id="date_range" 
                                               value="{{ $default_start_date }} - {{ $default_end_date }}" 
                                               autocomplete="off" style="font-weight: 500;">
                                    </div>
                                </div>

                                <div class="col-md-5 col-sm-12" style="margin-top: 18px;">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-white btn-default btn-filter-preset" data-preset="today">Today</button>
                                        <button type="button" class="btn btn-white btn-default btn-filter-preset" data-preset="yesterday">Yesterday</button>
                                        <button type="button" class="btn btn-white btn-default btn-filter-preset" data-preset="last7">Last 7 Days</button>
                                        <button type="button" class="btn btn-white btn-default btn-filter-preset active" data-preset="thisMonth">This Month</button>
                                        <button type="button" class="btn btn-white btn-default btn-filter-preset" data-preset="lastMonth">Last Month</button>
                                        <button type="button" class="btn btn-white btn-default btn-filter-preset" data-preset="all">All Time</button>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 12px; padding-top: 10px; border-top: 1px dashed #e2e8f0;">
                                <div class="col-xs-12 text-right">
                                    <button type="button" class="btn btn-sm btn-default btn-round" onclick="resetFilters();" style="margin-right: 5px;">
                                        <i class="ace-icon fa fa-refresh"></i> Reset
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary btn-round" onclick="loadDailyReportData();">
                                        <i class="ace-icon fa fa-search"></i> Apply Filter & Calculate
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Filter Info Banner -->
    <div class="row" style="margin-top: 15px;">
        <div class="col-xs-12">
            <div style="background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 15px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                <div>
                    <span style="font-size: 13px; color: #475569;">
                        <strong>Filtering:</strong> <span id="info_shops" class="text-primary font-bold">All Shops</span>
                        <span style="margin: 0 10px; color: #cbd5e1;">|</span>
                        <strong>Period:</strong> <span id="info_date" class="text-primary font-bold">--</span>
                        <span style="margin: 0 10px; color: #cbd5e1;">|</span>
                        <strong>Active Days:</strong> <span id="info_days_count" class="badge badge-info" style="font-weight: bold;">0</span>
                    </span>
                </div>
                <div style="font-size: 12px; color: #64748b;">
                    <i class="fa fa-info-circle text-info"></i> All figures sourced directly from sales orders, customer receipts, and inventory ledger.
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="row" style="margin-top: 15px;">
        <!-- Card 1: Total Sales -->
        <div class="col-xs-12 col-sm-6 col-md-3" style="margin-bottom: 12px;">
            <div class="summary-kpi-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-left: 4px solid #3b82f6; border-radius: 6px; padding: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div class="text-muted" style="font-size: 12px; font-weight: 600; text-transform: uppercase;">
                    <i class="fa fa-shopping-bag text-primary"></i> Total Gross Sales
                </div>
                <div style="font-size: 22px; font-weight: 700; color: #1e293b; margin-top: 4px;">
                    Rs <span id="kpi_total_sales">0.00</span>
                </div>
                <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                    <span id="kpi_order_count">0</span> Orders Recorded
                </div>
            </div>
        </div>

        <!-- Card 2: Cash Sales (POS) -->
        <div class="col-xs-12 col-sm-6 col-md-3" style="margin-bottom: 12px;">
            <div class="summary-kpi-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-left: 4px solid #10b981; border-radius: 6px; padding: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div class="text-muted" style="font-size: 12px; font-weight: 600; text-transform: uppercase;">
                    <i class="fa fa-money text-success"></i> Cash Sales (POS)
                </div>
                <div style="font-size: 22px; font-weight: 700; color: #047857; margin-top: 4px;">
                    Rs <span id="kpi_cash_sales">0.00</span>
                </div>
                <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                    Received Upfront at Sale
                </div>
            </div>
        </div>

        <!-- Card 3: Online Sales (Bank) -->
        <div class="col-xs-12 col-sm-6 col-md-3" style="margin-bottom: 12px;">
            <div class="summary-kpi-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-left: 4px solid #06b6d4; border-radius: 6px; padding: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div class="text-muted" style="font-size: 12px; font-weight: 600; text-transform: uppercase;">
                    <i class="fa fa-university text-info"></i> Online / Bank Sales
                </div>
                <div style="font-size: 22px; font-weight: 700; color: #0e7490; margin-top: 4px;">
                    Rs <span id="kpi_online_sales">0.00</span>
                </div>
                <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                    Direct Bank Transfers / Cards
                </div>
            </div>
        </div>

        <!-- Card 4: Credit Sales & Pending -->
        <div class="col-xs-12 col-sm-6 col-md-3" style="margin-bottom: 12px;">
            <div class="summary-kpi-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-left: 4px solid #f59e0b; border-radius: 6px; padding: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div class="text-muted" style="font-size: 12px; font-weight: 600; text-transform: uppercase;">
                    <i class="fa fa-clock-o text-warning"></i> Credit Pending Collection
                </div>
                <div style="font-size: 22px; font-weight: 700; color: #b45309; margin-top: 4px;">
                    Rs <span id="kpi_credit_pending">0.00</span>
                </div>
                <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                    Total Billed: Rs <span id="kpi_credit_sales">0.00</span>
                </div>
            </div>
        </div>

        <!-- Card 5: Customer Debt Recovered -->
        <div class="col-xs-12 col-sm-6 col-md-3" style="margin-bottom: 12px;">
            <div class="summary-kpi-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-left: 4px solid #8b5cf6; border-radius: 6px; padding: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div class="text-muted" style="font-size: 12px; font-weight: 600; text-transform: uppercase;">
                    <i class="fa fa-handshake-o text-purple"></i> Customer Receipts Collected
                </div>
                <div style="font-size: 22px; font-weight: 700; color: #6d28d9; margin-top: 4px;">
                    Rs <span id="kpi_total_collected">0.00</span>
                </div>
                <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                    Cash: Rs <span id="kpi_customer_cash">0.00</span> | Bank: Rs <span id="kpi_customer_online">0.00</span>
                </div>
            </div>
        </div>

        <!-- Card 6: Total Cash Inflow -->
        <div class="col-xs-12 col-sm-6 col-md-3" style="margin-bottom: 12px;">
            <div class="summary-kpi-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-left: 4px solid #14b8a6; border-radius: 6px; padding: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div class="text-muted" style="font-size: 12px; font-weight: 600; text-transform: uppercase;">
                    <i class="fa fa-calculator text-teal"></i> Total Cash Received
                </div>
                <div style="font-size: 22px; font-weight: 700; color: #0f766e; margin-top: 4px;">
                    Rs <span id="kpi_cash_inflow">0.00</span>
                </div>
                <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                    POS Cash + Debt Cash Inflow
                </div>
            </div>
        </div>

        <!-- Card 7: Net Sales -->
        <div class="col-xs-12 col-sm-6 col-md-3" style="margin-bottom: 12px;">
            <div class="summary-kpi-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-left: 4px solid #6366f1; border-radius: 6px; padding: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div class="text-muted" style="font-size: 12px; font-weight: 600; text-transform: uppercase;">
                    <i class="fa fa-line-chart text-indigo"></i> Net Sales Revenue
                </div>
                <div style="font-size: 22px; font-weight: 700; color: #4338ca; margin-top: 4px;">
                    Rs <span id="kpi_net_sales">0.00</span>
                </div>
                <div style="font-size: 12px; color: #dc2626; margin-top: 2px;">
                    Returns: Rs <span id="kpi_return_amount">0.00</span>
                </div>
            </div>
        </div>

        <!-- Card 8: Stock Movement -->
        <div class="col-xs-12 col-sm-6 col-md-3" style="margin-bottom: 12px;">
            <div class="summary-kpi-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-left: 4px solid #64748b; border-radius: 6px; padding: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div class="text-muted" style="font-size: 12px; font-weight: 600; text-transform: uppercase;">
                    <i class="fa fa-cubes text-secondary"></i> Net Stock Movement
                </div>
                <div style="font-size: 22px; font-weight: 700; color: #334155; margin-top: 4px;">
                    <span id="kpi_net_stock">0</span> units
                </div>
                <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                    Sold: <span id="kpi_items_sold">0</span> | Ret: <span id="kpi_items_returned">0</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Daily Summary Table -->
    <div class="row" style="margin-top: 15px;">
        <div class="col-xs-12">
            <div class="widget-box transparent">
                <div class="widget-header" style="border-bottom: 1px solid #cbd5e1; padding-bottom: 5px;">
                    <h4 class="widget-title" style="font-weight: 700; color: #1e293b;">
                        <i class="fa fa-table text-primary"></i> Daily Sales Breakdown & Reconciliation Table
                    </h4>
                </div>

                <div class="widget-body" style="padding-top: 10px;">
                    <div class="table-responsive" id="print-area">
                        <!-- Printable Header (Visible only in Print mode) -->
                        <div class="print-only" style="display: none; margin-bottom: 20px; text-align: center;">
                            <h2 style="margin: 0; font-weight: bold;">AWAN BUCKLE - POINT OF SALE</h2>
                            <h3 style="margin: 5px 0 0 0;">Daily Sales Summary & Reconciliation Report</h3>
                            <p style="margin: 5px 0;"><strong>Period:</strong> <span class="print-period-text"></span> | <strong>Shops:</strong> <span class="print-shop-text"></span></p>
                            <hr style="margin: 10px 0; border-top: 2px solid #000;">
                        </div>

                        <table class="table table-striped table-bordered table-hover" id="daily-summary-table" style="width: 100%; border: 1px solid #cbd5e1;">
                            <thead style="background: #2b3d51; color: #ffffff;">
                                <tr>
                                    <th rowspan="2" class="text-center" style="vertical-align: middle; width: 110px;">Date</th>
                                    <th rowspan="2" class="text-center" style="vertical-align: middle; width: 85px;">Day</th>
                                    <th rowspan="2" class="text-center" style="vertical-align: middle; width: 55px;">Orders</th>
                                    <th rowspan="2" class="text-right" style="vertical-align: middle; width: 110px; background: #1e293b;">
                                        Total Sales<br><small style="color: #94a3b8;">(Gross Rs)</small>
                                    </th>
                                    <th colspan="3" class="text-center" style="background: #334155; border-bottom: 1px solid #475569;">
                                        Sales Payment Methods (POS Checkout)
                                    </th>
                                    <th colspan="2" class="text-center" style="background: #475569; border-bottom: 1px solid #64748b;">
                                        Customer Debt Collections
                                    </th>
                                    <th rowspan="2" class="text-right" style="vertical-align: middle; width: 105px; background: #0f766e;">
                                        Total Cash Received<br><small style="color: #99f6e4;">(POS + Debt)</small>
                                    </th>
                                    <th rowspan="2" class="text-right" style="vertical-align: middle; width: 95px; background: #991b1b;">
                                        Returns<br><small style="color: #fca5a5;">(Amount Rs)</small>
                                    </th>
                                    <th rowspan="2" class="text-right" style="vertical-align: middle; width: 110px; background: #3730a3;">
                                        Net Sales<br><small style="color: #c7d2fe;">(Gross - Ret)</small>
                                    </th>
                                    <th colspan="3" class="text-center" style="background: #1e293b; border-bottom: 1px solid #334155;">
                                        Stock Movement
                                    </th>
                                    <th rowspan="2" class="text-center no-print" style="vertical-align: middle; width: 110px;">
                                        Verify / Reconcile
                                    </th>
                                </tr>
                                <tr>
                                    <!-- Sales Payment Methods Sub-headers -->
                                    <th class="text-right" style="width: 95px; background: #065f46; color: #a7f3d0;">Cash Sales</th>
                                    <th class="text-right" style="width: 95px; background: #155e75; color: #a5f3fc;">Online Sales</th>
                                    <th class="text-right" style="width: 130px; background: #92400e; color: #fde68a;">
                                        Credit Sales<br><small style="color: #fef3c7;">(Pending)</small>
                                    </th>
                                    <!-- Collections Sub-headers -->
                                    <th class="text-right" style="width: 90px; background: #581c87; color: #e9d5ff;">Cash Recv</th>
                                    <th class="text-right" style="width: 90px; background: #6b21a8; color: #f3e8ff;">Total Recv</th>
                                    <!-- Stock Sub-headers -->
                                    <th class="text-center" style="width: 50px; background: #334155;">Sold</th>
                                    <th class="text-center" style="width: 50px; background: #334155;">Ret</th>
                                    <th class="text-center" style="width: 55px; background: #1e293b; color: #38bdf8;">Net</th>
                                </tr>
                            </thead>
                            <tbody id="daily-summary-tbody">
                                <tr>
                                    <td colspan="16" class="text-center" style="padding: 40px; color: #64748b;">
                                        <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
                                        <div style="margin-top: 10px; font-weight: 500;">Calculating Daily Sales Summary & Reconciliation Figures...</div>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot id="daily-summary-tfoot" style="background: #1e293b; color: #ffffff; font-weight: bold; border-top: 2px solid #0f172a;">
                                <tr>
                                    <td colspan="2" class="text-center" style="font-size: 13px;">GRAND TOTAL</td>
                                    <td class="text-center" id="foot_orders">0</td>
                                    <td class="text-right" id="foot_total_sales" style="color: #60a5fa; font-size: 13px;">0.00</td>
                                    <td class="text-right" id="foot_cash_sales" style="color: #34d399; font-size: 13px;">0.00</td>
                                    <td class="text-right" id="foot_online_sales" style="color: #22d3ee; font-size: 13px;">0.00</td>
                                    <td class="text-right" id="foot_credit_sales" style="color: #fbbf24; font-size: 13px;">0.00</td>
                                    <td class="text-right" id="foot_cust_cash" style="color: #c084fc;">0.00</td>
                                    <td class="text-right" id="foot_cust_total" style="color: #e879f9;">0.00</td>
                                    <td class="text-right" id="foot_cash_inflow" style="color: #2dd4bf; font-size: 13px;">0.00</td>
                                    <td class="text-right" id="foot_returns" style="color: #f87171;">0.00</td>
                                    <td class="text-right" id="foot_net_sales" style="color: #818cf8; font-size: 13px;">0.00</td>
                                    <td class="text-center" id="foot_sold">0</td>
                                    <td class="text-center" id="foot_ret">0</td>
                                    <td class="text-center" id="foot_net_stock" style="color: #38bdf8; font-size: 13px;">0</td>
                                    <td class="no-print"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Details & Reconciliation Modal -->
<div class="modal fade" id="dayDetailsModal" tabindex="-1" role="dialog" aria-labelledby="dayDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 90%; max-width: 1200px;">
        <div class="modal-content" id="dayDetailsContainer" style="border-radius: 6px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
            <!-- Loaded via AJAX -->
            <div style="padding: 40px; text-align: center;">
                <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
                <div style="margin-top: 10px;">Loading transaction details...</div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling for Daily Sales Summary */
    .summary-kpi-card {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .summary-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    #daily-summary-table th, #daily-summary-table td {
        font-size: 12px;
        padding: 8px 6px;
    }
    #daily-summary-table tbody tr:hover {
        background-color: #f1f5f9 !important;
    }
    .text-purple {
        color: #8b5cf6 !important;
    }
    .text-teal {
        color: #14b8a6 !important;
    }
    .text-indigo {
        color: #6366f1 !important;
    }

    /* Print Styles */
    @media print {
        body {
            background: #fff !important;
            font-size: 10px !important;
        }
        .main-container:before, 
        #sidebar, 
        .navbar, 
        .breadcrumbs, 
        .footer, 
        .page-header, 
        #filter-widget, 
        .no-print,
        .modal,
        .btn {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
            padding: 0 !important;
        }
        .page-content {
            padding: 0 !important;
        }
        .print-only {
            display: block !important;
        }
        #daily-summary-table {
            border-collapse: collapse !important;
            width: 100% !important;
        }
        #daily-summary-table th, #daily-summary-table td {
            border: 1px solid #333 !important;
            padding: 4px !important;
            font-size: 9px !important;
            color: #000 !important;
            background: transparent !important;
        }
        #daily-summary-table thead tr th {
            background-color: #eee !important;
            color: #000 !important;
        }
    }
</style>
@endsection

@section('script')
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker.min.js') }}"></script>

<script>
    var currentReportData = [];

    $(document).ready(function() {
        // Initialize chosen select for shops
        if ($(".chosen-multi-select").length > 0) {
            $(".chosen-multi-select").chosen({
                no_results_text: "No shop matches found!",
                width: "100%"
            });
        }

        // Initialize Daterangepicker
        $('#date_range').daterangepicker({
            autoUpdateInput: true,
            locale: {
                format: 'DD/MM/YYYY',
                applyLabel: 'Apply',
                cancelLabel: 'Cancel'
            },
            opens: 'left',
            applyClass: 'btn-sm btn-success',
            cancelClass: 'btn-sm btn-default'
        });

        // Quick Preset Buttons
        $('.btn-filter-preset').on('click', function() {
            $('.btn-filter-preset').removeClass('active btn-primary').addClass('btn-white btn-default');
            $(this).removeClass('btn-white btn-default').addClass('active btn-primary');

            var preset = $(this).data('preset');
            var startDate, endDate;

            if (preset === 'today') {
                startDate = moment();
                endDate = moment();
            } else if (preset === 'yesterday') {
                startDate = moment().subtract(1, 'days');
                endDate = moment().subtract(1, 'days');
            } else if (preset === 'last7') {
                startDate = moment().subtract(6, 'days');
                endDate = moment();
            } else if (preset === 'thisMonth') {
                startDate = moment().startOf('month');
                endDate = moment();
            } else if (preset === 'lastMonth') {
                startDate = moment().subtract(1, 'month').startOf('month');
                endDate = moment().subtract(1, 'month').endOf('month');
            } else if (preset === 'all') {
                startDate = moment('2020-01-01');
                endDate = moment();
            }

            $('#date_range').data('daterangepicker').setStartDate(startDate);
            $('#date_range').data('daterangepicker').setEndDate(endDate);
            $('#date_range').val(startDate.format('DD/MM/YYYY') + ' - ' + endDate.format('DD/MM/YYYY'));

            loadDailyReportData();
        });

        // Initial Data Load
        loadDailyReportData();
    });

    /**
     * Load Daily Sales Summary via AJAX
     */
    function loadDailyReportData() {
        var shopIds = $('#shop_ids').val() || [];
        var dateRange = $('#date_range').val() || '';

        $('#daily-summary-tbody').html(
            '<tr><td colspan="16" class="text-center" style="padding: 40px; color: #64748b;">' +
            '<i class="fa fa-spinner fa-spin fa-2x text-primary"></i>' +
            '<div style="margin-top: 10px; font-weight: 500;">Loading and calculating figures...</div>' +
            '</td></tr>'
        );

        $.ajax({
            url: "{{ route('report.sale.daily_summary.data') }}",
            type: 'GET',
            data: {
                shop_ids: shopIds,
                date_range: dateRange
            },
            dataType: 'json',
            success: function(res) {
                if (res.status) {
                    currentReportData = res.data;
                    renderTableAndKPIs(res);
                } else {
                    $('#daily-summary-tbody').html(
                        '<tr><td colspan="16" class="text-center text-danger" style="padding: 30px;">' +
                        '<i class="fa fa-exclamation-triangle"></i> ' + (res.message || 'Error loading report data.') +
                        '</td></tr>'
                    );
                }
            },
            error: function(xhr) {
                $('#daily-summary-tbody').html(
                    '<tr><td colspan="16" class="text-center text-danger" style="padding: 30px;">' +
                    '<i class="fa fa-exclamation-triangle"></i> An error occurred while retrieving data. Please check connection and try again.' +
                    '</td></tr>'
                );
            }
        });
    }

    /**
     * Render the table and summary KPI cards
     */
    function renderTableAndKPIs(res) {
        var data = res.data || [];
        var totals = res.grand_totals || {};
        var filterInfo = res.filter_info || {};

        // Update Info Banner
        $('#info_shops').text(filterInfo.shops_name || 'All Shops');
        $('#info_date').text(filterInfo.date_label || 'All Time');
        $('#info_days_count').text((filterInfo.days_count || 0) + ' Days');

        // Printable banner texts
        $('.print-shop-text').text(filterInfo.shops_name || 'All Shops');
        $('.print-period-text').text(filterInfo.date_label || 'All Time');

        // Update KPIs
        $('#kpi_total_sales').text(Number(totals.total_sales || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#kpi_order_count').text(totals.order_count || 0);

        $('#kpi_cash_sales').text(Number(totals.cash_sales || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#kpi_online_sales').text(Number(totals.online_sales || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));

        $('#kpi_credit_pending').text(Number(totals.credit_pending || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#kpi_credit_sales').text(Number(totals.credit_sales || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));

        $('#kpi_total_collected').text(Number(totals.total_collected || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#kpi_customer_cash').text(Number(totals.customer_cash_collected || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#kpi_customer_online').text(Number(totals.customer_online_collected || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));

        $('#kpi_cash_inflow').text(Number(totals.total_cash_inflow || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#kpi_net_sales').text(Number(totals.net_sales || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#kpi_return_amount').text(Number(totals.return_amount || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));

        $('#kpi_net_stock').text(totals.net_stock_movement || 0);
        $('#kpi_items_sold').text(totals.items_sold || 0);
        $('#kpi_items_returned').text(totals.items_returned || 0);

        // Update Table Rows
        var tbodyHtml = '';
        if (data.length === 0) {
            tbodyHtml = '<tr><td colspan="16" class="text-center" style="padding: 30px; color: #64748b;">' +
                        '<i class="fa fa-info-circle"></i> No sales, payment collections, or stock movements found for the selected period.' +
                        '</td></tr>';
        } else {
            $.each(data, function(i, row) {
                var pendingBadge = '';
                if (row.credit_sales > 0) {
                    if (row.credit_pending <= 0) {
                        pendingBadge = '<br><span class="badge badge-success" style="font-size: 10px;">Fully Settled</span>';
                    } else if (row.credit_pending < row.credit_sales) {
                        pendingBadge = '<br><span class="badge badge-warning" style="font-size: 10px;">Rs ' + Number(row.credit_pending).toLocaleString() + ' Due</span>';
                    } else {
                        pendingBadge = '<br><span class="badge badge-danger" style="font-size: 10px;">Rs ' + Number(row.credit_pending).toLocaleString() + ' Due</span>';
                    }
                }

                var dayClass = (row.day_name === 'Sunday') ? 'color: #dc2626; font-weight: bold;' : 'color: #475569;';

                tbodyHtml += '<tr>' +
                    '<td class="text-center"><strong>' + row.formatted_date + '</strong></td>' +
                    '<td class="text-center" style="' + dayClass + '">' + row.day_name + '</td>' +
                    '<td class="text-center"><span class="badge badge-grey">' + row.order_count + '</span></td>' +
                    '<td class="text-right" style="font-weight: 700; color: #1e293b;">' + Number(row.total_sales).toLocaleString(undefined, {minimumFractionDigits: 2}) + '</td>' +
                    '<td class="text-right text-success" style="font-weight: 600;">' + Number(row.cash_sales).toLocaleString(undefined, {minimumFractionDigits: 2}) + '</td>' +
                    '<td class="text-right text-info" style="font-weight: 600;">' + Number(row.online_sales).toLocaleString(undefined, {minimumFractionDigits: 2}) + '</td>' +
                    '<td class="text-right" style="font-weight: 600; color: #d97706;">' + 
                        Number(row.credit_sales).toLocaleString(undefined, {minimumFractionDigits: 2}) + 
                        pendingBadge + 
                    '</td>' +
                    '<td class="text-right text-purple">' + Number(row.customer_cash_collected).toLocaleString(undefined, {minimumFractionDigits: 2}) + '</td>' +
                    '<td class="text-right text-purple" style="font-weight: 600;">' + Number(row.total_collected).toLocaleString(undefined, {minimumFractionDigits: 2}) + '</td>' +
                    '<td class="text-right" style="font-weight: 700; color: #0d9488;">' + Number(row.total_cash_inflow).toLocaleString(undefined, {minimumFractionDigits: 2}) + '</td>' +
                    '<td class="text-right ' + (row.return_amount > 0 ? 'text-danger font-bold' : 'text-muted') + '">' + Number(row.return_amount).toLocaleString(undefined, {minimumFractionDigits: 2}) + '</td>' +
                    '<td class="text-right" style="font-weight: 700; color: #4338ca;">' + Number(row.net_sales).toLocaleString(undefined, {minimumFractionDigits: 2}) + '</td>' +
                    '<td class="text-center">' + row.items_sold + '</td>' +
                    '<td class="text-center text-danger">' + row.items_returned + '</td>' +
                    '<td class="text-center" style="font-weight: bold; color: #0284c7;">' + row.net_stock_movement + '</td>' +
                    '<td class="text-center no-print">' +
                        '<button type="button" class="btn btn-xs btn-primary btn-round" onclick="viewDayDetails(\'' + row.date + '\');" title="Inspect Day Transactions & Reconciliation">' +
                            '<i class="fa fa-search"></i> Reconcile' +
                        '</button>' +
                    '</td>' +
                '</tr>';
            });
        }
        $('#daily-summary-tbody').html(tbodyHtml);

        // Update Footers
        $('#foot_orders').text(totals.order_count || 0);
        $('#foot_total_sales').text(Number(totals.total_sales || 0).toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#foot_cash_sales').text(Number(totals.cash_sales || 0).toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#foot_online_sales').text(Number(totals.online_sales || 0).toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#foot_credit_sales').html(
            Number(totals.credit_sales || 0).toLocaleString(undefined, {minimumFractionDigits: 2}) +
            '<br><small style="color: #fef3c7;">(' + Number(totals.credit_pending || 0).toLocaleString(undefined, {minimumFractionDigits: 2}) + ' Due)</small>'
        );
        $('#foot_cust_cash').text(Number(totals.customer_cash_collected || 0).toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#foot_cust_total').text(Number(totals.total_collected || 0).toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#foot_cash_inflow').text(Number(totals.total_cash_inflow || 0).toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#foot_returns').text(Number(totals.return_amount || 0).toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#foot_net_sales').text(Number(totals.net_sales || 0).toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#foot_sold').text(totals.items_sold || 0);
        $('#foot_ret').text(totals.items_returned || 0);
        $('#foot_net_stock').text(totals.net_stock_movement || 0);
    }

    /**
     * Open Transaction Details Modal for specific day
     */
    function viewDayDetails(dateStr) {
        var shopIds = $('#shop_ids').val() || [];
        $('#dayDetailsContainer').html(
            '<div style="padding: 60px; text-align: center;">' +
            '<i class="fa fa-spinner fa-spin fa-3x text-primary"></i>' +
            '<div style="margin-top: 15px; font-weight: 600; font-size: 15px; color: #475569;">Fetching and compiling verification details for ' + dateStr + '...</div>' +
            '</div>'
        );
        $('#dayDetailsModal').modal('show');

        $.ajax({
            url: "{{ route('report.sale.daily_summary.details') }}",
            type: 'GET',
            data: {
                date: dateStr,
                shop_ids: shopIds,
                format: 'html'
            },
            success: function(htmlResponse) {
                $('#dayDetailsContainer').html(htmlResponse);
            },
            error: function(err) {
                $('#dayDetailsContainer').html(
                    '<div class="modal-body text-center text-danger" style="padding: 40px;">' +
                    '<i class="fa fa-exclamation-triangle fa-2x"></i>' +
                    '<div style="margin-top: 10px;">Failed to load transaction details. Please try again.</div>' +
                    '<button type="button" class="btn btn-sm btn-default" data-dismiss="modal" style="margin-top: 15px;">Close</button>' +
                    '</div>'
                );
            }
        });
    }

    /**
     * Reset Filters
     */
    function resetFilters() {
        if ($('#shop_ids').length > 0) {
            $('#shop_ids').val('').trigger('chosen:updated');
        }
        var startDate = moment().startOf('month');
        var endDate = moment();
        $('#date_range').data('daterangepicker').setStartDate(startDate);
        $('#date_range').data('daterangepicker').setEndDate(endDate);
        $('#date_range').val(startDate.format('DD/MM/YYYY') + ' - ' + endDate.format('DD/MM/YYYY'));

        $('.btn-filter-preset').removeClass('active btn-primary').addClass('btn-white btn-default');
        $('.btn-filter-preset[data-preset="thisMonth"]').removeClass('btn-white btn-default').addClass('active btn-primary');

        loadDailyReportData();
    }

    /**
     * Export to CSV
     */
    function exportReportCsv() {
        var shopIds = $('#shop_ids').val() || [];
        var dateRange = $('#date_range').val() || '';
        var url = "{{ route('report.sale.daily_summary.export') }}?date_range=" + encodeURIComponent(dateRange);
        if (shopIds && shopIds.length > 0) {
            url += "&shop_ids=" + encodeURIComponent(shopIds.join(','));
        }
        window.location.href = url;
    }

    /**
     * Print Report
     */
    window.printReport = function() {
        window.print();
    };
</script>
@endsection
