@extends('Layout.master')

@section('title')
Issuance Items
@endsection

@section('content')

<div class="page-content">
<div class="page-header" style="min-height:40px;">
    <div class="" style="float: left;">
        <h1>Stock Exchange</h1>
    </div>
    <div class="" style="float: right;">
        <a href="{{ route('stock_exchange.req_form') }}" class="btn btn-xs btn-light bigger"><i class="ace-icon fa fa-add-o"></i> Create Request </a> 
    </div>
</div>


<div class="row ">
    <div class="col-6 col-lg-6" style="margin-top:20px;">
        <div class="card radius-10 border-top border-0 border-4 border-danger">
            <div class="widget-header"><h4 class="widget-title">Request Send</h4></div>
            <div class="card-body">
                @include('Layout.alerts')
                <div class="table-responsive">
                    <table class="table table-striped table-bordered yajratable" id="yajra-table-send" style="width:100%"></table>
                </div>
            </div>
        </div>
    </div>

    <!--------------------------------------------------------------------->

    <div class="col-6 col-lg-6" style="margin-top:20px;">
        <div class="card radius-10 border-top border-0 border-4 border-danger">
            <div class="widget-header"><h4 class="widget-title">Request Receive</h4></div>
            <div class="card-body">
                
                @include('Layout.alerts')
                <div class="table-responsive">
                    <table class="table table-striped table-bordered yajratable" id="yajra-table-receive" style="width:100%"></table>
                </div>
            </div>
        </div>
    </div>

</div>
<!--end row-->
</div>



<!--- delete confirmation modal --->
<div class="modal" tabindex="-1" role="dialog" id="deletePopup">
    <div class="modal-dialog" role="document">
        <div class="modal-content">


            <form action="#" method="post" id="delete_form">
                <input type="hidden" name="_method" value="delete" />
                @csrf
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




@section('script')
<script>
    var sendTable;
    var receiveTable;
    var selectID;

    var ajaxFailBlock = function(jqXHR, textStatus, errorThrown) {
    // console.log(textStatus,jqXHR, errorThrown);
    $('#submit-delivery').removeAttr('disabled');
    if (jqXHR.status != 422) {
      $.confirm({
        title: 'Warning',
        content: jqXHR.responseJSON.message
      });
    } else if (jqXHR.status != 200) {
      if (typeof jqXHR.responseJSON !== 'undefined') {
        $.confirm({
          title: 'warning',
          content: jqXHR.responseJSON.message
        });
      }
    }
  }

    function markReceive(ele){
        var linkURL = $(ele).data('link');
        $.ajax({
		type: 'GET',
		url:  linkURL,
		error:ajaxFailBlock,
		success: function(res) {
				$.confirm({
						title: 'Response',
						content: res.message,
						buttons: {
								yes: {
										text: 'OK',
										action: function() {
                                                sendTable.ajax.reload();
												receiveTable.ajax.reload();
										}
								}
						}
				});
		}
        });
    }

    $(document).ready(function() {
        setTimeout(function(){


            sendTable = $('#yajra-table-send').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('stock_exchange.records') }}?list_for=send&group_by_for=exchange_key",
            columns: [
                {
                    "data": "id",
                    title: 'Sr.',
                    width:'5%',
                    render: function(data, type, row, meta) {
                        return data; //meta.row + meta.settings._iDisplayStart + 1;
                    }
                },{
                    data: 'product_name',
                    name: 'product_name',
                    title: 'Product'
                },{
                    data: 'req_receive_shop',
                    name: 'req_receive_shop',
                    title: 'Receive from Shop',
                },{
                    data: 'req_qty',
                    name: 'req_qty',
                    title: 'Quantity',
                },{
                    data: 'date_time',
                    name: 'date_time',
                    title: 'Date Time',
                },{
                    data: 'status',
                    name: 'status',
                    title: 'Status',
                },
                {
                    data: 'action',
                    name: 'action',
                    title: 'Action'
                }
            ],
            order: [[0, 'desc']]
        });


        ///////------------------------------------------


        receiveTable = $('#yajra-table-receive').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('stock_exchange.records') }}?list_for=receive&group_by_for=exchange_key",
            columns: [
                {
                    "data": "id",
                    title: 'Sr.',
                    width:'5%',
                    render: function(data, type, row, meta) {
                        return data; //  meta.row + meta.settings._iDisplayStart + 1;
                    }
                },{
                    data: 'product_name',
                    name: 'product_name',
                    title: 'Product'
                },{
                    data: 'req_receive_shop',
                    name: 'req_receive_shop',
                    title: 'Exchange Shop',
                },{
                    data: 'req_qty',
                    name: 'req_qty',
                    title: 'Quantity',
                },{
                    data: 'date_time',
                    name: 'date_time',
                    title: 'Date Time',
                },{
                    data: 'status',
                    name: 'status',
                    title: 'Status',
                },
                {
                    data: 'action',
                    name: 'action',
                    title: 'Action'
                }
            ],
            order: [[0, 'desc']]
        });




    },500);



   

    
    });
</script>
@endsection