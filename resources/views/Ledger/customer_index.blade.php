@extends('Layout.master')

@section('title')
Customer Ledgers (Accounts Receivable)
@endsection

@section('content')
<div class="page-content">
    <div class="page-header">
        <h1>
            <i class="ace-icon fa fa-users blue"></i> Customer Ledgers
            <small>
                <i class="ace-icon fa fa-angle-double-right"></i>
                Accounts Receivable & Balance Statement Directory
            </small>
        </h1>
    </div>

    <!-- Summary Metrics Cards -->
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-xs-12 col-sm-3">
            <div class="infobox infobox-blue infobox-large infobox-dark" style="width:100%; border-radius: 6px;">
                <div class="infobox-icon">
                    <i class="ace-icon fa fa-users"></i>
                </div>
                <div class="infobox-data">
                    <div class="infobox-content" style="font-size: 13px;">Total Customers</div>
                    <div class="infobox-content" style="font-size: 22px; font-weight: bold;">{{ number_format($customerCount) }}</div>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-3">
            <div class="infobox infobox-green infobox-large infobox-dark" style="width:100%; border-radius: 6px;">
                <div class="infobox-icon">
                    <i class="ace-icon fa fa-shopping-cart"></i>
                </div>
                <div class="infobox-data">
                    <div class="infobox-content" style="font-size: 13px;">Total Sales / Invoiced</div>
                    <div class="infobox-content" style="font-size: 20px; font-weight: bold;">PKR {{ number_format($totalBilled, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-3">
            <div class="infobox infobox-grey infobox-large infobox-dark" style="width:100%; border-radius: 6px;">
                <div class="infobox-icon">
                    <i class="ace-icon fa fa-money"></i>
                </div>
                <div class="infobox-data">
                    <div class="infobox-content" style="font-size: 13px;">Total Paid / Received</div>
                    <div class="infobox-content" style="font-size: 20px; font-weight: bold;">PKR {{ number_format($totalPaid, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-3">
            <div class="infobox infobox-red infobox-large infobox-dark" style="width:100%; border-radius: 6px;">
                <div class="infobox-icon">
                    <i class="ace-icon fa fa-exclamation-circle"></i>
                </div>
                <div class="infobox-data">
                    <div class="infobox-content" style="font-size: 13px;">Total Outstanding (Due)</div>
                    <div class="infobox-content" style="font-size: 20px; font-weight: bold;">PKR {{ number_format($totalReceivable, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Ledger Table -->
    <div class="row">
        <div class="col-xs-12">
            <div class="widget-box widget-color-blue2">
                <div class="widget-header">
                    <h5 class="widget-title bigger lighter">
                        <i class="ace-icon fa fa-table"></i> Customer Accounts Receivable List
                    </h5>
                    <div class="widget-toolbar">
                        <a href="{{ route('customer.create') }}" class="btn btn-xs btn-success">
                            <i class="ace-icon fa fa-plus"></i> Add New Customer
                        </a>
                    </div>
                </div>

                <div class="widget-body">
                    <div class="widget-main no-padding">
                        <div class="table-responsive" style="padding: 10px;">
                            <table class="table table-striped table-bordered table-hover" id="customer_ledger_table" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th style="width:5%;">#</th>
                                        <th>Customer Name</th>
                                        <th>Mobile / Phone</th>
                                        <th>CNIC</th>
                                        <th>Total Billed (Dr)</th>
                                        <th>Total Paid (Cr)</th>
                                        <th>Current Balance (Due)</th>
                                        <th style="width:15%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Collect Due / Payment Modal -->
<div class="modal fade" id="collectPaymentModal" tabindex="-1" role="dialog" aria-labelledby="collectPaymentModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="collect_payment_form" method="POST" action="javascript:void(0);">
                @csrf
                <input type="hidden" id="modal_customer_id" name="customer_id" value="">
                
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="collectPaymentModalLabel" style="color:white;">
                        <i class="fa fa-money"></i> Record Customer Payment / Collect Due
                    </h4>
                </div>

                <div class="modal-body">
                    <div class="alert alert-info" id="modal_customer_info" style="margin-bottom: 15px;">
                        <strong>Customer:</strong> <span id="modal_customer_name">-</span><br>
                        <strong>Current Outstanding Due:</strong> <span id="modal_customer_balance" style="font-weight: bold; color: #d15b47;">PKR 0.00</span>
                    </div>

                    <div class="form-group">
                        <label for="payment_amount"><strong>Amount Received (PKR) <span class="text-danger">*</span></strong></label>
                        <input type="number" step="0.01" min="0.01" class="form-control" id="payment_amount" name="amount" placeholder="Enter amount received" required>
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
                        <input type="text" class="form-control" id="payment_reference" name="reference_no" placeholder="Optional bank slip or check number">
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
var customerTable;

$(document).ready(function() {
    customerTable = $('#customer_ledger_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('customer.ledger.index') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name', render: function(data, type, row) {
                return '<strong><a href="{{ url("customer/ledger/statement") }}/' + row.id + '">' + data + '</a></strong>';
            }},
            { data: 'mobile', name: 'mobile', defaultContent: '-' },
            { data: 'cnic', name: 'cnic', defaultContent: '-' },
            { data: 'total_billed', name: 'total_billed', orderable: false, searchable: false },
            { data: 'total_paid', name: 'total_paid', orderable: false, searchable: false },
            { data: 'balance', name: 'balance', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']]
    });

    $('#collect_payment_form').on('submit', function(e) {
        e.preventDefault();
        var customerId = $('#modal_customer_id').val();
        if (!customerId) return;

        $('#btn_submit_payment').prop('disabled', true).text('Processing...');

        $.ajax({
            url: "{{ url('customer/ledger/payment') }}/" + customerId,
            type: "POST",
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                $('#collectPaymentModal').modal('hide');
                $('#btn_submit_payment').prop('disabled', false).html('<i class="fa fa-check"></i> Record & Generate Voucher');
                customerTable.ajax.reload(null, false);

                if (res.voucher_url) {
                    window.open(res.voucher_url, '_blank');
                }

                $.confirm({
                    title: 'Payment Successful',
                    content: res.message,
                    type: 'green',
                    buttons: {
                        ok: {
                            text: 'OK',
                            btnClass: 'btn-green'
                        }
                    }
                });
            },
            error: function(xhr) {
                $('#btn_submit_payment').prop('disabled', false).html('<i class="fa fa-check"></i> Record & Generate Voucher');
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to record payment.';
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
            }
        });
    });
});

function openCustomerPaymentModal(id, name, balance) {
    $('#modal_customer_id').val(id);
    $('#modal_customer_name').text(name);
    $('#modal_customer_balance').text('PKR ' + parseFloat(balance).toLocaleString('en-US', { minimumFractionDigits: 2 }));
    if (balance > 0) {
        $('#payment_amount').val(balance);
    } else {
        $('#payment_amount').val('');
    }
    $('#collectPaymentModal').modal('show');
}
</script>
@endsection
