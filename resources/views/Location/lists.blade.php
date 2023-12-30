@extends('Layout.master')

@section('title')
{{ $name }}
@endsection

@section('content')
<div class="page-content">
<div class="page-header"><h1>Locations</h1></div>
<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px;">

        <div class="card radius-10 border-top border-0 border-4 border-danger">
            <div class="card-body">
                <a href="{{$adminURL.'/create'}}" class="btn btn-xs btn-light bigger"><i class="ace-icon fa fa-floppy-o"></i> Add Record </a> 
            </div>

            <div class="card-body">
                @include('Layout.alerts')
                <div class="table-responsive">
                    <table id="yajra-table" class="table table-striped table-bordered" style="width:100%"></table>
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

<!--- change status modal --->
<div class="modal" tabindex="-1" role="dialog" id="statusPopup">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" value="" id="status_object_id">
                    <p>Do you really want to change status ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="doChangeStatus();" class="btn btn-primary">Confirm</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
        </div>
    </div>
</div>
<!--- change status modal end --->
@endsection

@section('script')
<script>

    // employee/datatable
    var table;

    function deleteConfirmation(objID) {
        $('#delete_form').attr('action', '{{ $adminURL }}/' + objID);
        $('#deletePopup').modal('show');
    }

    function changeStatus(objID) {
        $('#status_object_id').val(objID);
        $('#statusPopup').modal('show');
    }


    function doChangeStatus(){
        var objID = $('#status_object_id').val();
        $.ajax({
            dataType: 'json',
            type:'get',
            url : "{{ url('locations/change-status/') }}/"+objID,
            success:function(res){
                console.log('yes');
                if(res.status){
                    table.ajax.reload();
                    $('#statusPopup').modal('hide');
                } 
            }
        });
    }




    $(document).ready(function() {

        setTimeout(function(){

        table = $('#yajra-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('locations/datatable') }}",
            columns: [
                // {data: 'id', name: 'id', title : 'ID'},
                {
                "data": "id",
                title : 'Sr.',
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
                },
                {data: 'name', name: 'name', title: 'Place'},
                {data: 'parent_name', name: 'parent_name', title: 'Parent'},
                {data: 'type_label', name: 'type', title: 'Type'},
                {data: 'status_label', name: 'status', title: 'Status'},
                {data: 'action', name: 'action', title: 'Action'}
            ],
            order: [[0, 'asc']]
        });

    },100);



    });
</script>
@endsection