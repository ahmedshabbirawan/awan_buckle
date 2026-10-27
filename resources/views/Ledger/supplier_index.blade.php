@extends('Layout.master')

@section('title')
Supplier Ledgers (Accounts Payable)
@endsection

@section('content')
<div class="page-content">
    <div class="page-header">
        <h1>
            <i class="ace-icon fa fa-truck blue"></i> Supplier Ledgers
            <small>
                <i class="ace-icon fa fa-angle-double-right"></i>
                Accounts Payable & Vendor Liability Directory
            </small>
        </h1>
    </div>

    <!-- Summary Metrics Cards -->
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-xs-12 col-sm-3">
            <div class="infobox infobox-blue infobox-large infobox-dark" style="width:100%; border-radius: 6px;">
                <div class="infobox-icon">
                    <i class="ace-icon fa fa-truck"></i>
                </div>
                <div class="infobox-data">
                    <div class="infobox-content" style="font-size: 13px;">Total Suppliers</div>
                    <div class="infobox-content" style="font-size: 22px; font-weight: bold;">{{ number_format($supplierCount) }}</div>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-3">
            <div class="infobox infobox-purple infobox-large infobox-dark" style="width:100%; border-radius: 6px;">
                <div class="infobox-icon">
                    <i class="ace-icon fa fa-shopping-basket"></i>
                </div>
                <div class="infobox-data">
                    <div class="infobox-content" style="font-size: 13px;">Total Purchased / Invoiced</div>
                    <div class="infobox-content" style="font-size: 20px; font-weight: bold;">PKR {{ number_format($totalPurchased, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-3">
            <div class="infobox infobox-green infobox-large infobox-dark" style="width:100%; border-radius: 6px;">
                <div class="infobox-icon">
                    <i class="ace-icon fa fa-money"></i>
                </div>
                <div class="infobox-data">
                    <div class="infobox-content" style="font-size: 13px;">Total Paid (Cash / Bank)</div>
                    <div class="infobox-content" style="font-size: 20px; font-weight: bold;">PKR {{ number_format($totalPaid, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-3">
            <div class="infobox infobox-red infobox-large infobox-dark" style="width:100%; border-radius: 6px;">
                <div class="infobox-icon">
                    <i class="ace-icon fa fa-exclamation-triangle"></i>
                </div>
                <div class="infobox-data">
                    <div class="infobox-content" style="font-size: 13px;">Total Outstanding (Payable)</div>
                    <div class="infobox-content" style="font-size: 20px; font-weight: bold;">PKR {{ number_format($totalPayable, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Supplier Ledger Table -->
    <div class="row">
        <div class="col-xs-12">
            <div class="widget-box widget-color-blue2">
                <div class="widget-header">
                    <h5 class="widget-title bigger lighter">
                        <i class="ace-icon fa fa-table"></i> Supplier Accounts Payable List
                    </h5>
                    <div class="widget-toolbar">
                        <a href="{{ route('supplier.create') }}" class="btn btn-xs btn-success">
                            <i class="ace-icon fa fa-plus"></i> Add New Supplier
                        </a>
                    </div>
                </div>

                <div class="widget-body">
                    <div class="widget-main no-padding">
                        <div class="table-responsive" style="padding: 10px;">
                            <table class="table table-striped table-bordered table-hover" id="supplier_ledger_table" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th style="width:5%;">#</th>
                                        <th>Supplier Name</th>
                                        <th>Phone / Contact</th>
                                        <th>Code</th>
                                        <th>Total Purchased (Cr)</th>
                                        <th>Total Paid (Dr)</th>
                                        <th>Current Balance (Payable)</th>
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

<!-- Pay Supplier / Record Payment Modal -->
<div class="modal fade" id="supplierPaymentModal" tabindex="-1" role="dialog" aria-labelledby="supplierPaymentModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="supplier_payment_form" method="POST" action="javascript:void(0);">
                @csrf
                <input type="hidden" id="modal_supplier_id" name="supplier_id" value="">
                
                <div class="modal-header bg-warning">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="supplierPaymentModalLabel" style="color:#333;">
                        <i class="fa fa-credit-card"></i> Record Supplier Payment / Payout Voucher
                    </h4>
                </div>

                <div class="modal-body">
                    <div class="alert alert-warning" id="modal_supplier_info" style="margin-bottom: 15px;">
                        <strong>Supplier:</strong> <span id="modal_supplier_name">-</span><br>
                        <strong>Current Outstanding Liability:</strong> <span id="modal_supplier_balance" style="font-weight: bold; color: #c53030;">PKR 0.00</span>
                    </div>

                    <div class="form-group">
                        <label for="supplier_payment_amount"><strong>Amount Paid (PKR) <span class="text-danger">*</span></strong></label>
                        <input type="number" step="0.01" min="0.01" class="form-control" id="supplier_payment_amount" name="amount" placeholder="Enter payout amount" required>
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
                        <input type="text" class="form-control" id="supplier_payment_reference" name="reference_no" placeholder="Optional bank slip or check number">
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
var supplierTable;

$(document).ready(function() {
    supplierTable = $('#supplier_ledger_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('supplier.ledger.index') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name', render: function(data, type, row) {
                return '<strong><a href="{{ url("supplier/ledger/statement") }}/' + row.id + '">' + data + '</a></strong>';
            }},
            { data: 'phone', name: 'phone', defaultContent: '-' },
            { data: 'code', name: 'code', defaultContent: '-' },
            { data: 'total_purchased', name: 'total_purchased', orderable: false, searchable: false },
            { data: 'total_paid', name: 'total_paid', orderable: false, searchable: false },
            { data: 'balance', name: 'balance', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']]
    });

    $('#supplier_payment_form').on('submit', function(e) {
        e.preventDefault();
        var supplierId = $('#modal_supplier_id').val();
        if (!supplierId) return;

        $('#btn_submit_supplier_payment').prop('disabled', true).text('Processing...');

        $.ajax({
            url: "{{ url('supplier/ledger/payment') }}/" + supplierId,
            type: "POST",
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                $('#supplierPaymentModal').modal('hide');
                $('#btn_submit_supplier_payment').prop('disabled', false).html('<i class="fa fa-check"></i> Record Payout & Generate Voucher');
                supplierTable.ajax.reload(null, false);

                if (res.voucher_url) {
                    window.open(res.voucher_url, '_blank');
                }

                $.confirm({
                    title: 'Payout Recorded',
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
                $('#btn_submit_supplier_payment').prop('disabled', false).html('<i class="fa fa-check"></i> Record Payout & Generate Voucher');
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to record payout.';
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

function openSupplierPaymentModal(id, name, balance) {
    $('#modal_supplier_id').val(id);
    $('#modal_supplier_name').text(name);
    $('#modal_supplier_balance').text('PKR ' + parseFloat(balance).toLocaleString('en-US', { minimumFractionDigits: 2 }));
    if (balance > 0) {
        $('#supplier_payment_amount').val(balance);
    } else {
        $('#supplier_payment_amount').val('');
    }
    $('#supplierPaymentModal').modal('show');
}
</script>
@endsection
