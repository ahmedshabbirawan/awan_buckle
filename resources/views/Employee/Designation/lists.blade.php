@extends('Layout.master')

@section('title')
Designations
@endsection

@section('content')

<div class="page-content">

<div class="page-header" style="min-height:40px;">
    <div class="" style="float: left;">
        <h1>Designations</h1>
    </div>
    <div class="" style="float: right;">
    
    
    <a href="{{ route('Settings.designations.create') }}" class="btn btn-xs btn-light bigger"><i class="ace-icon fa fa-floppy-o"></i> Add Record </a> 
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

</div>
@endsection

@section('javascript')
<script>

    // employee/datatable
    var table;

    function deleteConfirmation(objID) {
        $('#delete_form').attr('action', '{{ route("Settings.designations.delete","") }}/' + objID);
        $('#deletePopup').modal('show');
    }

    function changeStatus(objID) {
        $('#status_object_id').val(objID);
        $('#statusPopup').modal('show');
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
                        doChangeStatus(selectID);
                    }
                },
                no: {
                    text: 'No', // With spaces and symbols
                    action: function() { }
                }
            }
        });
    }

    function doChangeStatus(objID){
        var changeURL = "{{ route('Settings.designations.status','') }}/"+objID;
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

   




    $(document).ready(function() {

        setTimeout(function(){},3000);

        table = $('#employee-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("Settings.designations.list","") }}/',
            columns: [
                {data:"id",title: 'Sr.',render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
                {data: 'name', name: 'name', title: 'Name', width:'60%'},
                {data: 'status_label', name: 'status', title: 'Status', width:'20%'},
                {data: 'action', name: 'action', title: 'Action', width:'20%'}
            ],
            order: [[1, 'asc']]
        });



    });
</script>
@endsection