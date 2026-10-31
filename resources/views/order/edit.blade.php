@extends('Layout.master')

@section('title')
Edit Order #{{ $order->id }}
@endsection

@section('content')

<div class="page-content">
    <div class="page-header" style="min-height: 40px;">
        <div style="float: left;">
            <h1>
                <i class="ace-icon fa fa-pencil-square-o"></i> Edit Order #{{ $order->id }}
                <small>
                    <i class="ace-icon fa fa-angle-double-right"></i>
                    Sale Key: {{ $order->sale_key }}
                </small>
            </h1>
        </div>
        <div style="float: right;">
            <a href="{{ route('sale.order.detail', $order->id) }}" class="btn btn-xs btn-primary">
                <i class="ace-icon fa fa-eye"></i> View Order
            </a>
            <a href="{{ route('sale.order.list') }}" class="btn btn-xs btn-light">
                <i class="ace-icon fa fa-list"></i> Order List
            </a>
        </div>
    </div>

    @include('Layout.alerts')

    <div class="row">
        <!-- Edit Form -->
        <div class="col-xs-12 col-md-7">
            <div class="widget-box widget-color-blue2">
                <div class="widget-header">
                    <h5 class="widget-title bigger lighter">
                        <i class="ace-icon fa fa-truck"></i> Transport &amp; Delivery Information
                    </h5>
                </div>
                <div class="widget-body">
                    <div class="widget-main">
                        <form method="POST" action="{{ route('sale.order.update', $order->id) }}" class="form-horizontal" id="order_edit_form">
                            @csrf

                            <div class="alert alert-info">
                                <i class="ace-icon fa fa-info-circle"></i>
                                Update the transport details for this order. Line items and historical ledger balances remain protected and immutable.
                            </div>

                            <div class="form-group @error('vehicle_no') has-error @enderror">
                                <label class="col-sm-4 control-label no-padding-right" for="vehicle_no" style="font-weight: bold;">
                                    <i class="fa fa-truck text-primary"></i> Vehicle Number
                                </label>
                                <div class="col-sm-8">
                                    <div class="input-group col-xs-12">
                                        <span class="input-group-addon"><i class="fa fa-truck"></i></span>
                                        <input type="text" name="vehicle_no" id="vehicle_no" class="form-control" 
                                               value="{{ old('vehicle_no', $order->vehicle_no) }}" 
                                               placeholder="e.g. LES-1234, Truck 5, Rickshaw">
                                    </div>
                                    @error('vehicle_no')
                                        <div class="help-block col-xs-12 col-sm-reset inline">{{ $message }}</div>
                                    @enderror
                                    <span class="help-block" style="font-size: 11px; color: #888; margin-bottom: 0;">Vehicle or transport carrier registration number.</span>
                                </div>
                            </div>

                            <div class="form-group @error('bilty_no') has-error @enderror">
                                <label class="col-sm-4 control-label no-padding-right" for="bilty_no" style="font-weight: bold;">
                                    <i class="fa fa-file-text-o text-primary"></i> Bilty Number
                                </label>
                                <div class="col-sm-8">
                                    <div class="input-group col-xs-12">
                                        <span class="input-group-addon"><i class="fa fa-file-text-o"></i></span>
                                        <input type="text" name="bilty_no" id="bilty_no" class="form-control" 
                                               value="{{ old('bilty_no', $order->bilty_no) }}" 
                                               placeholder="e.g. BL-98765, G-4432">
                                    </div>
                                    @error('bilty_no')
                                        <div class="help-block col-xs-12 col-sm-reset inline">{{ $message }}</div>
                                    @enderror
                                    <span class="help-block" style="font-size: 11px; color: #888; margin-bottom: 0;">Goods transport receipt / tracking bilty number.</span>
                                </div>
                            </div>

                            <div class="clearfix form-actions" style="margin-bottom: 0; background: #f9f9f9;">
                                <div class="col-md-offset-4 col-md-8">
                                    <button class="btn btn-success" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        Save Changes
                                    </button>
                                    &nbsp; &nbsp; &nbsp;
                                    <a href="{{ route('sale.order.detail', $order->id) }}" class="btn btn-default">
                                        <i class="ace-icon fa fa-times bigger-110"></i>
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary Card -->
        <div class="col-xs-12 col-md-5">
            <div class="widget-box widget-color-grey">
                <div class="widget-header">
                    <h5 class="widget-title bigger lighter">
                        <i class="ace-icon fa fa-info-circle"></i> Order Details &amp; Summary
                    </h5>
                </div>
                <div class="widget-body">
                    <div class="widget-main no-padding">
                        <table class="table table-striped table-bordered">
                            <tbody>
                                <tr>
                                    <td style="width: 40%; font-weight: bold;">Bill / Order No:</td>
                                    <td><span class="badge badge-info">#{{ $order->id }}</span></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">Sale Key:</td>
                                    <td>{{ $order->sale_key }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">Order Date:</td>
                                    <td>{{ $order->created_at->format('d-m-Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">Customer:</td>
                                    <td>
                                        @if($customer)
                                            <strong>{{ $customer->name }}</strong><br>
                                            <small class="text-muted"><i class="fa fa-phone"></i> {{ $customer->mobile }}</small>
                                        @else
                                            <span class="label label-default">Walking Customer</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">Branch / Shop:</td>
                                    <td>{{ optional($shop)->name ?? 'Main Branch' }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">Manager / Cashier:</td>
                                    <td>{{ optional($manager)->name ?? 'Admin' }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">Total Items:</td>
                                    <td>{{ $order->item_count }} item(s)</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">Grand Total:</td>
                                    <td><strong>Rs. {{ number_format($order->total_amount ?: $order->total_price, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">Payment Method:</td>
                                    <td>
                                        <span class="label label-info">{{ strtoupper($order->payment_method ?: 'cash') }}</span>
                                        <span class="label {{ $order->payment_status === 'paid' ? 'label-success' : ($order->payment_status === 'partial' ? 'label-warning' : 'label-danger') }}">
                                            {{ strtoupper($order->payment_status ?: 'paid') }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">Amount Paid:</td>
                                    <td style="color: #2e8965; font-weight: bold;">Rs. {{ number_format($order->paid_amount, 2) }}</td>
                                </tr>
                                @if($order->due_amount > 0)
                                <tr>
                                    <td style="font-weight: bold; color: #d15b47;">Due Balance:</td>
                                    <td style="color: #d15b47; font-weight: bold;">Rs. {{ number_format($order->due_amount, 2) }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Line Items Table -->
    <div class="row" style="margin-top: 20px;">
        <div class="col-xs-12">
            <div class="widget-box widget-color-dark">
                <div class="widget-header">
                    <h5 class="widget-title bigger lighter">
                        <i class="ace-icon fa fa-list"></i> Ordered Items (Read-Only)
                    </h5>
                </div>
                <div class="widget-body">
                    <div class="widget-main no-padding">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Product Name</th>
                                    <th style="text-align: right;">Unit Price</th>
                                    <th style="text-align: right;">Discount</th>
                                    @if($order->total_commission > 0)
                                    <th style="text-align: right;">Commission</th>
                                    @endif
                                    <th style="text-align: center;">Qty</th>
                                    <th style="text-align: right;">Line Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orderItem as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ optional($item->product)->name ?? 'Product #'.$item->product_id }}</strong></td>
                                    <td style="text-align: right;">Rs. {{ number_format($item->price, 2) }}</td>
                                    <td style="text-align: right;">Rs. {{ number_format($item->discount, 2) }}</td>
                                    @if($order->total_commission > 0)
                                    <td style="text-align: right;">
                                        @if($item->is_commission_applied && $item->commission_amount > 0)
                                            {{ $item->commission_percentage }}% (-{{ number_format($item->commission_amount, 2) }})
                                        @else
                                            -
                                        @endif
                                    </td>
                                    @endif
                                    <td style="text-align: center;"><strong>{{ $item->qty }}</strong></td>
                                    <td style="text-align: right;">
                                        <strong>Rs. {{ number_format(round(($item->offer_price - $item->discount) * $item->qty - ($item->is_commission_applied ? $item->commission_amount : 0)), 2) }}</strong>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
