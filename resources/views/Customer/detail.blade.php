@extends('Layout.master')

@section('title')
Supplier Detail
@endsection

@section('content')
<style>
    .profile-info-name {
        width: 190px;
    }

    .profile-info-value span {
        font-weight: bold;
    }
</style>




<div class="page-content">

    <div class="page-header" style="min-height:40px;">
        <div class="" style="float: left;">
            <h1>Customer Detail</h1>
        </div>
    </div>

    <div class="row ">







        <div class="col-xs-12">

            <div class="table-detail">
                <div class="row">
                    <div class="col-xs-12 col-sm-2">
                        <div class="text-center">
                            <img height="150" class="thumbnail inline no-margin-bottom media-object" alt="{{ $row->name }}" data-src="holder.js/72x72" alt="721x72" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%2272%22%20height%3D%2272%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2072%2072%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_19128bb28e3%20text%20%7B%20fill%3A%23AAAAAA%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A10pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_19128bb28e3%22%3E%3Crect%20width%3D%2272%22%20height%3D%2272%22%20fill%3D%22%23EEEEEE%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2217.46875%22%20y%3D%2240.5%22%3E72x72%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E" data-holder-rendered="true">
                            <br>
                            <div class="width-80 label label-info label-xlg arrowed-in arrowed-in-right">
                                <div class="inline position-relative">
                                    <a class="user-title-label" href="#">
                                        <i class="ace-icon fa fa-circle light-green"></i>
                                        &nbsp;
                                        <span class="white">{{ $row->name }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-7">
                        <div class="space visible-xs"></div>

                        <div class="profile-user-info profile-user-info-striped">
                            <div class="profile-info-row">
                                <div class="profile-info-name"> Name </div>

                                <div class="profile-info-value">
                                    <span>{{ $row->name }}</span>
                                </div>
                            </div>
                            <div class="profile-info-row">
                                <div class="profile-info-name"> Id </div>

                                <div class="profile-info-value">
                                    <span>{{ $row->id }}</span>
                                </div>
                            </div>

                            <div class="profile-info-row">
                                <div class="profile-info-name"> Address </div>

                                <div class="profile-info-value">
                                    <i class="fa fa-map-marker light-orange bigger-110"></i>
                                    <span>{{ $row->address }}</span>
                                </div>
                            </div>

                            <div class="profile-info-row">
                                <div class="profile-info-name"> Bazaar </div>

                                <div class="profile-info-value">
                                    <i class="fa fa-map-marker light-orange bigger-110"></i>
                                    <span>{{ $row->bazaar }}</span>
                                </div>
                            </div>

                            <div class="profile-info-row">
                                <div class="profile-info-name"> City </div>

                                <div class="profile-info-value">
                                    <span>{{ $row->city }}</span>
                                </div>
                            </div>

                            <div class="profile-info-row">
                                <div class="profile-info-name"> CNIC </div>

                                <div class="profile-info-value">
                                    <span>{{ $row->cnic }}</span>
                                </div>
                            </div>

                            <div class="profile-info-row">
                                <div class="profile-info-name"> Status </div>

                                <div class="profile-info-value">
                                    <span class="editable editable-click" id="username">{!!$row->status_label!!}</span>
                                </div>
                            </div>


                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-3">
                        <div class="space visible-xs"></div>
                        <table class="table table-striped table-bordered table-hover">
                            <tbody>
                                <tr>
                                    <td>Created</td>
                                    <th>{{ $row->created_at }}</th>
                                </tr>
                                <tr>
                                    <td>Total Orders</td>
                                    <th>{{ $stats['orders'] }}</th>
                                </tr>
                                <tr>
                                    <td>Orders Amount</td>
                                    <th>{{ $stats['orders_amount'] }}</th>
                                </tr>
                                <tr>
                                    <td>Amount Pay</td>
                                    <th>{{ $stats['pay'] }}</th>
                                </tr>
                                <tr>
                                    <td>Amount Due</td>
                                    <th>{{ $stats['due'] }}</th>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <td colspan="2"><button>Receive Payment</button></td>
                                </tr>
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>

        </div>
        <!------------------------------  Transactions  -------------------------------------->
        <div class="col-sm-12 col-xs-12 widget-container-col ui-sortable" id="widget-container-col-10">
            <div class="space"></div>
            <div class="widget-box ui-sortable-handle" id="widget-box-10">
                <div class="widget-header widget-header-small">
                    <h5 class="widget-title smaller">Payments</h5>
                </div>

                <div class="widget-body">
                    <div class="widget-main padding-6">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered yajratable" id="payments-table" style="width:100%"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-xs-12 widget-container-col ui-sortable" id="widget-container-col-10">
            <div class="space"></div>
            <div class="widget-box ui-sortable-handle" id="widget-box-10">
                <div class="widget-header widget-header-small">
                    <h5 class="widget-title smaller">Orders</h5>
                </div>

                <div class="widget-body">
                    <div class="widget-main padding-6">
                    <div class="table-responsive">
                            <table class="table table-striped table-bordered yajratable" id="orders-table" style="width:100%"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!------------------------------  Transactions End -------------------------------------->
        <!-- <div class="col-xs-12">
        <div class="media search-media">
            <div class="media-left">
                <a href="#">
                    <img class="media-object" data-src="holder.js/72x72" alt="72x72" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%2272%22%20height%3D%2272%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2072%2072%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_19128bb28e3%20text%20%7B%20fill%3A%23AAAAAA%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A10pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_19128bb28e3%22%3E%3Crect%20width%3D%2272%22%20height%3D%2272%22%20fill%3D%22%23EEEEEE%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2217.46875%22%20y%3D%2240.5%22%3E72x72%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E" data-holder-rendered="true" style="width: 72px; height: 72px;">
                </a>
            </div>

            <div class="media-body">
                <div>
                    <h4 class="media-heading">
                        <a href="#" class="blue">Media heading</a>
                    </h4>
                </div>
                <p>Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin commodo. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis ...</p>

                <div class="search-actions text-center">
                    <span class="text-info">$</span>

                    <span class="blue bolder bigger-150">220</span>

                    monthly
                    <div class="action-buttons bigger-125">
                        <a href="#">
                            <i class="ace-icon fa fa-phone green"></i>
                        </a>

                        <a href="#">
                            <i class="ace-icon fa fa-heart red"></i>
                        </a>

                        <a href="#">
                            <i class="ace-icon fa fa-star orange2"></i>
                        </a>
                    </div>
                    <a class="search-btn-action btn btn-sm btn-block btn-info">Book it!</a>
                </div>
            </div>
        </div>
</div> -->
    </div> <!-- row end -->

</div>







<!--- delete confirmation modal --->
<div class="modal" tabindex="-1" role="dialog" id="deletePopup">
    <div class="modal-dialog" role="document">
        <div class="modal-content">


            <form action="#" method="post" id="delete_form">
                <input type="hidden" name="_method" value="delete" />

                <div class="modal-header">
                    <h5 class="modal-title">confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Do you really want to delete ?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Confirm</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>

            </form>

        </div>
    </div>
</div>
<!--- delete confirmation modal end --->
@endsection

@section('javascript')
<script>
    function deleteConfirmation(objID) {
        $('#delete_form').attr('action', '{{ $adminURL }}/' + objID);
        $('#deletePopup').modal('show');
    }

    $(document).ready(function() {
        setTimeout(function() {
            $('#orders-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('sale.order.list') }}?group_by=issue_key&customer_id={{ $row->id }}",
                columns: [{
                        data: 'id',
                        name: 'id',
                        title: 'ID.',
                        width: '5%'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name',
                        title: 'Customer',

                    }, {
                        data: 'item_count',
                        name: 'item_count',
                        title: 'Items',

                    }, {
                        data: 'total_offer_price',
                        name: 'total_offer_price',
                        title: 'Offer Amount',

                    }, {
                        data: 'total_price',
                        name: 'total_price',
                        title: 'Bill Price',
                    }, {
                        data: 'total_discount',
                        name: 'total_discount',
                        title: 'Total Discount',

                    }, {
                        data: 'shop_name',
                        name: 'shop_name',
                        title: 'Shop',

                    }, {
                        data: 'order_time',
                        name: 'order_time',
                        title: 'Date N Time',

                    },
                    {
                        data: 'action',
                        name: 'action',
                        title: 'Action'
                    }
                ],
                order: [
                    [0, 'desc']
                ]
            });


            //--------------
            $('#payments-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('finance.payment.list') }}?customer_id={{ $row->id }}",
                columns: [{
                        data: 'id',
                        name: 'id',
                        title: 'ID.',
                        width: '5%'
                    }, {
                        data: 'created_at',
                        name: 'created_at',
                        title: 'Time',

                    }, {
                        data: 'order_id',
                        name: 'order_id',
                        title: 'Order Id',

                    }, {
                        data: 'type',
                        name: 'type',
                        title: 'Type',

                    }, {
                        data: 'amount',
                        name: 'amount',
                        title: 'Amount',
                    }, {
                        data: 'status',
                        name: 'status',
                        title: 'Status',

                    }, {
                        data: 'balance',
                        name: 'balance',
                        title: 'Balance',

                    },
                    {
                        data: 'action',
                        name: 'action',
                        title: 'Action'
                    }
                ],
                order: [
                    [0, 'desc']
                ]
            });
            //--------------

        }, 500);


    });
</script>
@endsection