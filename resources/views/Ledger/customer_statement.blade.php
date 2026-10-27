@extends('Layout.master')

@section('title')
Customer Statement - {{ $customer->name }}
@endsection

@section('content')
<style>
@media print {
    .no-print { display: none !important; }
    .breadcrumbs { display: none !important; }
    .page-header { margin-bottom: 5px; }
    body { background-color: #fff; font-size: 11px; }
    .widget-box { border: none !important; margin-bottom: 5px !important; }
    .widget-header { display: none !important; }
    .widget-body { border: none !important; }
    .table-bordered, .table-bordered > tbody > tr > td, .table-bordered > thead > tr > th {
        border: 1px solid #ddd !important;
        padding: 4px 6px !important;
    }
}
.summary-row {
    display: flex;
    flex-wrap: wrap;
    margin-left: -5px;
    margin-right: -5px;
    margin-bottom: 10px;
}
.summary-col {
    flex: 1;
    min-width: 175px;
    padding-left: 5px;
    padding-right: 5px;
    margin-bottom: 10px;
}
.summary-card {
    border-radius: 6px;
    padding: 12px 14px;
    color: #fff;
    height: 100%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
}
.summary-title {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.95;
    font-weight: 600;
}
.summary-amount {
    font-size: 19px;
    font-weight: bold;
    margin-top: 4px;
    margin-bottom: 2px;
    white-space: nowrap;
}
.summary-sub {
    font-size: 10px;
    opacity: 0.85;
}
</style>

<div class="page-content">
    <div class="page-header no-print">
        <h1>
            <a href="{{ route('customer.ledger.index') }}" class="btn btn-xs btn-default" style="margin-right: 8px;">
                <i class="fa fa-arrow-left"></i> Back to Ledgers
            </a>
            Customer Statement: <span class="blue">{{ $customer->name }}</span>
            <small>
                <i class="ace-icon fa fa-angle-double-right"></i>
                Complete Purchase History, Cash & Credit Transactions & Account Reconciliation
            </small>
        </h1>
    </div>

    <!-- Customer Header Profile Bar -->
    <div class="widget-box widget-color-blue" style="margin-bottom: 15px;">
        <div class="widget-header">
            <h5 class="widget-title bigger lighter">
                <i class="ace-icon fa fa-user"></i> Customer Profile & Account Status
            </h5>
            <div class="widget-toolbar no-print">
                <button type="button" class="btn btn-xs btn-success" onclick="openPaymentModal();">
                    <i class="fa fa-money"></i> Record Payment / Collect Due
                </button>
                <a href="{{ request()->fullUrlWithQuery(['view' => 'print']) }}" target="_blank" class="btn btn-xs btn-primary">
                    <i class="fa fa-print"></i> Print Statement
                </a>
                <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-xs btn-warning">
                    <i class="fa fa-file-excel-o"></i> Export CSV
                </a>
            </div>
        </div>
        <div class="widget-body">
            <div class="widget-main" style="padding: 15px;">
                <div class="row">
                    <div class="col-sm-3">
                        <strong>Customer Name:</strong> {{ $customer->name }}<br>
                        <strong>Customer ID:</strong> #{{ $customer->id }}
                    </div>
                    <div class="col-sm-3">
                        <strong>Mobile:</strong> {{ $customer->mobile ?? 'N/A' }}<br>
                        <strong>CNIC:</strong> {{ $customer->cnic ?? 'N/A' }}
                    </div>
                    <div class="col-sm-3">
                        <strong>Address:</strong> {{ $customer->address ?? 'N/A' }}<br>
                        <strong>Account Code:</strong> {{ $account->code }}
                    </div>
                    <div class="col-sm-3 text-right">
                        <span style="font-size: 13px; color: #555;">Current Net Outstanding Balance:</span><br>
                        <span style="font-size: 22px; font-weight: bold; color: {{ $currentBalance > 0 ? '#c53030' : '#2e8965' }};">
                            PKR {{ number_format($currentBalance, 2) }}
                        </span>
                        <div style="font-size: 11px; color: #888;">
                            {{ $currentBalance > 0 ? '(Receivable / Customer Debt)' : ($currentBalance < 0 ? '(Customer Advance Credit)' : '(Fully Settled - Zero Due)') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 6 Summary Reconciliation Cards -->
    <div class="summary-row">
        <!-- Card 1: Total Sales Amount -->
        <div class="summary-col">
            <div class="summary-card" style="background: linear-gradient(135deg, #6b46c1, #805ad5);">
                <div class="summary-title"><i class="fa fa-shopping-cart"></i> Total Sales Amount</div>
                <div class="summary-amount">PKR {{ number_format($lifetimeSales, 2) }}</div>
                <div class="summary-sub">All Cash, Credit & Online Invoiced</div>
            </div>
        </div>

        <!-- Card 2: Total Cash Received -->
        <div class="summary-col">
            <div class="summary-card" style="background: linear-gradient(135deg, #0e7490, #06b6d4);">
                <div class="summary-title"><i class="fa fa-money"></i> Total Cash Received</div>
                <div class="summary-amount">PKR {{ number_format($lifetimeCashReceived, 2) }}</div>
                <div class="summary-sub">Sales: {{ number_format($statement['lifetime_cash_received_at_sale'], 2) }} | Vouchers: {{ number_format($statement['lifetime_voucher_cash'], 2) }}</div>
            </div>
        </div>

        <!-- Card 3: Total Online Payments Received -->
        <div class="summary-col">
            <div class="summary-card" style="background: linear-gradient(135deg, #1d4ed8, #3b82f6);">
                <div class="summary-title"><i class="fa fa-university"></i> Total Online Received</div>
                <div class="summary-amount">PKR {{ number_format($lifetimeOnlineReceived, 2) }}</div>
                <div class="summary-sub">Bank Transfer & Online Receipts</div>
            </div>
        </div>

        <!-- Card 4: Total Credit Sales -->
        <div class="summary-col">
            <div class="summary-card" style="background: linear-gradient(135deg, #b45309, #d97706);">
                <div class="summary-title"><i class="fa fa-clock-o"></i> Total Credit Sales</div>
                <div class="summary-amount">PKR {{ number_format($lifetimeCreditSales, 2) }}</div>
                <div class="summary-sub">Initial Receivables Billed</div>
            </div>
        </div>

        <!-- Card 5: Total Amount Collected -->
        <div class="summary-col">
            <div class="summary-card" style="background: linear-gradient(135deg, #15803d, #22c55e);">
                <div class="summary-title"><i class="fa fa-check-circle"></i> Total Amount Collected</div>
                <div class="summary-amount">PKR {{ number_format($lifetimeTotalCollected, 2) }}</div>
                <div class="summary-sub">Cash + Online Payments Combined</div>
            </div>
        </div>

        <!-- Card 6: Remaining Outstanding Balance -->
        <div class="summary-col">
            <div class="summary-card" style="background: linear-gradient(135deg, {{ $currentBalance > 0 ? '#b91c1c, #ef4444' : '#15803d, #22c55e' }});">
                <div class="summary-title"><i class="fa fa-exclamation-triangle"></i> Remaining Balance</div>
                <div class="summary-amount">PKR {{ number_format($currentBalance, 2) }}</div>
                <div class="summary-sub">{{ $currentBalance > 0 ? 'Current Net Receivable' : 'Fully Settled / No Due' }}</div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="widget-box widget-color-grey no-print" style="margin-bottom: 15px;">
        <div class="widget-body">
            <div class="widget-main" style="padding: 10px 15px;">
                <form method="GET" action="{{ route('customer.ledger.show', $customer->id) }}" class="form-inline">
                    <div class="form-group" style="margin-right: 15px;">
                        <label for="start_date"><strong>From Date:</strong></label>
                        <input type="date" class="form-control input-sm" id="start_date" name="start_date" value="{{ $startDate }}">
                    </div>
                    <div class="form-group" style="margin-right: 15px;">
                        <label for="end_date"><strong>To Date:</strong></label>
                        <input type="date" class="form-control input-sm" id="end_date" name="end_date" value="{{ $endDate }}">
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fa fa-filter"></i> Filter Statement
                    </button>
                    @if($startDate || $endDate)
                        <a href="{{ route('customer.ledger.show', $customer->id) }}" class="btn btn-sm btn-default" style="margin-left: 5px;">
                            <i class="fa fa-refresh"></i> Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    @if($startDate || $endDate)
        <div class="alert alert-info no-print" style="margin-bottom: 15px; padding: 10px 15px;">
            <i class="fa fa-info-circle"></i> <strong>Filtered Period:</strong> 
            {{ $startDate ? date('d-m-Y', strtotime($startDate)) : 'Start' }} to {{ $endDate ? date('d-m-Y', strtotime($endDate)) : 'Present' }}
            <span class="pull-right">
                <strong>Period Sales:</strong> PKR {{ number_format($statement['period_total_sales'], 2) }} | 
                <strong>Period Cash Received:</strong> PKR {{ number_format($statement['period_cash_received'], 2) }} | 
                <strong>Period Online Received:</strong> PKR {{ number_format($statement['period_online_received'], 2) }} | 
                <strong>Period Collected:</strong> PKR {{ number_format($statement['period_total_collected'], 2) }} | 
                <strong>Closing Balance:</strong> PKR {{ number_format($statement['closing_balance'], 2) }}
            </span>
        </div>
    @endif

    <!-- Statement Table -->
    <div class="widget-box widget-color-blue2">
        <div class="widget-header">
            <h5 class="widget-title bigger lighter">
                <i class="ace-icon fa fa-list-alt"></i> Complete Customer Account Statement (All Sales & Payments)
                @if($startDate && $endDate)
                    <small style="color: #fff;">({{ date('d-m-Y', strtotime($startDate)) }} to {{ date('d-m-Y', strtotime($endDate)) }})</small>
                @endif
            </h5>
        </div>

        <div class="widget-body">
            <div class="widget-main no-padding">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" style="margin-bottom: 0;">
                        <thead style="background-color: #f7f9fb;">
                            <tr>
                                <th style="width: 10%;">Date</th>
                                <th style="width: 13%;">Invoice / Ref No</th>
                                <th style="width: 13%;">Transaction Type</th>
                                <th style="width: 11%;">Method</th>
                                <th>Description / Notes</th>
                                <th style="width: 12%; text-align: right;">Total Sale Amount</th>
                                <th style="width: 11%; text-align: right;">Amount Paid</th>
                                <th style="width: 11%; text-align: right;">Credit / Due</th>
                                <th style="width: 14%; text-align: right;">Outstanding Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Opening Balance Row -->
                            <tr style="background-color: #fff9e6; font-weight: bold;">
                                <td>{{ $startDate ? date('d-m-Y', strtotime($startDate)) : '-' }}</td>
                                <td><span class="label label-warning arrowed">OPENING</span></td>
                                <td><em>Opening Balance</em></td>
                                <td>-</td>
                                <td><em>Opening Balance as of {{ $startDate ? date('d-m-Y', strtotime($startDate)) : 'Account Inception' }}</em></td>
                                <td style="text-align: right;">-</td>
                                <td style="text-align: right;">-</td>
                                <td style="text-align: right;">-</td>
                                <td style="text-align: right; color: {{ $statement['opening_balance'] > 0 ? '#c53030' : '#2e8965' }};">
                                    PKR {{ number_format($statement['opening_balance'], 2) }}
                                </td>
                            </tr>

                            @forelse($statement['rows'] as $row)
                                <tr>
                                    <td>{{ $row['date'] }}</td>
                                    <td>
                                        <strong>{{ $row['invoice_no'] }}</strong>
                                        @if($row['reference_type'] === 'Sale' && $row['reference_id'])
                                            <a href="{{ route('sale.order.detail', $row['reference_id']) }}" target="_blank" class="no-print" title="View Sale Invoice" style="margin-left: 4px;">
                                                <i class="fa fa-external-link"></i>
                                            </a>
                                        @elseif(in_array($row['reference_type'], ['Payment', 'Receipt']) && $row['reference_id'])
                                            <a href="{{ route('customer.ledger.voucher', $row['reference_id']) }}" target="_blank" class="no-print" title="Print Receipt Voucher" style="margin-left: 4px;">
                                                <i class="fa fa-print"></i>
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($row['type_badge'] === 'success')
                                            <span class="label label-success arrowed"><i class="fa fa-check"></i> {{ $row['type_label'] }}</span>
                                        @elseif($row['type_badge'] === 'info')
                                            <span class="label label-info arrowed"><i class="fa fa-university"></i> {{ $row['type_label'] }}</span>
                                        @elseif($row['type_badge'] === 'danger')
                                            <span class="label label-danger arrowed"><i class="fa fa-clock-o"></i> {{ $row['type_label'] }}</span>
                                        @elseif($row['type_badge'] === 'warning')
                                            <span class="label label-warning arrowed"><i class="fa fa-pie-chart"></i> {{ $row['type_label'] }}</span>
                                        @elseif($row['type_badge'] === 'primary')
                                            <span class="label label-primary arrowed"><i class="fa fa-credit-card"></i> {{ $row['type_label'] }}</span>
                                        @else
                                            <span class="label label-inverse arrowed">{{ $row['type_label'] }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(str_contains(strtolower($row['payment_method']), 'online') || str_contains(strtolower($row['payment_method']), 'bank'))
                                            <span class="badge badge-info"><i class="fa fa-university"></i> Online</span>
                                        @elseif(str_contains(strtolower($row['payment_method']), 'credit'))
                                            <span class="badge badge-danger"><i class="fa fa-clock-o"></i> Credit</span>
                                        @elseif(str_contains(strtolower($row['payment_method']), 'split'))
                                            <span class="badge badge-warning"><i class="fa fa-pie-chart"></i> Split</span>
                                        @elseif(str_contains(strtolower($row['payment_method']), 'cash'))
                                            <span class="badge badge-success"><i class="fa fa-money"></i> Cash</span>
                                        @else
                                            <span class="badge badge-grey">{{ $row['payment_method'] }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $row['description'] }}</td>
                                    <td style="text-align: right; font-weight: {{ $row['total_amount'] > 0 ? 'bold' : 'normal' }};">
                                        {{ $row['total_amount'] > 0 ? 'PKR ' . number_format($row['total_amount'], 2) : '-' }}
                                    </td>
                                    <td style="text-align: right; font-weight: {{ $row['paid_amount'] > 0 ? 'bold' : 'normal' }}; color: {{ $row['paid_amount'] > 0 ? '#2e8965' : 'inherit' }};">
                                        {{ $row['paid_amount'] > 0 ? 'PKR ' . number_format($row['paid_amount'], 2) : '-' }}
                                    </td>
                                    <td style="text-align: right; font-weight: {{ $row['due_amount'] > 0 ? 'bold' : 'normal' }}; color: {{ $row['due_amount'] > 0 ? '#c53030' : 'inherit' }};">
                                        {{ $row['due_amount'] > 0 ? 'PKR ' . number_format($row['due_amount'], 2) : '-' }}
                                    </td>
                                    <td style="text-align: right; font-weight: bold; color: {{ $row['running_balance'] > 0 ? '#c53030' : '#2e8965' }};">
                                        PKR {{ number_format($row['running_balance'], 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted" style="padding: 25px;">
                                        <i class="fa fa-info-circle fa-2x"></i><br>
                                        No sales or payment transactions recorded for this customer in the selected period.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot style="background-color: #eaedf2; font-weight: bold; font-size: 13px;">
                            <tr>
                                <td colspan="5" style="text-align: right;">Period Totals:</td>
                                <td style="text-align: right; color: #6b46c1;">PKR {{ number_format($statement['period_total_sales'], 2) }}</td>
                                <td style="text-align: right; color: #2e8965;">PKR {{ number_format($statement['period_total_collected'], 2) }}</td>
                                <td style="text-align: right; color: #c53030;">PKR {{ number_format($statement['period_credit_sales'], 2) }}</td>
                                <td style="text-align: right; color: {{ $statement['closing_balance'] > 0 ? '#c53030' : '#2e8965' }}; font-size: 14px;">
                                    PKR {{ number_format($statement['closing_balance'], 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Reconciliation & Lifetime Summary Box -->
    <div class="widget-box widget-color-blue" style="margin-top: 20px;">
        <div class="widget-header">
            <h5 class="widget-title bigger lighter">
                <i class="ace-icon fa fa-balance-scale"></i> Customer Account Reconciliation Summary
            </h5>
        </div>
        <div class="widget-body">
            <div class="widget-main" style="padding: 15px;">
                <div class="row">
                    <div class="col-sm-6">
                        <table class="table table-condensed" style="margin-bottom: 0;">
                            <tr>
                                <td style="width: 60%;"><strong>Total Sales Amount (Cash, Credit & Online):</strong></td>
                                <td class="text-right"><strong>PKR {{ number_format($lifetimeSales, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td><strong>Total Cash Received (Sales + Receipts):</strong></td>
                                <td class="text-right text-success">PKR {{ number_format($lifetimeCashReceived, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Online / Bank Received:</strong></td>
                                <td class="text-right text-info">PKR {{ number_format($lifetimeOnlineReceived, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-6">
                        <table class="table table-condensed" style="margin-bottom: 0;">
                            <tr>
                                <td style="width: 60%;"><strong>Total Credit Sales Invoiced:</strong></td>
                                <td class="text-right text-warning">PKR {{ number_format($lifetimeCreditSales, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Amount Collected (Cash + Online):</strong></td>
                                <td class="text-right text-success"><strong>PKR {{ number_format($lifetimeTotalCollected, 2) }}</strong></td>
                            </tr>
                            <tr style="border-top: 2px solid #ddd;">
                                <td><strong>Remaining Outstanding Receivable:</strong></td>
                                <td class="text-right" style="font-size: 15px; font-weight: bold; color: {{ $currentBalance > 0 ? '#c53030' : '#2e8965' }};">
                                    PKR {{ number_format($currentBalance, 2) }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment / Collect Due Modal -->
<div class="modal fade no-print" id="collectPaymentModal" tabindex="-1" role="dialog" aria-labelledby="collectPaymentModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="collect_payment_form" method="POST" action="{{ route('customer.ledger.payment', $customer->id) }}">
                @csrf
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="collectPaymentModalLabel" style="color:white;">
                        <i class="fa fa-money"></i> Record Payment / Collect Customer Due
                    </h4>
                </div>

                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Customer:</strong> {{ $customer->name }} ({{ $customer->mobile ?? 'No Mobile' }})<br>
                        <strong>Current Outstanding Due:</strong> 
                        <span style="font-weight: bold; color: #c53030;">PKR {{ number_format($currentBalance, 2) }}</span>
                    </div>

                    <div class="form-group">
                        <label for="payment_amount"><strong>Amount Received (PKR) <span class="text-danger">*</span></strong></label>
                        <input type="number" step="0.01" min="0.01" class="form-control" id="payment_amount" name="amount" value="{{ $currentBalance > 0 ? $currentBalance : '' }}" placeholder="Enter amount collected" required>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="payment_method"><strong>Payment Method <span class="text-danger">*</span></strong></label>
                                <select class="form-control" id="payment_method" name="payment_method" required>
                                    <option value="cash" selected>Cash (Drawer / Hand)</option>
                                    <option value="bank_transfer">Bank Transfer / Online</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="payment_date"><strong>Payment Date <span class="text-danger">*</span></strong></label>
                                <input type="date" class="form-control" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="payment_reference">Reference / Receipt / Cheque No</label>
                        <input type="text" class="form-control" id="payment_reference" name="reference_no" placeholder="Optional check or receipt number">
                    </div>

                    <div class="form-group">
                        <label for="payment_notes">Notes / Remarks</label>
                        <textarea class="form-control" id="payment_notes" name="notes" rows="2" placeholder="Optional notes for this collection"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="btn_submit_payment" class="btn btn-success">
                        <i class="fa fa-check"></i> Record & Generate Voucher
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
function openPaymentModal() {
    $('#collectPaymentModal').modal('show');
}

@if($isPrint)
window.onload = function() {
    window.print();
};
@endif
</script>
@endsection
