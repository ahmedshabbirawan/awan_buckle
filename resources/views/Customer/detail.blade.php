@extends('Layout.master')

@section('title')
Customer Detail - {{ $row->name }}
@endsection

@section('content')
<style>
    .profile-info-name {
        width: 190px;
    }
    .profile-info-value span {
        font-weight: bold;
    }
    .detail-card-stat {
        padding: 10px;
        background: #fdfdfd;
        border-radius: 4px;
        border-left: 4px solid #438eb9;
        margin-bottom: 8px;
    }
    .modal-header.modal-header-danger {
        background-color: #d9534f;
        color: #ffffff;
    }
    .modal-header.modal-header-primary {
        background-color: #337ab7;
        color: #ffffff;
    }
    .modal-header.modal-header-info {
        background-color: #5bc0de;
        color: #ffffff;
    }
</style>

<div class="page-content">
    <div class="page-header" style="min-height:45px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
        <div>
            <h1>
                <a href="{{ route('customer.list') }}" class="btn btn-xs btn-default" style="margin-right: 6px;">
                    <i class="ace-icon fa fa-arrow-left"></i>
                </a>
                Customer Detail: <span class="blue">{{ $row->name }}</span>
            </h1>
        </div>
        <div class="btn-group" style="margin-top: 5px;">
            <button type="button" class="btn btn-sm btn-danger" onclick="openCreditTransactionModal();">
                <i class="ace-icon fa fa-plus-circle bigger-110"></i> Create Credit Transaction
            </button>
            <button type="button" class="btn btn-sm btn-success" onclick="openCustomerPaymentModal();">
                <i class="ace-icon fa fa-money bigger-110"></i> Collect Due / Record Payment
            </button>
            <a href="{{ route('customer.ledger.show', $row->id) }}" class="btn btn-sm btn-info">
                <i class="ace-icon fa fa-book bigger-110"></i> View Statement
            </a>
            <a href="{{ route('customer.edit', $row->id) }}" class="btn btn-sm btn-warning">
                <i class="ace-icon fa fa-pencil bigger-110"></i> Edit Customer
            </a>
        </div>
    </div>

    <!-- Main Profile and Stats Row -->
    <div class="row">
        <div class="col-xs-12">
            <div class="table-detail" style="background-color: #fff; border: 1px solid #e0e0e0; border-radius: 4px; padding: 15px; margin-bottom: 20px;">
                <div class="row">
                    <!-- Avatar Column -->
                    <div class="col-xs-12 col-sm-2 text-center">
                        <div style="background: #eef4f9; border-radius: 50%; width: 100px; height: 100px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                            <i class="fa fa-user" style="font-size: 55px; color: #438eb9;"></i>
                        </div>
                        <div class="label label-info label-lg arrowed-in arrowed-in-right" style="display: block; max-width: 140px; margin: 0 auto; text-overflow: ellipsis; overflow: hidden;">
                            {{ $row->name }}
                        </div>
                        <div style="margin-top: 8px;">
                            {!! $row->status_label !!}
                        </div>
                    </div>

                    <!-- Customer Info Column -->
                    <div class="col-xs-12 col-sm-6">
                        <div class="profile-user-info profile-user-info-striped" style="margin-left: 0;">
                            <div class="profile-info-row">
                                <div class="profile-info-name"> Customer ID </div>
                                <div class="profile-info-value">
                                    <span class="badge badge-grey">#{{ $row->id }}</span>
                                </div>
                            </div>
                            <div class="profile-info-row">
                                <div class="profile-info-name"> Full Name </div>
                                <div class="profile-info-value">
                                    <span>{{ $row->name }}</span>
                                </div>
                            </div>
                            <div class="profile-info-row">
                                <div class="profile-info-name"> Mobile / Phone </div>
                                <div class="profile-info-value">
                                    <span>{{ $row->mobile ?: '-' }}</span>
                                </div>
                            </div>
                            <div class="profile-info-row">
                                <div class="profile-info-name"> CNIC </div>
                                <div class="profile-info-value">
                                    <span>{{ $row->cnic ?: '-' }}</span>
                                </div>
                            </div>
                            <div class="profile-info-row">
                                <div class="profile-info-name"> Address </div>
                                <div class="profile-info-value">
                                    <i class="fa fa-map-marker light-orange bigger-110"></i>
                                    <span>{{ $row->address ?: '-' }}</span>
                                </div>
                            </div>
                            <div class="profile-info-row">
                                <div class="profile-info-name"> Bazaar / City </div>
                                <div class="profile-info-value">
                                    <span>{{ $row->bazaar ? $row->bazaar . ', ' : '' }}{{ $row->city ?: '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial & Order Stats Column -->
                    <div class="col-xs-12 col-sm-4">
                        <div class="widget-box" style="border: 1px solid #dce4eb;">
                            <div class="widget-header widget-header-flat bg-primary" style="color: #fff;">
                                <h5 class="widget-title smaller">
                                    <i class="ace-icon fa fa-bar-chart"></i> Account & Sales Summary
                                </h5>
                            </div>
                            <div class="widget-body">
                                <div class="widget-main padding-8">
                                    <table class="table table-bordered table-striped" style="margin-bottom: 10px;">
                                        <tbody>
                                            <tr>
                                                <td style="width: 50%;">Registered Since</td>
                                                <th class="text-right">{{ $row->created_at ? $row->created_at->format('d-M-Y') : '-' }}</th>
                                            </tr>
                                            <tr>
                                                <td>Total POS Orders</td>
                                                <th class="text-right">{{ $stats['orders'] }}</th>
                                            </tr>
                                            <tr>
                                                <td>Total Orders Amount</td>
                                                <th class="text-right">{{ $stats['orders_amount'] }}</th>
                                            </tr>
                                            <tr>
                                                <td>Total Amount Paid</td>
                                                <th class="text-right" style="color: #2e8965;">{{ $stats['pay'] }}</th>
                                            </tr>
                                            <tr style="background-color: {{ ($stats['raw_due'] ?? 0) > 0 ? '#fbeae5' : '#edf7ed' }};">
                                                <td><strong>Outstanding Due</strong></td>
                                                <th class="text-right" style="font-size: 15px; color: {{ ($stats['raw_due'] ?? 0) > 0 ? '#d9534f' : '#3c763d' }};" id="customer_due_display">
                                                    {{ $stats['due'] }}
                                                </th>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="row">
                                        <div class="col-xs-6" style="padding-right: 4px;">
                                            <button type="button" class="btn btn-xs btn-danger btn-block" onclick="openCreditTransactionModal();">
                                                <i class="fa fa-plus-circle"></i> Add Credit
                                            </button>
                                        </div>
                                        <div class="col-xs-6" style="padding-left: 4px;">
                                            <button type="button" class="btn btn-xs btn-success btn-block" onclick="openCustomerPaymentModal();">
                                                <i class="fa fa-money"></i> Receive Pay
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Container for Transactions & Orders -->
    <div class="row">
        <div class="col-xs-12">
            <div class="tabbable">
                <ul class="nav nav-tabs" id="customerTabs">
                    <li class="active">
                        <a data-toggle="tab" href="#manual-credit-tab">
                            <i class="green ace-icon fa fa-credit-card bigger-120"></i>
                            Manual Credit Transactions History
                            <span class="badge badge-danger" id="credit_count_badge">{{ count($creditTransactions) }}</span>
                        </a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="#orders-tab">
                            <i class="blue ace-icon fa fa-shopping-cart bigger-120"></i>
                            Sales Orders
                        </a>
                    </li>
                </ul>

                <div class="tab-content" style="padding: 15px 12px;">
                    <!-- Manual Credit Transactions Tab -->
                    <div id="manual-credit-tab" class="tab-pane fade in active">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <div>
                                <h4 class="header smaller lighter blue" style="margin: 0;">
                                    <i class="fa fa-list"></i> Recorded Credit Transactions (Receivables)
                                </h4>
                                <small class="text-muted">Manually recorded receivable debt independent of POS sales orders</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger" onclick="openCreditTransactionModal();">
                                <i class="fa fa-plus-circle"></i> Create Credit Transaction
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="credit-transactions-table" style="width: 100%;">
                                <thead style="background-color: #f7f9fb;">
                                    <tr>
                                        <th style="width: 9%;">Date</th>
                                        <th style="width: 14%;">Transaction ID</th>
                                        <th style="width: 13%;">Transaction Type</th>
                                        <th style="width: 11%;">Category</th>
                                        <th>Description</th>
                                        <th style="width: 10%;">Reference No</th>
                                        <th style="width: 9%; text-align: right;">Debit</th>
                                        <th style="width: 7%; text-align: right;">Credit</th>
                                        <th style="width: 11%; text-align: right;">Outstanding Balance</th>
                                        <th style="width: 10%;">Created By</th>
                                        <th style="width: 6%; text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($creditTransactions as $ctx)
                                        <tr>
                                            <td>{{ $ctx->transaction_date ? $ctx->transaction_date->format('d-m-Y') : '-' }}</td>
                                            <td>
                                                <strong class="blue">{{ $ctx->transaction_id }}</strong>
                                                @if($ctx->attachment)
                                                    <a href="{{ route('credit_debt_transaction.attachment', $ctx->id) }}" target="_blank" title="Download Attachment" style="margin-left: 4px; color: #555;">
                                                        <i class="fa fa-paperclip text-warning"></i>
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="label label-danger arrowed">{{ $ctx->type_label }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-grey">{{ $ctx->category_label }}</span>
                                            </td>
                                            <td>{{ $ctx->description ?: '-' }}</td>
                                            <td>{{ $ctx->reference_no ?: '-' }}</td>
                                            <td style="text-align: right; font-weight: bold; color: #d9534f;">
                                                PKR {{ number_format($ctx->amount, 2) }}
                                            </td>
                                            <td style="text-align: right; color: #888;">
                                                -
                                            </td>
                                            <td style="text-align: right; font-weight: bold; color: {{ $ctx->balance_after > 0 ? '#d9534f' : '#3c763d' }};">
                                                PKR {{ number_format($ctx->balance_after, 2) }}
                                            </td>
                                            <td>{{ optional($ctx->creator)->name ?: 'System' }}</td>
                                            <td style="text-align: center;">
                                                <button type="button" class="btn btn-xs btn-info" onclick="viewTransactionDetails({{ $ctx->id }});" title="View Transaction Details">
                                                    <i class="ace-icon fa fa-eye bigger-120"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="empty_credit_row">
                                            <td colspan="11" class="text-center text-muted" style="padding: 25px;">
                                                <i class="fa fa-info-circle fa-2x" style="color: #bbb; display: block; margin-bottom: 8px;"></i>
                                                No manual credit transactions recorded for this customer yet.
                                                <div style="margin-top: 8px;">
                                                    <button type="button" class="btn btn-xs btn-danger" onclick="openCreditTransactionModal();">
                                                        <i class="fa fa-plus-circle"></i> Create First Credit Transaction
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Orders Tab -->
                    <div id="orders-tab" class="tab-pane fade">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered yajratable" id="orders-table" style="width:100%"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Modal 1: Create Credit Transaction Modal                                   -->
<!-- ========================================================================= -->
<div class="modal fade" id="createCreditModal" tabindex="-1" role="dialog" aria-labelledby="createCreditModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="create_credit_form" method="POST" action="{{ route('customer.credit_transaction.store', $row->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header modal-header-danger">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff; opacity: 0.9;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="createCreditModalLabel">
                        <i class="fa fa-plus-circle"></i> Create Customer Credit Transaction
                    </h4>
                </div>

                <div class="modal-body" style="padding: 20px;">
                    <div class="alert alert-info" style="margin-bottom: 15px; font-size: 13px;">
                        <i class="fa fa-info-circle"></i> Recording this credit increases customer receivable and outstanding balance without creating a sales order or altering inventory.
                    </div>

                    <!-- Customer Name (preselected and readonly) -->
                    <div class="form-group">
                        <label for="credit_customer_name"><strong>Customer Name <span class="text-danger">*</span></strong></label>
                        <input type="text" class="form-control" id="credit_customer_name" value="{{ $row->name }} (ID: #{{ $row->id }})" readonly style="background-color: #eee; font-weight: bold;">
                    </div>

                    <div class="row">
                        <!-- Transaction Date -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="credit_transaction_date"><strong>Transaction Date <span class="text-danger">*</span></strong></label>
                                <input type="date" class="form-control" id="credit_transaction_date" name="transaction_date" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <!-- Transaction Type (Credit / Amount Receivable) -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="credit_transaction_type"><strong>Transaction Type <span class="text-danger">*</span></strong></label>
                                <input type="text" class="form-control" id="credit_transaction_type" value="Credit / Amount Receivable" readonly style="background-color: #fce8e6; color: #d9534f; font-weight: bold;">
                                <input type="hidden" name="type" value="credit">
                            </div>
                        </div>
                    </div>

                    <!-- Amount -->
                    <div class="form-group">
                        <label for="credit_amount"><strong>Amount (PKR) <span class="text-danger">*</span></strong></label>
                        <div class="input-group">
                            <span class="input-group-addon"><strong>PKR</strong></span>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="credit_amount" name="amount" placeholder="0.00" required style="font-size: 16px; font-weight: bold;">
                        </div>
                    </div>

                    <!-- Description / Remarks -->
                    <div class="form-group">
                        <label for="credit_description"><strong>Description / Remarks</strong></label>
                        <textarea class="form-control" id="credit_description" name="description" rows="3" placeholder="Enter reason or details for this receivable credit transaction..."></textarea>
                    </div>

                    <div class="row">
                        <!-- Reference Number (optional) -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="credit_reference_no">Reference Number <small class="text-muted">(Optional)</small></label>
                                <input type="text" class="form-control" id="credit_reference_no" name="reference_no" placeholder="e.g. SLIP-1029, PROMISSORY-44">
                            </div>
                        </div>

                        <!-- Attachment (optional) -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="credit_attachment">Attachment <small class="text-muted">(Optional doc/image)</small></label>
                                <input type="file" class="form-control" id="credit_attachment" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="btn_save_credit" class="btn btn-danger">
                        <i class="fa fa-save"></i> Save Credit Transaction
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Modal 2: View Credit / Debt Transaction Details Modal                     -->
<!-- ========================================================================= -->
<div class="modal fade" id="viewTransactionModal" tabindex="-1" role="dialog" aria-labelledby="viewTransactionModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-info">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff; opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="viewTransactionModalLabel">
                    <i class="fa fa-file-text-o"></i> Transaction Details
                </h4>
            </div>

            <div class="modal-body" style="padding: 20px;">
                <div id="tx_modal_loading" class="text-center" style="padding: 30px;">
                    <i class="fa fa-spinner fa-spin fa-2x blue"></i>
                    <p style="margin-top: 10px;">Loading transaction details...</p>
                </div>

                <div id="tx_modal_content" style="display: none;">
                    <div class="row">
                        <div class="col-sm-6">
                            <table class="table table-bordered table-striped">
                                <tbody>
                                    <tr>
                                        <th style="width: 40%;">Transaction ID</th>
                                        <td><span class="label label-primary" id="v_tx_id" style="font-size: 13px;"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Party Type & Name</th>
                                        <td><strong id="v_party_name"></strong> (<span id="v_party_type"></span>)</td>
                                    </tr>
                                    <tr>
                                        <th>Transaction Date</th>
                                        <td id="v_tx_date"></td>
                                    </tr>
                                    <tr>
                                        <th>Transaction Type</th>
                                        <td><span class="label label-danger" id="v_tx_type"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Category</th>
                                        <td><span class="badge badge-grey" id="v_tx_category"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-sm-6">
                            <table class="table table-bordered table-striped">
                                <tbody>
                                    <tr>
                                        <th style="width: 40%;">Transaction Amount</th>
                                        <td><strong class="text-danger" style="font-size: 16px;" id="v_amount"></strong></td>
                                    </tr>
                                    <tr>
                                        <th>Debit</th>
                                        <td id="v_debit" style="font-weight: bold; color: #d9534f;"></td>
                                    </tr>
                                    <tr>
                                        <th>Credit</th>
                                        <td id="v_credit"></td>
                                    </tr>
                                    <tr>
                                        <th>Outstanding Balance After Posting</th>
                                        <td><strong id="v_balance_after" style="font-size: 14px;"></strong></td>
                                    </tr>
                                    <tr>
                                        <th>Reference Number</th>
                                        <td id="v_reference_no"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 10px;">
                        <div class="col-xs-12">
                            <div class="well well-sm" style="margin-bottom: 10px;">
                                <strong>Description / Remarks:</strong>
                                <p id="v_description" style="margin-top: 5px; margin-bottom: 0; white-space: pre-wrap;"></p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <small class="text-muted">
                                <strong>Created By:</strong> <span id="v_created_by"></span> | 
                                <strong>Created At:</strong> <span id="v_created_at"></span>
                            </small>
                        </div>
                        <div class="col-sm-6 text-right" id="v_attachment_container">
                            <!-- Attachment button dynamically injected -->
                        </div>
                    </div>

                    <div style="margin-top: 15px;" id="v_entries_box">
                        <h5 class="header smaller lighter blue" style="margin-bottom: 8px;">
                            <i class="fa fa-book"></i> Double-Entry Accounting Ledger Postings
                        </h5>
                        <table class="table table-bordered table-condensed" style="font-size: 12px; margin-bottom: 0;">
                            <thead style="background-color: #f7f9fb;">
                                <tr>
                                    <th>Account Name</th>
                                    <th>Account Code</th>
                                    <th style="text-align: right; width: 120px;">Debit</th>
                                    <th style="text-align: right; width: 120px;">Credit</th>
                                </tr>
                            </thead>
                            <tbody id="v_entries_tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Modal 3: Collect Customer Due / Payment Modal                             -->
<!-- ========================================================================= -->
<div class="modal fade" id="collectPaymentModal" tabindex="-1" role="dialog" aria-labelledby="collectPaymentModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="collect_payment_form" method="POST" action="{{ route('customer.ledger.payment', $row->id) }}">
                @csrf
                <div class="modal-header modal-header-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff; opacity: 0.9;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="collectPaymentModalLabel">
                        <i class="fa fa-money"></i> Collect Customer Due / Record Payment
                    </h4>
                </div>

                <div class="modal-body" style="padding: 20px;">
                    <div class="alert alert-info" style="margin-bottom: 15px;">
                        <strong>Customer:</strong> {{ $row->name }} ({{ $row->mobile ?: 'No Mobile' }})<br>
                        <strong>Current Outstanding Due:</strong> 
                        <span style="font-weight: bold; color: #d9534f;" id="modal_due_text">{{ $stats['due'] }}</span>
                    </div>

                    <div class="form-group">
                        <label for="payment_amount"><strong>Amount Received (PKR) <span class="text-danger">*</span></strong></label>
                        <div class="input-group">
                            <span class="input-group-addon"><strong>PKR</strong></span>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="payment_amount" name="amount" value="{{ ($stats['raw_due'] ?? 0) > 0 ? $stats['raw_due'] : '' }}" placeholder="Enter collected amount" required style="font-size: 16px; font-weight: bold;">
                        </div>
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
                        <label for="payment_reference">Reference / Receipt / Cheque No <small class="text-muted">(Optional)</small></label>
                        <input type="text" class="form-control" id="payment_reference" name="reference_no" placeholder="Optional bank slip or cheque number">
                    </div>

                    <div class="form-group">
                        <label for="payment_notes">Notes / Remarks <small class="text-muted">(Optional)</small></label>
                        <textarea class="form-control" id="payment_notes" name="notes" rows="2" placeholder="Optional collection notes"></textarea>
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

<!--- delete confirmation modal --->
<div class="modal" tabindex="-1" role="dialog" id="deletePopup">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post" id="delete_form">
                @csrf
                <input type="hidden" name="_method" value="delete" />

                <div class="modal-header">
                    <h5 class="modal-title">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Do you really want to delete this customer?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Confirm</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
    function deleteConfirmation(objID) {
        $('#delete_form').attr('action', '{{ $adminURL }}/' + objID);
        $('#deletePopup').modal('show');
    }

    function openCreditTransactionModal() {
        $('#create_credit_form')[0].reset();
        $('#credit_transaction_date').val('{{ date("Y-m-d") }}');
        $('#createCreditModal').modal('show');
    }

    function openCustomerPaymentModal() {
        $('#collect_payment_form')[0].reset();
        $('#payment_date').val('{{ date("Y-m-d") }}');
        $('#collectPaymentModal').modal('show');
    }

    function viewTransactionDetails(id) {
        $('#tx_modal_loading').show();
        $('#tx_modal_content').hide();
        $('#viewTransactionModal').modal('show');

        $.ajax({
            url: "{{ url('/credit-debt-transaction') }}/" + id,
            type: "GET",
            dataType: "json",
            success: function(res) {
                $('#tx_modal_loading').hide();
                $('#tx_modal_content').show();

                $('#v_tx_id').text(res.transaction_id);
                $('#v_party_name').text(res.party_name);
                $('#v_party_type').text(res.party_type);
                $('#v_tx_date').text(res.transaction_date);
                $('#v_tx_type').text(res.type_label);
                $('#v_tx_category').text(res.category_label);
                $('#v_amount').text(res.formatted_amount);
                $('#v_debit').text(res.formatted_debit);
                $('#v_credit').text(res.formatted_credit);
                $('#v_balance_after').text(res.formatted_balance_after);
                $('#v_reference_no').text(res.reference_no);
                $('#v_description').text(res.description);
                $('#v_created_by').text(res.created_by_name);
                $('#v_created_at').text(res.created_at);

                if (res.has_attachment && res.attachment_url) {
                    $('#v_attachment_container').html(
                        '<a href="' + res.attachment_url + '" target="_blank" class="btn btn-xs btn-warning">' +
                        '<i class="fa fa-download"></i> Download Attachment (' + res.attachment_name + ')' +
                        '</a>'
                    );
                } else {
                    $('#v_attachment_container').html('<span class="text-muted"><i class="fa fa-ban"></i> No Attachment</span>');
                }

                var entriesHtml = '';
                if (res.entries && res.entries.length > 0) {
                    $.each(res.entries, function(idx, e) {
                        entriesHtml += '<tr>' +
                            '<td>' + e.account_name + '</td>' +
                            '<td><code>' + (e.account_code || '-') + '</code></td>' +
                            '<td style="text-align: right; font-weight: bold; color: ' + (e.debit > 0 ? '#d9534f' : '#888') + ';">' + e.formatted_debit + '</td>' +
                            '<td style="text-align: right; font-weight: bold; color: ' + (e.credit > 0 ? '#3c763d' : '#888') + ';">' + e.formatted_credit + '</td>' +
                            '</tr>';
                    });
                }
                $('#v_entries_tbody').html(entriesHtml);
            },
            error: function() {
                $('#tx_modal_loading').html('<div class="alert alert-danger">Failed to load transaction details.</div>');
            }
        });
    }

    $(document).ready(function() {
        // Handle Create Credit Transaction Form Submit
        $('#create_credit_form').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            var $btn = $('#btn_save_credit');

            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Credit Transaction');
                    $('#createCreditModal').modal('hide');

                    // Update Due Balance Display on Page
                    if (res.formatted_balance) {
                        $('#customer_due_display').text(res.formatted_balance);
                        $('#modal_due_text').text(res.formatted_balance);
                    }

                    if (typeof $.confirm === 'function') {
                        $.confirm({
                            title: 'Credit Recorded Successfully',
                            content: res.message,
                            type: 'green',
                            buttons: {
                                ok: {
                                    text: 'OK',
                                    btnClass: 'btn-green',
                                    action: function() {
                                        window.location.reload();
                                    }
                                }
                            }
                        });
                    } else {
                        alert(res.message);
                        window.location.reload();
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Credit Transaction');
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to record credit transaction.';
                    if (typeof $.confirm === 'function') {
                        $.confirm({
                            title: 'Error',
                            content: msg,
                            type: 'red',
                            buttons: {
                                ok: {
                                    text: 'OK',
                                    btnClass: 'btn-red'
                                }
                            }
                        });
                    } else {
                        alert(msg);
                    }
                }
            });
        });

        // Handle Payment Collection Form Submit
        $('#collect_payment_form').on('submit', function(e) {
            e.preventDefault();
            var $btn = $('#btn_submit_payment');
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Recording...');

            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    $btn.prop('disabled', false).html('<i class="fa fa-check"></i> Record & Generate Voucher');
                    $('#collectPaymentModal').modal('hide');

                    if (res.voucher_url) {
                        window.open(res.voucher_url, '_blank');
                    }

                    if (typeof $.confirm === 'function') {
                        $.confirm({
                            title: 'Payment Recorded',
                            content: res.message,
                            type: 'green',
                            buttons: {
                                ok: {
                                    text: 'OK',
                                    btnClass: 'btn-green',
                                    action: function() {
                                        window.location.reload();
                                    }
                                }
                            }
                        });
                    } else {
                        alert(res.message);
                        window.location.reload();
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('<i class="fa fa-check"></i> Record & Generate Voucher');
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to record payment.';
                    if (typeof $.confirm === 'function') {
                        $.confirm({
                            title: 'Error',
                            content: msg,
                            type: 'red',
                            buttons: {
                                ok: {
                                    text: 'OK',
                                    btnClass: 'btn-red'
                                }
                            }
                        });
                    } else {
                        alert(msg);
                    }
                }
            });
        });

        // Initialize Orders DataTable
        setTimeout(function() {
            $('#orders-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('sale.order.list') }}?group_by=issue_key&customer_id={{ $row->id }}",
                columns: [
                    { data: 'id', name: 'id', title: 'ID.', width: '5%' },
                    { data: 'customer_name', name: 'customer_name', title: 'Customer' },
                    { data: 'item_count', name: 'item_count', title: 'Items' },
                    { data: 'total_offer_price', name: 'total_offer_price', title: 'Offer Amount' },
                    { data: 'total_price', name: 'total_price', title: 'Bill Price' },
                    { data: 'total_discount', name: 'total_discount', title: 'Total Discount' },
                    { data: 'shop_name', name: 'shop_name', title: 'Shop' },
                    { data: 'order_time', name: 'order_time', title: 'Date N Time' },
                    { data: 'action', name: 'action', title: 'Action' }
                ],
                order: [[0, 'desc']]
            });
        }, 300);
    });
</script>
@endsection