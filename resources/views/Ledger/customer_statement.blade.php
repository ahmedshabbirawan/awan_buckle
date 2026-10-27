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
    body { background-color: #fff; font-size: 12px; }
    .widget-box { border: none !important; }
    .widget-header { display: none !important; }
    .widget-body { border: none !important; }
}
.summary-card {
    border-radius: 6px;
    padding: 15px;
    color: #fff;
    margin-bottom: 15px;
}
.summary-title {
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.9;
}
.summary-amount {
    font-size: 24px;
    font-weight: bold;
    margin-top: 5px;
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
                Detailed Transaction & Running Balance Ledger
            </small>
        </h1>
    </div>

    <!-- Customer Header Profile Bar -->
    <div class="widget-box widget-color-blue" style="margin-bottom: 15px;">
        <div class="widget-header">
            <h5 class="widget-title bigger lighter">
                <i class="ace-icon fa fa-user"></i> Customer Details
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
                        <span style="font-size: 13px; color: #555;">Current Net Balance:</span><br>
                        <span style="font-size: 22px; font-weight: bold; color: {{ $currentBalance > 0 ? '#d15b47' : '#87b87f' }};">
                            PKR {{ number_format($currentBalance, 2) }}
                        </span>
                        <div style="font-size: 11px; color: #888;">
                            {{ $currentBalance > 0 ? '(Receivable / Due)' : ($currentBalance < 0 ? '(Advance Credit)' : '(Settled)') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xs-12 col-sm-4">
            <div class="summary-card" style="background: linear-gradient(135deg, #2b6cb0, #3182ce);">
                <div class="summary-title"><i class="fa fa-shopping-cart"></i> Total Purchased / Billed</div>
                <div class="summary-amount">PKR {{ number_format($lifetimeBilled, 2) }}</div>
                <div style="font-size: 11px; opacity: 0.8; margin-top: 4px;">Lifetime Total Sales Value</div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-4">
            <div class="summary-card" style="background: linear-gradient(135deg, #2f855a, #38a169);">
                <div class="summary-title"><i class="fa fa-check-circle"></i> Total Paid (Cash / Bank)</div>
                <div class="summary-amount">PKR {{ number_format($lifetimePaid, 2) }}</div>
                <div style="font-size: 11px; opacity: 0.8; margin-top: 4px;">Total Settlements & Cash Collected</div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-4">
            <div class="summary-card" style="background: linear-gradient(135deg, #c53030, #e53e3e);">
                <div class="summary-title"><i class="fa fa-exclamation-circle"></i> Total Outstanding Due</div>
                <div class="summary-amount">PKR {{ number_format($currentBalance, 2) }}</div>
                <div style="font-size: 11px; opacity: 0.8; margin-top: 4px;">Net Receivable Debt</div>
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

    <!-- Statement Table -->
    <div class="widget-box widget-color-blue2">
        <div class="widget-header">
            <h5 class="widget-title bigger lighter">
                <i class="ace-icon fa fa-list-alt"></i> Account Statement
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
                                <th style="width: 12%;">Date</th>
                                <th style="width: 15%;">Reference No / Type</th>
                                <th>Description</th>
                                <th style="width: 14%; text-align: right;">Debit (Billed)</th>
                                <th style="width: 14%; text-align: right;">Credit (Paid)</th>
                                <th style="width: 16%; text-align: right;">Running Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Opening Balance Row -->
                            <tr style="background-color: #fff9e6; font-weight: bold;">
                                <td>{{ $startDate ? date('d-m-Y', strtotime($startDate)) : '-' }}</td>
                                <td><span class="label label-warning arrowed">OPENING</span></td>
                                <td><em>Opening Balance as of {{ $startDate ? date('d-m-Y', strtotime($startDate)) : 'Account Inception' }}</em></td>
                                <td style="text-align: right;">-</td>
                                <td style="text-align: right;">-</td>
                                <td style="text-align: right; color: {{ $statement['opening_balance'] > 0 ? '#d15b47' : '#87b87f' }};">
                                    PKR {{ number_format($statement['opening_balance'], 2) }}
                                </td>
                            </tr>

                            @forelse($statement['rows'] as $row)
                                <tr>
                                    <td>{{ $row['date'] }}</td>
                                    <td>
                                        <strong>{{ $row['reference_no'] }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $row['reference_type'] }}</small>
                                        @if($row['reference_type'] === 'Sale' && $row['reference_id'])
                                            <a href="{{ route('sale.order.detail', $row['reference_id']) }}" target="_blank" class="no-print" title="View Invoice">
                                                <i class="fa fa-external-link"></i>
                                            </a>
                                        @elseif(in_array($row['reference_type'], ['Payment', 'Receipt']) && $row['reference_id'])
                                            <a href="{{ route('customer.ledger.voucher', $row['reference_id']) }}" target="_blank" class="no-print" title="Print Receipt">
                                                <i class="fa fa-print"></i>
                                            </a>
                                        @endif
                                    </td>
                                    <td>{{ $row['description'] }}</td>
                                    <td style="text-align: right; font-weight: {{ $row['debit'] > 0 ? 'bold' : 'normal' }};">
                                        {{ $row['debit'] > 0 ? 'PKR ' . number_format($row['debit'], 2) : '-' }}
                                    </td>
                                    <td style="text-align: right; font-weight: {{ $row['credit'] > 0 ? 'bold' : 'normal' }}; color: {{ $row['credit'] > 0 ? '#2e8965' : 'inherit' }};">
                                        {{ $row['credit'] > 0 ? 'PKR ' . number_format($row['credit'], 2) : '-' }}
                                    </td>
                                    <td style="text-align: right; font-weight: bold; color: {{ $row['running_balance'] > 0 ? '#d15b47' : '#87b87f' }};">
                                        PKR {{ number_format($row['running_balance'], 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted" style="padding: 20px;">
                                        No transactions recorded in this period.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot style="background-color: #eaedf2; font-weight: bold; font-size: 14px;">
                            <tr>
                                <td colspan="3" style="text-align: right;">Period Totals:</td>
                                <td style="text-align: right; color: #2b6cb0;">PKR {{ number_format($statement['total_debit'], 2) }}</td>
                                <td style="text-align: right; color: #2f855a;">PKR {{ number_format($statement['total_credit'], 2) }}</td>
                                <td style="text-align: right; color: {{ $statement['closing_balance'] > 0 ? '#d15b47' : '#87b87f' }};">
                                    PKR {{ number_format($statement['closing_balance'], 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
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
                        <span style="font-weight: bold; color: #d15b47;">PKR {{ number_format($currentBalance, 2) }}</span>
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
