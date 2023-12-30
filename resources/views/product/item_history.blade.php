@extends('Layout.master')

@section('title','Product Item History')
@section('Title','Product Item History')
@section('URL',route("product.list"))
@section('PageName','Product Item & History')


@section('content')
<div class="page-content">
    <div class="page-header">
        <h1>
            Item
        </h1>
    </div><!-- /.page-header -->



    <div class="row">
        <div class="col-xs-12 col-sm-12">
            
            <div class="profile-user-info profile-user-info-striped">
                <div class="profile-info-row">
                    <div class="profile-info-name"> Product Title </div>
                    <div class="profile-info-value"><span>{{ $row->name }}</span></div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-name"> Categories </div>
                    <div class="profile-info-value"><span>{{ $categories['category']['name'] }} / {{ $categories['sub_category']['name'] }} / {{ $categories['product_category']['name'] }}</span></div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-name"> Attributes </div>
                    <div class="profile-info-value"><span><?php 
                                            foreach($row->product_attribute_value->pluck('value','attribute_type.name') as $key => $value):
                                                echo $key.' : '.$value.'<br>';
                                            endforeach;
                                        ?></span></div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-name"> Unit of Measurement </div>
                    <div class="profile-info-value"><span>{{(isset($row->uom->name))?$row->uom->name:'' }}</span></div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-name"> Created Date & Time </div>
                    <div class="profile-info-value"><span>{{ (isset($row->created_at))?$row->created_at:'' }}</span></div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-name"> Description </div>
                    <div class="profile-info-value"><span>{{ $row->description }}</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-------------------------------------------------------------------------->

    <div class="page-header">
        <h1>
            Timeline
        </h1>
    </div><!-- /.page-header -->

<div class="row">

<div class="col-xs-12 col-sm-10 col-sm-offset-1">


    <?php foreach($history as $hisRow): ?>


    <div class="timeline-container">




        <?php if($hisRow->return_date){ ?>
            <div class="timeline-container">
                <div class="timeline-label">
                    <span class="label label-primary arrowed-in-right label-lg">
                        <b>{{ $hisRow->return_date }}</b>
                    </span>
                </div>
        
                <div class="timeline-items">
                        <div class="timeline-item clearfix">
                            <div class="timeline-info">
                                <i class="timeline-indicator ace-icon fa fa-download btn btn-primary no-hover"></i>
                            </div>
                            <div class="widget-box transparent">
                                <div class="widget-header widget-header-small"><h5 class="widget-title smaller">Return</h5></div>
                                <div class="widget-body">
                                    <div class="widget-main">
                                        Item Return by <a href="#" class="blue">{{ $hisRow->employee->full_name }}</a> 
                                        to <a href="#" class="blue">{{ $hisRow->receivedBy->name }}</a>
                                        <div class="pull-right">
                                            {{-- <i class="ace-icon fa fa-clock-o bigger-110"></i> --}}
                                            {!! $hisRow->return_condition_status_label !!}
                                        </div>
                                    </div>
                                    <div class="widget-main">
                                        {{ ($hisRow->remarks)?$hisRow->remarks:'' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
    
            <?php } ?>





        <div class="timeline-label">
            <span class="label label-success arrowed-right label-lg">
                <b>{{ $hisRow->issue_date }}</b>
            </span>
        </div>

        <div class="timeline-items">



            
            <div class="timeline-item clearfix">
                <div class="timeline-info">
                    <i class="timeline-indicator ace-icon fa fa-upload btn btn-success no-hover green"></i>
                </div>
                <div class="widget-box transparent">
                    <div class="widget-header widget-header-small">
                        <h5 class="widget-title smaller">
                            <span class="grey">Issued Item to</span>
                            <a href="#" class="blue">{{ $hisRow->employee->full_name }}</a>
                            <span class="grey">By</span>
                            <a href="#" class="blue">{{ $hisRow->issuedBy->name }}</a>
                        </h5>
                    </div>

                    <div class="widget-body">
                        <div class="widget-main">
                            content
                            <div class="space-6"></div>

                            <div class="widget-toolbox clearfix">
                                {{-- <div class="pull-left">
                                    <i class="ace-icon fa fa-hand-o-right grey bigger-125"></i>
                                    <a href="#" class="bigger-110">Click to read &hellip;</a>
                                </div> --}}

                                <div class="pull-right action-buttons">
                                    Status : {{ '' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            

        </div><!-- /.timeline-items -->




       




    </div><!-- /.timeline-container -->


    <?php endforeach; ?>


</div>

<div class="space-6"></div>
<div class="space-6"></div>

</div>

    <!-------------------------------------------------------------------------->



    <div class="row hidden">
                        <div class="col-sm-12">
                                <div class="table-responsive">
                                    <input type="hidden" value="{{ $itemID }}" id="item_id">
                                    <table class="table table-striped table-bordered yajratable" id="item_history_table" style="width:100%"></table>
                                </div>
                        </div><!-- /.col -->
                    </div><!-- /.row -->


</div>


@endsection



@section('javascript')
<script>
	var table;
	var selectID;
    var historyTable;
</script>



<script>


$(document).ready(function(){

    setTimeout(() => {
        
    
    
 historyTable = $('#item_history_table').DataTable({
            // lengthMenu: [
            //         [1, 2, 3, -1],
            //         [1, 2, 3, 'All'],
            //     ],
            // "aLengthMenu": [[1,5,10, 25, 50, 100, -1], [1,5,10, 25, 50, 100, "All"]],
            // 					"iDisplayLength": -1,
            // dom: 'Bfrtip',
            // buttons: ['excel'],
            processing: true,
            serverSide: true,
            "ajax": {
    "url": "{{ route('stocks.item.issuance_history') }}",
    "data": function ( d ) {
        d.item_id = $('#item_id').val();
    }
  },
          //   ajax: "{{ route('stocks.item.issuance_history') }}?item_id="+itemID,
            columns: [
                {
                    "data": "id",
                    title: 'Sr.',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                      //   $('#serial_number').val(row.serial_number);
                    }
                },
                {
                    data: 'serial_number',
                    name: 'serial_number',
                    title: 'Serial Number',
                    width: '10%'
                },
                {data:  'employee_detail',name: 'employee_detail', title: 'Assigned To'},
                
                {
                    data: 'qty',
                    name: 'qty',
                    title: 'Qty Issue',
                    width: '10%'
                },
                {
                    data: 'issue_date',
                    name: 'issue_date',
                    title: 'Issue Date',
                    width: '10%'
                },
                {
                    data: 'return_date',
                    name: 'return_date',
                    title: 'Return Date',
                    width: '10%'
                },
                {
                    data: 'return_condition_status',
                    name: 'return_condition_status',
                    title: 'Condition Return',
                    render: function(data, type, row, meta) {
                        return 'Working';
                    }
                },
                {
                    data: 'remarks',
                    name: 'remarks',
                    title: 'Remarks'
                }
            ],
             order: [[0, 'desc']]
        });

    }, 549);
});




function historyTableReload(itemID){
    $('#item_id').val(itemID);
     historyTable.ajax.reload();
        $('#item-history-btn').click();
}

    
</script>


@endsection