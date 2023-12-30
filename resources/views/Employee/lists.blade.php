@extends('Layout.master')

@section('title')
{{ $name }}
@endsection

@section('content')

<div class="page-content">

<div class="page-header" style="min-height:40px;">
    <div class="" style="float: left;">
        <h1>Employee</h1>
    </div>
    <div class="" style="float: right;">
    
    <a href="javascript:void(0);" onclick="sync_remote_data();"  class="btn btn-xs btn-light bigger"><i class="ace-icon fa fa-floppy-o"></i> Sync  Data </a>  
    
    <a href="{{$adminURL.'/create'}}" class="btn btn-xs btn-light bigger"><i class="ace-icon fa fa-floppy-o"></i> Add Record </a> 
    </div>
</div>

<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px;">

        <div class="card radius-10 border-top border-0 border-4 border-danger">

            <div class="card-body">

                @include('Layout.alerts')

                <div class="table-responsive">
                    <table id="employee-table" class="table table-striped table-bordered" style="width:100%">
                    </table>
                </div>
            </div>



        </div>
    </div>
</div>
<!--end row-->

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
</div>
@endsection

@section('javascript')
<script src="{{asset('assets/js/bootbox.js')}}"></script>
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

    function loadEmployeeData(){
        table = $('#employee-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('employees/datatable') }}",
            columns: [
                // {data: 'id', name: 'id', title : 'ID'},
                // {
                //     "data": "id",
                //     title : 'Sr.',
                //     render: function (data, type, row, meta) {
                //         return meta.row + meta.settings._iDisplayStart + 1;
                //     }
                // },

                {data: 'emp_code', name: 'emp_code', title: 'Code'},
                {data: 'first_name', name: 'first_name', title: 'Name', render: function (data, type, row, meta) {
                  //  console.log(row);
                    return row.first_name+' '+row.last_name;
                } },
                {data: 'email', name: 'email', title: 'E-Mail'},                
                {data: 'cnic', name: 'cnic', title: 'CNIC'},
                {data: 'mobile', name: 'mobile', title: 'Mobile'},
                {data: 'project', name: 'project', title: 'Project'},
                {data: 'designation', name: 'designation', title: 'Designation'},
                {data: 'status_label', name: 'status', title: 'Status'},
                {data: 'action', name: 'action', title: 'Action'}
            ],
            order: [[0, 'asc']]
        });
    }


    function doChangeStatus(){
        var objID = $('#status_object_id').val();
        $.ajax({
            dataType: 'json',
            type:'get',
            url : "{{ url('employees/change-status/') }}/"+objID,
            success:function(res){
                console.log('yes');
                if(res.status){
                    table.ajax.reload();
                    $('#statusPopup').modal('hide');
                } 
            }
        });
    }

    function sync_remote_data(){
        let dialog = bootbox.dialog({message: "<span class='bigger-110'>Please wait while system fetch data from HRIS</span>", closeButton: false});
        $.ajax({
            dataType: 'json',
            type:'get',
            url : "{{ url('employees/sync-remote-data/') }}",
            beforeSend: function(){
              // do nothing
            },
            complete: function(){
                table.ajax.reload();
                dialog.modal('hide');
            },
            success:function(res){
                table.ajax.reload();
                loadEmployeeData();
            }
        });
    }




    $(document).ready(function() {

        setTimeout(function(){},3000);

        loadEmployeeData();



    });
</script>
@endsection