@extends('Layout.master')

@section('title')
Warehouses
@endsection

@section('content')

<div class="page-content">
<div class="page-header" style="min-height:40px;">
    <div class="" style="float: left;">
        <h1>Warehouses</h1>
    </div>
    <div class="" style="float: right;">
        <a href="{{ route('Settings.warehouse.create') }}" class="btn btn-xs btn-light bigger"><i class="ace-icon fa fa-floppy-o"></i> Add Record </a> 
    </div>
</div>

<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px;">
        <div class="card radius-10 border-top border-0 border-4 border-danger">
            <div class="card-body">
                @include('Layout.alerts')
                <div class="table-responsive">
                    <table class="table table-striped table-bordered " id="yajra-table" style="width:100%"></table>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end row-->

</div>


@endsection

@section('javascript')
<script>
    var table;

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
            ajax: "{{ route('Settings.warehouse.index') }}",
            columns: [
                //   {data: 'id', name: 'id', title : 'ID'},
                {
                    "data": "id",
                    title: 'Sr.',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'name',
                    name: 'name',
                    title: 'Name',
                    width: '40%'
                },
                {
                    data: 'address',
                    name: 'address',
                    title: 'Address',
                   // width: '40%'
                },
                {
                    data: 'status_label',
                    name: 'status',
                    title: 'Status'
                },
                {
                    data: 'action',
                    name: 'action',
                    title: 'Action'
                }
            ],
            // order: [[1, 'asc']]
        });


    function deleteConfirmation(objID) {
        $('#delete_form').attr('action', "{{ url('warehouse') }}/" + objID);
        $('#deletePopup').modal('show');
    }

    $(document).ready(function() {
        //   update_all_account_balance();
    });
</script>
@endsection