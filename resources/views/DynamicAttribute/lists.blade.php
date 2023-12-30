@extends('Layout.master')

@section('title')
{{ $name }}
@endsection

@section('content')

<div class="page-content">


<div class="page-header" style="min-height:40px;">
    <div class="" style="float: left;">
        <h1> Attributes <small> <i class="ace-icon fa fa-angle-double-right"></i> {{ $name }} </small> </h1>
    </div>
    <div class="" style="float: right;">
            <a href="{{ url('attribute/'.$slug.'/create') }}" class="btn btn-xs btn-light bigger"><i class="ace-icon fa fa-floppy-o"></i> Add Record </a> 
    </div>
</div>



<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px;">

        <div class="card radius-10 border-top border-0 border-4 border-danger">
            <div class="card-body">
                @include('Layout.alerts')
                <div class="table-responsive">
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
<script>
    var table;
    var selectID;



    function doChangeStatus(objID){
        var changeURL = "{{ url('attribute/'.$typeID.'/status') }}"; 
        $.ajax({
            dataType: 'json',
            type:'get',
            url : changeURL + '/' + objID,
            success:function(res){
                console.log('yes');
                if(res.status){
                    table.ajax.reload();
                    $('#statusPopup').modal('hide');
                } 
            }
        });
    }

   


    function changeStatus(id) {
        selectID = id;
        $.confirm({
            title: 'Confirmation',
            content: 'Do you really want to change status ?',
            buttons: {
                yes: {
                    text: 'Yes',
                    action: function() {
                        doChangeStatus(id);
                    }
                },
                no: {
                    text: 'No', // With spaces and symbols
                    action: function() {

                    }
                }
            }
        });
    }

    $(document).ready(function() {

      setTimeout(() => {
        table = $('#yajra-table').DataTable({
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
            ajax: "{{ url('attribute/'.$slug.'/list') }}",
            columns: [
                //   {data: 'id', name: 'id', title : 'ID'},
                {
                    "data": "id",
                    title: 'Sr.',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    width: '10%'
                },
                {
                    data: 'value',
                    name: 'value',
                    title: 'Value',
                   // width: '40%'
                },
                {
                    data: 'status_label',
                    name: 'status_Label',
                    title: 'Status',
                    width: '10%'
                },
                {
                    data: 'action',
                    name: 'action',
                    title: 'Action',
                    width: '20%'                    
                }
            ],
             order: [[1, 'asc']]
        });
      }, 100);
        

    

    });
</script>
@endsection