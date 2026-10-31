@extends('Layout.master')

@section('title')
Order Detail
@endsection

@section('content')

<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px;">
        @include('Layout.alerts')

        <div class="card radius-10 border-top border-0 border-4 border-danger">
            <div class="card-header" style="background-color: #f8f9fa; padding: 12px 20px; border-bottom: 1px solid #e5e5e5;">
                <div class="row" style="align-items: center;">
                    <div class="col-sm-6">
                        <h4 style="margin: 0; font-weight: bold; color: #333;">
                            <i class="fa fa-file-text-o text-primary"></i> Order #{{ $order->id }}
                            <small class="text-muted">(Sale Key: {{ $order->sale_key }})</small>
                        </h4>
                    </div>
                    <div class="col-sm-6 text-right">
                        <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editTransportModal">
                            <i class="fa fa-pencil"></i> Quick Edit Transport
                        </button>
                        <a href="{{ route('sale.order.edit', $order->id) }}" class="btn btn-sm btn-default">
                            <i class="fa fa-edit"></i> Full Edit
                        </a>
                        <a href="{{ route('sale.order.detail', $order->id) }}?view=print" target="_blank" class="btn btn-sm btn-primary">
                            <i class="fa fa-print"></i> Print Invoice
                        </a>
                        <a href="{{ route('sale.order.list') }}" class="btn btn-sm btn-light">
                            <i class="fa fa-arrow-left"></i> Back to Orders
                        </a>
                    </div>
                </div>

                <!-- Transport badges strip -->
                <div style="margin-top: 10px; padding: 8px 12px; background: #fff; border: 1px solid #e0e0e0; border-radius: 4px; display: flex; gap: 20px; flex-wrap: wrap; align-items: center;">
                    <div>
                        <span class="text-muted" style="font-size: 11px; text-transform: uppercase; font-weight: bold;">Vehicle Number:</span>
                        <span class="label label-lg {{ $order->vehicle_no ? 'label-info' : 'label-default' }}" id="detail_vehicle_display" style="font-size: 12px; margin-left: 5px;">
                            <i class="fa fa-truck"></i> {{ $order->vehicle_no ?: 'None / Not Assigned' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-muted" style="font-size: 11px; text-transform: uppercase; font-weight: bold;">Bilty Number:</span>
                        <span class="label label-lg {{ $order->bilty_no ? 'label-primary' : 'label-default' }}" id="detail_bilty_display" style="font-size: 12px; margin-left: 5px;">
                            <i class="fa fa-file-text-o"></i> {{ $order->bilty_no ?: 'None / Not Assigned' }}
                        </span>
                    </div>
                    <div style="margin-left: auto;">
                        <a href="javascript:void(0);" data-toggle="modal" data-target="#editTransportModal" style="font-size: 12px;">
                            <i class="fa fa-pencil"></i> Edit Vehicle / Bilty
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                @include('order.order_detail_table',['hideInfo' => true])
            </div>

            <div class="card-footer" style="padding: 15px; background: #fafafa; display: flex; gap: 10px; justify-content: space-between;">
                <div>
                    <a href="{{ route('sale.order.list') }}" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> Back to List
                    </a>
                </div>
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('sale.order.sale_return_view', $order->id) }}" class="btn btn-primary">
                        <i class="fa fa-undo"></i> Sale Return
                    </a>
                    <a href="{{ route('sale.order.edit', $order->id) }}" class="btn btn-warning">
                        <i class="fa fa-pencil"></i> Edit Order
                    </a>
                    <a href="{{ route('sale.order.detail', $order->id) }}?view=print" target="_blank" class="btn btn-success">
                        <i class="fa fa-print"></i> Print Invoice
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Edit Transport Modal -->
<div class="modal fade" id="editTransportModal" tabindex="-1" role="dialog" aria-labelledby="editTransportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('sale.order.update', $order->id) }}" id="quick_edit_transport_form">
                @csrf
                <div class="modal-header" style="background-color: #307ECC; color: #fff;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff; opacity: 0.85;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="editTransportModalLabel" style="font-weight: 600; margin: 0;">
                        <i class="fa fa-truck"></i> Edit Order #{{ $order->id }} - Transport Details
                    </h4>
                </div>
                <div class="modal-body" style="padding: 20px;">
                    <div id="transport_edit_alert" style="display: none;"></div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label for="modal_edit_vehicle_no" style="font-weight: bold;">
                            <i class="fa fa-truck text-primary"></i> Vehicle Number:
                        </label>
                        <input type="text" class="form-control input-lg" id="modal_edit_vehicle_no" name="vehicle_no" value="{{ old('vehicle_no', $order->vehicle_no) }}" placeholder="e.g. LES-1234 / Truck 5">
                        <span class="help-block" style="font-size: 11px; color: #888; margin-bottom: 0;">Enter the vehicle registration or carrier identifier.</span>
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label for="modal_edit_bilty_no" style="font-weight: bold;">
                            <i class="fa fa-file-text-o text-primary"></i> Bilty Number:
                        </label>
                        <input type="text" class="form-control input-lg" id="modal_edit_bilty_no" name="bilty_no" value="{{ old('bilty_no', $order->bilty_no) }}" placeholder="e.g. BL-98765">
                        <span class="help-block" style="font-size: 11px; color: #888; margin-bottom: 0;">Enter the goods transport bilty / consignment tracking number.</span>
                    </div>
                </div>
                <div class="modal-footer" style="background: #f7f7f7;">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="btn_save_transport">
                        <i class="fa fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('script')
<script>
$(document).ready(function() {
    $('#quick_edit_transport_form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = $('#btn_save_transport');
        btn.attr('disabled', 'disabled').html('<i class="fa fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                btn.removeAttr('disabled').html('<i class="fa fa-save"></i> Save Changes');
                if (res.status) {
                    $('#editTransportModal').modal('hide');
                    // Update badges live
                    var v = res.data.vehicle_no || 'None / Not Assigned';
                    var b = res.data.bilty_no || 'None / Not Assigned';
                    $('#detail_vehicle_display').html('<i class="fa fa-truck"></i> ' + v);
                    $('#detail_bilty_display').html('<i class="fa fa-file-text-o"></i> ' + b);
                    if (res.data.vehicle_no) {
                        $('#detail_vehicle_display').removeClass('label-default').addClass('label-info');
                    } else {
                        $('#detail_vehicle_display').removeClass('label-info').addClass('label-default');
                    }
                    if (res.data.bilty_no) {
                        $('#detail_bilty_display').removeClass('label-default').addClass('label-primary');
                    } else {
                        $('#detail_bilty_display').removeClass('label-primary').addClass('label-default');
                    }
                    // Reload page to reflect printed invoice table as well
                    window.location.reload();
                }
            },
            error: function(jqXHR) {
                btn.removeAttr('disabled').html('<i class="fa fa-save"></i> Save Changes');
                var msg = 'Failed to update order.';
                if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                    msg = jqXHR.responseJSON.message;
                }
                $('#transport_edit_alert').html('<div class="alert alert-danger">'+msg+'</div>').show();
            }
        });
    });
});
</script>
@endsection