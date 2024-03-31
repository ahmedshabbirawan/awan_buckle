@extends('Layout.master')
<?php 
    $title = 'Sale Report';
?>
@section('title')
{{ $title }}
@endsection

@section('content')
<div class="page-content">
<div class="page-header" style="min-height:40px;">
    <div class="" style="float: left;">
        <h1>{{ $title }}</h1>
    </div>
</div>


<div class="row">
    <div class="col-xs-12 col-sm-12 widget-container-col" id="widget-container-col-1">
        <div class="widget-box" id="widget-box-1">
            <div class="widget-header widget-header-small">
                <h5 class="widget-title">Filters</h5>
                <div class="widget-toolbar">
                    <a href="#" data-action="collapse">
                        <i class="ace-icon fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>

            <div class="widget-body">
                <!---------->
                <div class="widget-main">
                    <form name="filterForm" > 
                        <div class="row">
                            <div class="col-lg-3 col-sm-3">
                                <label>Shops</label>
                                <select name="shop_ids" id="shop_ids" data-placeholder="Select shops" class="col-lg-12 chosen-multi-select" multiple >
                                    @foreach($shops as $shop_id => $shop_name)
                                        <option value="{{ $shop_id }}">{{ $shop_name }}</option>
                                    @endforeach;
                                </select>
                            </div>
                            <div class="col-lg-3 col-sm-3 hidden">
                                <label>Products</label>
                                <select name="product_id_bluk" id="product_id_bluk" class="col-lg-12 chosen_select select_chosen
                                    select21"  required>
                                </select>
                            </div>
                                <div class="col-lg-3 col-sm-3">
                                    <label>Date Range</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                        <i class="fa fa-calendar bigger-110"></i></span>
                                            <div><input type="text" class="form-control" name="date_range" id="date_range" type="text"  placeholder="Date To" placeholder="Date" autocomplete="off"></div>
                                    </div>
                                </div>
                        </div>
                    </form>

                 

                </div>
                <!----------------->
                <div class="widget-toolbox padding-8 clearfix">
                <button class="btn btn-xs btn-info pull-left" onclick="print_report();">
                        <span class="bigger-110">Print</span>
                        <i class="ace-icon fa fa-print icon-on-right"></i>
                    </button>

                    <button class="btn btn-xs btn-success pull-right" onclick="reloadTable();">
                        <span class="bigger-110">Search</span>
                        <i class="ace-icon fa fa-search icon-on-right"></i>
                    </button>
                </div>
            </div>
        </div></div></div>


<style>
    .td_divider{
        background-color: lightslategray;
        width: 5px;
    }
    .td_label{
        width: 150px;
    }
</style>
<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px;">
        <div class="card radius-10 border-top border-0 border-4 border-danger">
            <div class="card-body">
                @include('Layout.alerts')
                <div class="table-responsive" id="report_con" >
                    <table class="table table-striped table-bordered yajratable"  style="width:100%">
                    <tr>
                        <td class="td_label">Shops</td> <td id="shops_val" ></td> 
                        <td class="td_divider" ></td>
                        <td class="td_label" >Dates  </td> <td id="date_val" ></td> 
                        <td class="td_divider" ></td>
                        <td class="td_label"> </td> <td></td> 
                    </tr>
                    <tr>
                        <td class="td_label"> Total Sale:</td> <td id="total_sales" ></td> 
                        <td class="td_divider"></td> 
                        <td class="td_label"> Records  </td> <td id="total_record" ></td>
                        <td class="td_divider"></td> 
                        <td class="td_label"> Product Items</td> <td id="total_item" ></td> 
                    </tr>
                    </table>
                    <table class="table table-striped table-bordered yajratable" id="yajra-table" style="width:100%"></table>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end row-->

</div>
@endsection
@section('script')
<script src="{{ asset('assets/js/moment.min.js')}}"></script>
<script src="{{ asset('assets/css/rowGroup.dataTables.min.css')}}"></script>
<script src="{{ asset('assets/app-js/filters.js')}}?ver=<?=time()?>"></script>
<script src="{{ asset('assets/js/dataTables.rowGroup.min.js')}}"></script>
<script src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>





<script>
    var table;
    $(document).ready(function() {

        // $('.select21_multi').chosen({
        //     allow_single_deselect: false
        // });

        $(".chosen-multi-select").chosen({no_results_text: "Oops, nothing found!"}); 

        $('#date_range').daterangepicker({
					'applyClass' : 'btn-sm btn-success',
					'cancelClass' : 'btn-sm btn-default',
                    
					locale: {
						applyLabel: 'Apply',
						cancelLabel: 'Cancel',
                        format: 'D/M/Y'
					}
				});



        function initDataTableRecord(){
            table = $('#yajra-table').DataTable({
            lengthMenu: [
                    [1, 2, 3, -1],
                    [1, 2, 3, 'All'],
                ],
            "aLengthMenu": [[1,5,10, 25, 50, 100, -1], [1,5,10, 25, 50, 100, "All"]],
            "iDisplayLength": -1,
            paging: false,
            dom: 'Bfrtip',
            searching: false,  info: false,
            buttons: [{
                extend: 'csv',
                text: 'Export to Excel',
                     className: 'btn btn-primary text-light',
              //  split: [ 'pdf', 'excel'],
            //     action: newExportAction
            }],
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('report.sale.all') }}",
                type: 'GET',
                data: function (d) {
                    d.shop_ids = $('#shop_ids').val(),  
                    d.date_range    = $('#date_range').val()
                },
                complete:function(res){
                    var data = res['responseJSON'];
                    $('#total_sales').html(data.totalPrice);
                    $('#date_val').html($('#date_range').val());
                    $('#total_record').html(data.recordsTotal);
                    $('#shops_val').html(data.shopsName);
                    $('#total_item').html(data.itemCount);
                }
            },
            columns: [
                {
                    "data": "id",
                    title: 'Order.',
                    render: function(data, type, row, meta) {
                        return data; //meta.row + meta.settings._iDisplayStart + 1;
                    },
                    'width': '5px'
                },                
                {data: 'shop_name',name: 'shop_name', title: 'Shop'},
                {
                    data: 'customer_name',
                    name: 'customer_name',
                    title: 'Customer',
                 
                },{
                    data: 'item_count',
                    name: 'item_count',
                    title: 'Items',
                 
                },{
                    data: 'total_offer_price',
                    name: 'total_offer_price',
                    title: 'Offer Amount',
                 
                },{
                    data: 'total_price',
                    name: 'total_price',
                    title: 'Bill Price',
                },{
                    data: 'total_discount',
                    name: 'total_discount',
                    title: 'Total Discount',
                 
                },{
                    data: 'order_time',
                    name: 'order_time',
                    title: 'Date N Time',
                 
                },
            ],
            // rowGroup: {
            //     dataSrc: 'project_name'
            // },
             order: [[0, 'desc']]
        });
        }

        initDataTableRecord();

            
    });

    function reloadTable(){
        table.ajax.reload();
    }


 
    function print_report(){
        // report_con

        $("#report_con").printThis();

    }




</script>
@endsection