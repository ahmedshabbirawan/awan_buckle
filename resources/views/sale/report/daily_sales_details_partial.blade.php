<div class="modal-header" style="background: #2b3d51; color: #fff; padding: 12px 20px;">
    <button type="button" class="close" data-dismiss="modal" style="color: #fff; opacity: 0.8; font-size: 24px;">&times;</button>
    <h4 class="modal-title" style="margin: 0; font-weight: 600;">
        <i class="fa fa-calendar-check-o text-warning"></i> 
        Reconciliation Details &mdash; <span class="text-white">{{ $formattedDate }}</span>
    </h4>
</div>

<div class="modal-body" style="padding: 20px; background: #fdfdfd;">
    <!-- Day Quick Summary Badges -->
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-xs-6 col-sm-3">
            <div style="background: #f0f7ff; border-left: 4px solid #337ab7; padding: 10px; border-radius: 4px;">
                <span style="font-size: 11px; text-transform: uppercase; color: #666; font-weight: bold;">Sales Invoices</span>
                <div style="font-size: 18px; font-weight: bold; color: #337ab7;">
                    {{ count($orders) }} <small style="font-size: 12px; color: #555;">(Rs {{ number_format($orders->sum('total_amount'), 2) }})</small>
                </div>
            </div>
        </div>
        <div class="col-xs-6 col-sm-3">
            <div style="background: #f0fbf0; border-left: 4px solid #5cb85c; padding: 10px; border-radius: 4px;">
                <span style="font-size: 11px; text-transform: uppercase; color: #666; font-weight: bold;">Receipts Collected</span>
                <div style="font-size: 18px; font-weight: bold; color: #5cb85c;">
                    {{ count($receipts) }} <small style="font-size: 12px; color: #555;">(Rs {{ number_format($receipts->sum('amount'), 2) }})</small>
                </div>
            </div>
        </div>
        <div class="col-xs-6 col-sm-3">
            <div style="background: #fff8f0; border-left: 4px solid #f0ad4e; padding: 10px; border-radius: 4px;">
                <span style="font-size: 11px; text-transform: uppercase; color: #666; font-weight: bold;">Credit Billed</span>
                <div style="font-size: 18px; font-weight: bold; color: #e67e22;">
                    Rs {{ number_format($orders->sum('due_amount'), 2) }}
                </div>
            </div>
        </div>
        <div class="col-xs-6 col-sm-3">
            <div style="background: #fcf0f0; border-left: 4px solid #d9534f; padding: 10px; border-radius: 4px;">
                <span style="font-size: 11px; text-transform: uppercase; color: #666; font-weight: bold;">Sales Returns</span>
                <div style="font-size: 18px; font-weight: bold; color: #d9534f;">
                    {{ count($returns) }} items
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="tabbable">
        <ul class="nav nav-tabs padding-12 tab-color-blue background-blue" id="detailsTab">
            <li class="active">
                <a data-toggle="tab" href="#tab-sales">
                    <i class="ace-icon fa fa-shopping-cart bigger-110"></i>
                    Sales Transactions ({{ count($orders) }})
                </a>
            </li>
            <li>
                <a data-toggle="tab" href="#tab-receipts">
                    <i class="ace-icon fa fa-money bigger-110"></i>
                    Customer Collections ({{ count($receipts) }})
                </a>
            </li>
            <li>
                <a data-toggle="tab" href="#tab-returns">
                    <i class="ace-icon fa fa-undo bigger-110"></i>
                    Sales Returns ({{ count($returns) }})
                </a>
            </li>
            <li>
                <a data-toggle="tab" href="#tab-stock">
                    <i class="ace-icon fa fa-cubes bigger-110"></i>
                    Stock Movement ({{ count($stockMovements) }})
                </a>
            </li>
        </ul>

        <div class="tab-content" style="border: 1px solid #c5d0dc; padding: 16px 12px; border-top: none;">
            <!-- Tab 1: Sales Transactions -->
            <div id="tab-sales" class="tab-pane in active">
                @if(count($orders) > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead style="background: #f5f5f5;">
                            <tr>
                                <th>#</th>
                                <th>Sale Key / Invoice</th>
                                <th>Time</th>
                                <th>Customer</th>
                                <th>Shop</th>
                                <th>Payment Method</th>
                                <th class="text-right">Total (Rs)</th>
                                <th class="text-right">Paid (Rs)</th>
                                <th class="text-right">Due / Credit (Rs)</th>
                                <th class="text-center">Items</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $idx => $order)
                            @php
                                $methodBadge = 'badge-default';
                                if($order->payment_method == 'cash') $methodBadge = 'badge-success';
                                elseif($order->payment_method == 'bank_transfer') $methodBadge = 'badge-info';
                                elseif($order->payment_method == 'credit') $methodBadge = 'badge-warning';
                                elseif($order->payment_method == 'split') $methodBadge = 'badge-primary';

                                $statusBadge = 'badge-success';
                                if($order->payment_status == 'due') $statusBadge = 'badge-danger';
                                elseif($order->payment_status == 'partial') $statusBadge = 'badge-warning';
                            @endphp
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    <a href="{{ route('sale.order.detail', $order->id) }}" target="_blank" style="font-weight: bold;">
                                        <i class="fa fa-external-link"></i> {{ $order->sale_key }}
                                    </a>
                                </td>
                                <td>{{ $order->created_at ? $order->created_at->format('h:i A') : '--' }}</td>
                                <td>
                                    @if($order->customer)
                                        <span class="label label-info arrowed-right arrowed-in">{{ $order->customer->name }}</span>
                                    @else
                                        <span class="label label-default">Walking Customer</span>
                                    @endif
                                </td>
                                <td>{{ $order->shop ? $order->shop->name : '--' }}</td>
                                <td>
                                    <span class="badge {{ $methodBadge }}" style="text-transform: uppercase;">
                                        {{ str_replace('_', ' ', $order->payment_method ?? 'cash') }}
                                    </span>
                                </td>
                                <td class="text-right"><strong>{{ number_format($order->total_amount, 2) }}</strong></td>
                                <td class="text-right text-success">{{ number_format($order->paid_amount, 2) }}</td>
                                <td class="text-right {{ $order->due_amount > 0 ? 'text-danger font-bold' : 'text-muted' }}">
                                    {{ number_format($order->due_amount, 2) }}
                                </td>
                                <td class="text-center"><span class="badge badge-grey">{{ $order->item_count }}</span></td>
                                <td class="text-center">
                                    <span class="badge {{ $statusBadge }}" style="text-transform: capitalize;">{{ $order->payment_status }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('sale.order.detail', $order->id) }}" target="_blank" class="btn btn-xs btn-info" title="View Order Details">
                                        <i class="fa fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background: #e9ecef; font-weight: bold;">
                            <tr>
                                <td colspan="6" class="text-right">Grand Total:</td>
                                <td class="text-right">Rs {{ number_format($orders->sum('total_amount'), 2) }}</td>
                                <td class="text-right text-success">Rs {{ number_format($orders->sum('paid_amount'), 2) }}</td>
                                <td class="text-right text-danger">Rs {{ number_format($orders->sum('due_amount'), 2) }}</td>
                                <td class="text-center">{{ $orders->sum('item_count') }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="alert alert-warning" style="margin: 10px 0;">
                    <i class="fa fa-info-circle"></i> No sales transactions recorded on this day.
                </div>
                @endif
            </div>

            <!-- Tab 2: Customer Collections (Receipts) -->
            <div id="tab-receipts" class="tab-pane">
                @if(count($receipts) > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead style="background: #f5f5f5;">
                            <tr>
                                <th>#</th>
                                <th>Voucher #</th>
                                <th>Time</th>
                                <th>Customer Name</th>
                                <th>Payment Method</th>
                                <th class="text-right">Amount Received (Rs)</th>
                                <th>Reference No / Memo</th>
                                <th>Shop</th>
                                <th>Recorded By</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receipts as $idx => $receipt)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    <a href="{{ route('customer.ledger.voucher', $receipt->id) }}" target="_blank" style="font-weight: bold;">
                                        <i class="fa fa-file-text-o"></i> {{ $receipt->voucher_no }}
                                    </a>
                                </td>
                                <td>{{ $receipt->created_at ? $receipt->created_at->format('h:i A') : '--' }}</td>
                                <td>
                                    <strong>{{ $receipt->customer ? $receipt->customer->name : 'Customer #' . $receipt->party_id }}</strong>
                                </td>
                                <td>
                                    <span class="badge {{ $receipt->payment_method == 'cash' ? 'badge-success' : 'badge-info' }}" style="text-transform: uppercase;">
                                        {{ str_replace('_', ' ', $receipt->payment_method) }}
                                    </span>
                                </td>
                                <td class="text-right text-success" style="font-weight: bold; font-size: 14px;">
                                    Rs {{ number_format($receipt->amount, 2) }}
                                </td>
                                <td>{{ $receipt->reference_no ?: ($receipt->notes ?: '--') }}</td>
                                <td>{{ $receipt->shop ? $receipt->shop->name : '--' }}</td>
                                <td>{{ $receipt->creator ? $receipt->creator->name : '--' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('customer.ledger.voucher', $receipt->id) }}" target="_blank" class="btn btn-xs btn-primary" title="Print / View Voucher">
                                        <i class="fa fa-print"></i> Voucher
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background: #e9ecef; font-weight: bold;">
                            <tr>
                                <td colspan="5" class="text-right">Total Collections Received:</td>
                                <td class="text-right text-success" style="font-size: 15px;">
                                    Rs {{ number_format($receipts->sum('amount'), 2) }}
                                </td>
                                <td colspan="4"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="alert alert-info" style="margin: 10px 0;">
                    <i class="fa fa-info-circle"></i> No customer receipt vouchers or debt collections recorded on this day.
                </div>
                @endif
            </div>

            <!-- Tab 3: Sales Returns -->
            <div id="tab-returns" class="tab-pane">
                @if(count($returns) > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead style="background: #f5f5f5;">
                            <tr>
                                <th>#</th>
                                <th>Order Ref #</th>
                                <th>Product Name</th>
                                <th>Time</th>
                                <th class="text-center">Returned Qty</th>
                                <th class="text-right">Unit Price (Rs)</th>
                                <th class="text-right">Total Return Amount (Rs)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalReturnSum = 0; $totalReturnQty = 0; @endphp
                            @foreach($returns as $idx => $ret)
                            @php
                                $unitPrice = $ret->product ? (float)$ret->product->price : 0;
                                $rowTotal = $ret->qty * $unitPrice;
                                $totalReturnSum += $rowTotal;
                                $totalReturnQty += $ret->qty;
                            @endphp
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    <a href="{{ route('sale.order.detail', $ret->order_id) }}" target="_blank">
                                        #{{ $ret->order_id }}
                                    </a>
                                </td>
                                <td><strong>{{ $ret->product ? $ret->product->name : 'Product #' . $ret->product_id }}</strong></td>
                                <td>{{ $ret->created_at ? $ret->created_at->format('h:i A') : '--' }}</td>
                                <td class="text-center"><span class="badge badge-danger">{{ $ret->qty }}</span></td>
                                <td class="text-right">{{ number_format($unitPrice, 2) }}</td>
                                <td class="text-right text-danger" style="font-weight: bold;">{{ number_format($rowTotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background: #e9ecef; font-weight: bold;">
                            <tr>
                                <td colspan="4" class="text-right">Total Returns:</td>
                                <td class="text-center text-danger">{{ $totalReturnQty }} units</td>
                                <td></td>
                                <td class="text-right text-danger">Rs {{ number_format($totalReturnSum, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="alert alert-success" style="margin: 10px 0;">
                    <i class="fa fa-check-circle"></i> No sales returns recorded on this day.
                </div>
                @endif
            </div>

            <!-- Tab 4: Stock Movement -->
            <div id="tab-stock" class="tab-pane">
                @if(count($stockMovements) > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead style="background: #f5f5f5;">
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th class="text-center">Units Sold</th>
                                <th class="text-center">Units Returned</th>
                                <th class="text-center">Net Units Removed From Stock</th>
                                <th class="text-right">Gross Revenue (Rs)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totSold = 0; $totRet = 0; $totRev = 0; @endphp
                            @foreach($stockMovements as $idx => $sm)
                            @php 
                                $totSold += $sm['qty_sold'];
                                $totRet += $sm['qty_returned'];
                                $totRev += $sm['revenue'];
                            @endphp
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td><strong>{{ $sm['product_name'] }}</strong></td>
                                <td class="text-center"><span class="badge badge-primary">{{ $sm['qty_sold'] }}</span></td>
                                <td class="text-center"><span class="badge badge-warning">{{ $sm['qty_returned'] }}</span></td>
                                <td class="text-center">
                                    <span class="badge badge-info" style="font-weight: bold;">
                                        {{ $sm['net_movement'] }}
                                    </span>
                                </td>
                                <td class="text-right" style="font-weight: bold;">{{ number_format($sm['revenue'], 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background: #e9ecef; font-weight: bold;">
                            <tr>
                                <td colspan="2" class="text-right">Total Stock Movement:</td>
                                <td class="text-center">{{ $totSold }} units</td>
                                <td class="text-center text-danger">{{ $totRet }} units</td>
                                <td class="text-center text-primary" style="font-size: 15px;">{{ $totSold - $totRet }} units</td>
                                <td class="text-right">Rs {{ number_format($totRev, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="alert alert-info" style="margin: 10px 0;">
                    <i class="fa fa-info-circle"></i> No stock movement recorded on this day.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal-footer" style="padding: 12px 20px; background: #f5f5f5;">
    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">
        <i class="fa fa-times"></i> Close
    </button>
</div>
