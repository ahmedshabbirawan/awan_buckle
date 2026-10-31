<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Customer Sales Report - {{ $customer->name }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm 6mm 10mm 6mm;
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }

        .header-section {
            text-align: center;
            margin-bottom: 8px;
            border-bottom: 2px solid #000;
            padding-bottom: 4px;
        }
        .company-title {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin: 0 0 2px 0;
        }
        .report-subtitle {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            color: #333;
            margin: 0 0 4px 0;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            margin-bottom: 6px;
        }
        .meta-table td {
            padding: 2px 4px;
            vertical-align: top;
        }

        /* Report Table */
        table.report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            table-layout: fixed;
        }
        table.report-table thead {
            display: table-header-group;
        }
        table.report-table tfoot {
            display: table-footer-group;
        }
        table.report-table tr {
            page-break-inside: avoid;
        }
        table.report-table th, 
        table.report-table td {
            border: 1px solid #333;
            padding: 2px 3px;
            word-wrap: break-word;
            overflow: hidden;
        }
        table.report-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            font-size: 8.5px;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }

        .row-payment {
            background-color: #f7fafc;
        }
        .row-opening {
            background-color: #fff9e6;
            font-weight: bold;
        }
        .row-total {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .print-btn-bar {
            background: #2c3e50;
            color: #fff;
            padding: 8px 15px;
            text-align: right;
            margin-bottom: 10px;
        }
        .print-btn-bar button {
            background: #27ae60;
            color: #fff;
            border: none;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 3px;
        }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

@if(!empty($isHtml))
<div class="print-btn-bar no-print">
    <span style="float: left; font-size: 13px; font-weight: bold; line-height: 28px;">
        AWAN AGRO - Customer Sales Report (Print / PDF Preview)
    </span>
    <button onclick="window.print();"><i class="fa fa-print"></i> Print Report / Save PDF</button>
</div>
@endif

<div class="header-section">
    <div class="company-title">AWAN AGRO</div>
    <div class="report-subtitle">Customer-wise Product & Transaction Sales Report</div>
</div>

<table class="meta-table">
    <tr>
        <td style="width: 35%;">
            <strong>Customer:</strong> {{ $customer->name }}<br>
            <strong>Mobile:</strong> {{ $customer->mobile ?: 'N/A' }} | <strong>CNIC:</strong> {{ $customer->cnic ?: 'N/A' }}
        </td>
        <td style="width: 40%; text-align: center;">
            <strong>Period:</strong> 
            {{ $report['start_date'] ? date('d-M-Y', strtotime($report['start_date'])) : 'Inception' }}
            &nbsp;to&nbsp;
            {{ $report['end_date'] ? date('d-M-Y', strtotime($report['end_date'])) : 'Present' }}<br>
            <strong>Account Code:</strong> {{ $report['account']->code }}
        </td>
        <td style="width: 25%; text-align: right;">
            <strong>Current Balance:</strong> 
            {{ $report['closing_balance_dr_cr'] }} {{ number_format($report['closing_balance_amount'], 2) }}<br>
            <span style="font-size: 8px; color: #555;">Generated: {{ $report['generated_at'] }}</span>
        </td>
    </tr>
</table>

<table class="report-table">
    <thead>
        <tr>
            <th rowspan="2" style="width: 58px;">Date</th>
            <th rowspan="2" style="width: 140px;">Particulars</th>
            <th rowspan="2" style="width: 40px;">GP #</th>
            <th rowspan="2" style="width: 52px;">Veh#</th>
            <th rowspan="2" style="width: 42px;">Bilty #</th>
            <th rowspan="2" style="width: 38px;">Bags</th>
            <th rowspan="2" style="width: 45px;">Rate</th>
            <th rowspan="2" style="width: 60px;">Total Sale</th>
            <th colspan="2" style="width: 90px;">Commission Amount</th>
            <th colspan="2" style="width: 80px;">Discount</th>
            <th rowspan="2" style="width: 65px;">Debit</th>
            <th rowspan="2" style="width: 65px;">Credit</th>
            <th colspan="2" style="width: 95px;">Balance</th>
        </tr>
        <tr>
            <!-- Commission Subheaders -->
            <th style="width: 38px;">%</th>
            <th style="width: 52px;">Rs.</th>
            <!-- Discount Subheaders -->
            <th style="width: 35px;">Rate</th>
            <th style="width: 45px;">Amount</th>
            <!-- Balance Subheaders -->
            <th style="width: 25px;">dr/cr</th>
            <th style="width: 70px;">Amount</th>
        </tr>
    </thead>
    <tbody>
        <!-- Opening Balance Row -->
        <tr class="row-opening">
            <td class="text-center">{{ $report['start_date'] ? date('d/m/Y', strtotime($report['start_date'])) : '-' }}</td>
            <td colspan="7">
                <strong>OPENING BALANCE</strong>
                <span style="font-size: 7.5px; font-weight: normal; color: #555;">
                    (as of {{ $report['start_date'] ? date('d/m/Y', strtotime($report['start_date'])) : 'Inception' }})
                </span>
            </td>
            <td class="text-center">-</td>
            <td class="text-right">-</td>
            <td class="text-center">-</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            <td class="text-center bold">{{ strtolower($report['opening_balance_dr_cr']) }}</td>
            <td class="text-right bold">{{ number_format($report['opening_balance_amount'], 2) }}</td>
        </tr>

        <!-- Transaction Rows -->
        @foreach($report['rows'] as $r)
            <tr class="{{ $r['row_type'] === 'order_payment' || $r['row_type'] === 'voucher_payment' ? 'row-payment' : '' }}">
                <!-- Date -->
                <td class="text-center">{{ $r['date'] }}</td>

                <!-- Particulars -->
                <td class="text-left">
                    {{ $r['particulars'] }}
                </td>

                <!-- GP # (Order ID) -->
                <td class="text-center">{{ $r['order_id'] }}</td>

                <!-- Veh# -->
                <td class="text-center">{{ $r['vehicle_no'] }}</td>

                <!-- Bilty # -->
                <td class="text-center">{{ $r['bilty_no'] }}</td>

                <!-- Bags (Quantity) -->
                <td class="text-right">
                    {{ $r['qty'] !== null ? number_format($r['qty']) : '' }}
                </td>

                <!-- Rate -->
                <td class="text-right">
                    {{ $r['rate'] !== null && $r['rate'] > 0 ? number_format($r['rate'], 2) : '' }}
                </td>

                <!-- Total Sale -->
                <td class="text-right">
                    {{ $r['total_sale'] > 0 ? number_format($r['total_sale'], 2) : '' }}
                </td>

                <!-- Commission % -->
                <td class="text-center">
                    {{ $r['commission_percentage'] > 0 ? number_format($r['commission_percentage'], 2) : '' }}
                </td>

                <!-- Commission Rs. -->
                <td class="text-right">
                    {{ $r['commission_amount'] > 0 ? number_format($r['commission_amount'], 2) : '' }}
                </td>

                <!-- Discount Rate -->
                <td class="text-center">
                    {{ $r['discount_rate'] > 0 ? number_format($r['discount_rate'], 2) : '' }}
                </td>

                <!-- Discount Amount -->
                <td class="text-right">
                    {{ $r['discount_amount'] > 0 ? number_format($r['discount_amount'], 2) : '' }}
                </td>

                <!-- Debit -->
                <td class="text-right bold">
                    {{ $r['debit'] > 0 ? number_format($r['debit'], 2) : '' }}
                </td>

                <!-- Credit -->
                <td class="text-right bold">
                    {{ $r['credit'] > 0 ? number_format($r['credit'], 2) : '' }}
                </td>

                <!-- Balance dr/cr -->
                <td class="text-center bold">
                    {{ strtolower($r['balance_dr_cr']) }}
                </td>

                <!-- Balance Amount -->
                <td class="text-right bold">
                    {{ number_format($r['balance_amount'], 2) }}
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <!-- Closing Balance & Totals Row -->
        <tr class="row-total">
            <td colspan="5" class="text-left bold">Closing Balance / Totals</td>
            <!-- Total Bags -->
            <td class="text-right bold">{{ number_format($report['total_quantity_sold']) }}</td>
            <td></td>
            <!-- Total Sale -->
            <td class="text-right bold">{{ number_format($report['total_sale_amount'], 2) }}</td>
            <td></td>
            <!-- Total Commission -->
            <td class="text-right bold">{{ number_format($report['total_commission_amount'], 2) }}</td>
            <td></td>
            <!-- Total Discount -->
            <td class="text-right bold">{{ number_format($report['total_discount_amount'], 2) }}</td>
            <!-- Total Debit -->
            <td class="text-right bold">{{ number_format($report['total_debit'], 2) }}</td>
            <!-- Total Credit -->
            <td class="text-right bold">{{ number_format($report['total_credit'], 2) }}</td>
            <!-- Final dr/cr -->
            <td class="text-center bold">{{ strtolower($report['closing_balance_dr_cr']) }}</td>
            <!-- Final Closing Balance Amount -->
            <td class="text-right bold">{{ number_format($report['closing_balance_amount'], 2) }}</td>
        </tr>
    </tfoot>
</table>

@if(!empty($isHtml))
<script type="text/javascript">
    window.onload = function() {
        // window.print();
    };
</script>
@endif

</body>
</html>
