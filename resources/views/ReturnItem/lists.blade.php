@extends('Layout.master')

@section('title')
Issuance Items
@endsection

@section('content')

<div class="page-content">
<div class="page-header" style="min-height:40px;">
    <div class="" style="float: left;">
        <h1>Return Items</h1>
    </div>
    <!-- <div class="" style="float: right;">
        <a href="{{ route('issuance.create') }}" class="btn btn-xs btn-light bigger"><i class="ace-icon fa fa-floppy-o"></i> Add Record </a> 
    </div> -->
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
    var table;
    var selectID;

    $(document).ready(function() {
        setTimeout(function(){
        table = $('#yajra-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('return.list') }}?group_by=issue_key",
            columns: [
                {
                    "data": "id",
                    title: 'Sr.',
                    width:'5%',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'employee_detail',
                    name: 'employee_detail',
                    title: 'Employee',
                   
                },
                 {
                    data: 'item_count',
                    name: 'item_count',
                    title: 'Items',
                 
                },
                {data: 'return_date', name: 'return_date',title: 'Return Date'},
                {data: 'received_by', name: 'received_by',title: 'Received By'},
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