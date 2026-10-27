@extends('Layout.master')

@section('title')
Supplier Statement - {{ $supplier->name }}
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
    min-width: 190px;
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
    font-size: 20px;
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
            <a href="{{ route('supplier.ledger.index') }}" class="btn btn-xs btn-default" style="margin-right: 8px;">
                <i class="fa fa-arrow-left"></i> Back to Ledgers
            </a>
            Supplier Statement: <span class="blue">{{ $supplier->name }}</span>
            <small>
                <i class="ace-icon fa fa-angle-double-right"></i>
                Detailed Transaction, Purchase & Running Balance Reconciliation
            </small>
        </h1>
    </div>

    <!-- Supplier Header Profile Bar -->
    <div class="widget-box widget-color-blue" style="margin-bottom: 15px;">
        <div class="widget-header">
            <h5 class="widget-title bigger lighter">
                <i class="ace-icon fa fa-truck"></i> Supplier Profile & Current Balance
            </h5>
            <div class="widget-toolbar no-print">
                <button type="button" class="btn btn-xs btn-warning" onclick="openSupplierPaymentModal();">
                    <i class="fa fa-credit-card"></i> Record Payment / Payout
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
                        <strong>Supplier Name:</strong> {{ $supplier->name }}<br>
                        <strong>Supplier ID:</strong> #{{ $supplier->id }}
                    </div>
                    <div class="col-sm-3">
                        <strong>Phone:</strong> {{ $supplier->phone ?? 'N/A' }}<br>
                        <strong>Supplier Code:</strong> {{ $supplier->code ?? 'N/A' }}
                    </div>
                    <div class="col-sm-3">
                        <strong>Address:</strong> {{ $supplier->address ?? 'N/A' }}<br>
                        <strong>Account Code:</strong> {{ $account->code }}
                    </div>
                    <div class="col-sm-3 text-right">
                        <span style="font-size: 13px; color: #555;">Current Net Liability:</span><br>
                        <span style="font-size: 22px; font-weight: bold; color: {{ $currentBalance > 0 ? '#c53030' : '#87b87f' }};">
                            PKR {{ number_format($currentBalance, 2) }}
                        </span>
                        <div style="font-size: 11px; color: #888;">
                            {{ $currentBalance > 0 ? '(Accounts Payable / Outstanding Due)' : ($currentBalance < 0 ? '(Supplier Advance Debit)' : '(Fully Settled)') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5 Summary Reconciliation Cards -->
    <div class="summary-row">
        <!-- Card 1: Total Purchases -->
        <div class="summary-col">
            <div class="summary-card" style="background: linear-gradient(135deg, #6b46c1, #805ad5);">
                <div class="summary-title"><i class="fa fa-shopping-basket"></i> Total Purchases</div>
                <div class="summary-amount">PKR {{ number_format($lifetimePurchased, 2) }}</div>
                <div class="summary-sub">All Cash & Credit Invoiced</div>
            </div>
        </div>

        <!-- Card 2: Total Cash Paid at Purchase -->
        <div class="summary-col">
            <div class="summary-card" style="background: linear-gradient(135deg, #0e7490, #06b6d4);">
                <div class="summary-title"><i class="fa fa-money"></i> Cash Purchases Paid</div>
                <div class="summary-amount">PKR {{ number_format($lifetimeCashPaid, 2) }}</div>
                <div class="summary-sub">Settled at Purchase</div>
            </div>
        </div>

        <!-- Card 3: Total Credit Purchases -->
        <div class="summary-col">
            <div class="summary-card" style="background: linear-gradient(135deg, #2b6cb0, #3182ce);">
                <div class="summary-title"><i class="fa fa-clock-o"></i> Credit Purchases</div>
                <div class="summary-amount">PKR {{ number_format($lifetimeCreditPurchases, 2) }}</div>
                <div class="summary-sub">Invoiced on Credit Terms</div>
            </div>
        </div>

        <!-- Card 4: Total Amount Paid -->
        <div class="summary-col">
            <div class="summary-card" style="background: linear-gradient(135deg, #2f855a, #38a169);">
                <div class="summary-title"><i class="fa fa-check-circle"></i> Total Amount Paid</div>
                <div class="summary-amount">PKR {{ number_format($lifetimePaid, 2) }}</div>
                <div class="summary-sub">Cash at Purchase + Vouchers</div>
            </div>
        </div>

        <!-- Card 5: Remaining Outstanding Balance -->
        <div class="summary-col">
            <div class="summary-card" style="background: linear-gradient(135deg, {{ $currentBalance > 0 ? '#c53030, #e53e3e' : '#2f855a, #38a169' }});">
                <div class="summary-title"><i class="fa fa-exclamation-triangle"></i> Remaining Balance</div>
                <div class="summary-amount">PKR {{ number_format($currentBalance, 2) }}</div>
                <div class="summary-sub">Current Outstanding Liability</div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="widget-box widget-color-grey no-print" style="margin-bottom: 15px;">
        <div class="widget-body">
            <div class="widget-main" style="padding: 10px 15px;">
                <form method="GET" action="{{ route('supplier.ledger.show', $supplier->id) }}" class="form-inline">
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
                        <a href="{{ route('supplier.ledger.show', $supplier->id) }}" class="btn btn-sm btn-default" style="margin-left: 5px;">
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
                <strong>Period Purchases:</strong> PKR {{ number_format($statement['period_total_purchased'], 2) }} | 
                <strong>Period Paid:</strong> PKR {{ number_format($statement['period_total_paid'], 2) }} | 
                <strong>Closing Balance:</strong> PKR {{ number_format($statement['closing_balance'], 2) }}
            </span>
        </div>
    @endif

    <!-- Statement Table -->
    <div class="widget-box widget-color-blue2">
        <div class="widget-header">
            <h5 class="widget-title bigger lighter">
                <i class="ace-icon fa fa-list-alt"></i> Complete Supplier Account Statement
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
                                <th style="width: 10%;">Method</th>
                                <th>Description / Notes</th>
                                <th style="width: 12%; text-align: right;">Total Purchase</th>
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
                                <td style="text-align: right; color: {{ $statement['opening_balance'] > 0 ? '#c53030' : '#87b87f' }};">
                                    PKR {{ number_format($statement['opening_balance'], 2) }}
                                </td>
                            </tr>

                            @forelse($statement['rows'] as $row)
                                <tr>
                                    <td>{{ $row['date'] }}</td>
                                    <td>
                                        <strong>{{ $row['invoice_no'] }}</strong>
                                        @if($row['reference_type'] === 'Purchase' && $row['reference_id'])
                                            <a href="{{ route('stocks.delivery.detail', $row['reference_id']) }}" target="_blank" class="no-print" title="View Purchase Challan" style="margin-left: 4px;">
                                                <i class="fa fa-external-link"></i>
                                            </a>
                                        @elseif(in_array($row['reference_type'], ['Payment', 'Receipt']) && $row['reference_id'])
                                            <a href="{{ route('supplier.ledger.voucher', $row['reference_id']) }}" target="_blank" class="no-print" title="Print Payment Voucher" style="margin-left: 4px;">
                                                <i class="fa fa-print"></i>
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($row['type_badge'] === 'success')
                                            <span class="label label-success arrowed"><i class="fa fa-check"></i> {{ $row['type_label'] }}</span>
                                        @elseif($row['type_badge'] === 'danger')
                                            <span class="label label-danger arrowed"><i class="fa fa-clock-o"></i> {{ $row['type_label'] }}</span>
                                        @elseif($row['type_badge'] === 'warning')
                                            <span class="label label-warning arrowed"><i class="fa fa-pie-chart"></i> {{ $row['type_label'] }}</span>
                                        @elseif($row['type_badge'] === 'info')
                                            <span class="label label-info arrowed"><i class="fa fa-credit-card"></i> {{ $row['type_label'] }}</span>
                                        @else
                                            <span class="label label-inverse arrowed">{{ $row['type_label'] }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-grey">{{ $row['payment_method'] }}</span>
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
                                    <td style="text-align: right; font-weight: bold; color: {{ $row['running_balance'] > 0 ? '#c53030' : '#87b87f' }};">
                                        PKR {{ number_format($row['running_balance'], 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted" style="padding: 20px;">
                                        No transactions recorded in this period.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot style="background-color: #eaedf2; font-weight: bold; font-size: 13px;">
                            <tr>
                                <td colspan="5" style="text-align: right;">Period Totals:</td>
                                <td style="text-align: right; color: #6b46c1;">PKR {{ number_format($statement['period_total_purchased'], 2) }}</td>
                                <td style="text-align: right; color: #2f855a;">PKR {{ number_format($statement['period_total_paid'], 2) }}</td>
                                <td style="text-align: right; color: #c53030;">PKR {{ number_format($statement['period_total_credit_purchases'], 2) }}</td>
                                <td style="text-align: right; color: {{ $statement['closing_balance'] > 0 ? '#c53030' : '#87b87f' }};">
                                    PKR {{ number_format($statement['closing_balance'], 2) }}
                                </td>
                            </tr>
                            <tr class="no-print">
                                <td colspan="9" style="font-size: 11px; color: #666; background-color: #fcfcfc; font-weight: normal;">
                                    <strong>Reconciliation Summary:</strong>
                                    Cash Paid at Purchase: <strong>PKR {{ number_format($statement['period_total_cash_paid'], 2) }}</strong> | 
                                    Payout Vouchers Paid: <strong>PKR {{ number_format($statement['period_total_voucher_paid'], 2) }}</strong> | 
                                    Total Paid: <strong>PKR {{ number_format($statement['period_total_paid'], 2) }}</strong> | 
                                    Credit Purchases Incurred: <strong>PKR {{ number_format($statement['period_total_credit_purchases'], 2) }}</strong> | 
                                    Closing Outstanding Liability: <strong>PKR {{ number_format($statement['closing_balance'], 2) }}</strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Supplier Payment Modal -->
<div class="modal fade no-print" id="supplierPaymentModal" tabindex="-1" role="dialog" aria-labelledby="supplierPaymentModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="supplier_payment_form" method="POST" action="{{ route('supplier.ledger.payment', $supplier->id) }}">
                @csrf
                <div class="modal-header bg-warning">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="supplierPaymentModalLabel" style="color:#333;">
                        <i class="fa fa-credit-card"></i> Record Supplier Payment / Payout Voucher
                    </h4>
                </div>

                <div class="modal-body">
                    <div class="alert alert-warning">
                        <strong>Supplier:</strong> {{ $supplier->name }} ({{ $supplier->phone ?? 'No Phone' }})<br>
                        <strong>Current Outstanding Liability:</strong> 
                        <span style="font-weight: bold; color: #c53030;">PKR {{ number_format($currentBalance, 2) }}</span>
                    </div>

                    <div class="form-group">
                        <label for="supplier_payment_amount"><strong>Amount Paid (PKR) <span class="text-danger">*</span></strong></label>
                        <input type="number" step="0.01" min="0.01" class="form-control" id="supplier_payment_amount" name="amount" value="{{ $currentBalance > 0 ? $currentBalance : '' }}" placeholder="Enter payout amount" required>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="supplier_payment_method"><strong>Payment Account / Method <span class="text-danger">*</span></strong></label>
                                <select class="form-control" id="supplier_payment_method" name="payment_method" required>
                                    <option value="cash" selected>Cash (Drawer / Hand)</option>
                                    <option value="bank_transfer">Bank Transfer / Online</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="supplier_payment_date"><strong>Payment Date <span class="text-danger">*</span></strong></label>
                                <input type="date" class="form-control" id="supplier_payment_date" name="payment_date" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="supplier_payment_reference">Reference / Cheque / Transaction No</label>
                        <input type="text" class="form-control" id="supplier_payment_reference" name="reference_no" placeholder="Optional check or transaction slip number">
                    </div>

                    <div class="form-group">
                        <label for="supplier_payment_notes">Notes / Remarks</label>
                        <textarea class="form-control" id="supplier_payment_notes" name="notes" rows="2" placeholder="Optional notes for this payout"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="btn_submit_supplier_payment" class="btn btn-warning">
                        <i class="fa fa-check"></i> Record Payout & Generate Voucher
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
function openSupplierPaymentModal() {
    $('#supplierPaymentModal').modal('show');
}

@if($isPrint)
window.onload = function() {
    window.print();
};
@endif
</script>
@endsection
