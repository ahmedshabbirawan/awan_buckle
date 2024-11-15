@extends('Layout.master')

@section('title')
Customers
@endsection

@section('content')

<div class="page-content">
<div class="page-header" style="min-height:40px;">
    <div class="" style="float: left;">
        <h1>Customers</h1>
    </div>
    <div class="" style="float: right;">
        <a href="{{ route('customer.create') }}" class="btn btn-xs btn-light bigger"><i class="ace-icon fa fa-floppy-o"></i> Add Record </a> 
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

    function deleteConfirmation(id) {
        selectID = id;
        $.confirm({
            title: 'Confirmation',
            content: 'Do you really want to change status ?',
            buttons: {
                yes: {
                    text: 'Yes',
                    action: function() {
                        var changeURL = "{{ route('customer.delete','') }}";
                        // alert(changeURL);
                        window.location = changeURL + '/' + selectID;
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


    function doChangeStatus(objID){
        var changeURL = "{{ route('customer.status','') }}";
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

        console.log('i am db');
        table = $('#yajra-table').DataTable({
            // lengthMenu: [
            //         [1, 2, 3, -1],
            //         [1, 2, 3, 'All'],
            //     ],
            // "aLengthMenu": [[1,5,10, 25, 50, 100, -1], [1,5,10, 25, 50, 100, "All"]],
            // 					"iDisplayLength": -1,
            // dom: 'Bfrtip',
            // buttons: ['excel'],
            iDisplayLength: -1,
            processing: true,
            serverSide: true,
            ajax: "{{ route('customer.list') }}",
            columns: [
                   {data: 'id', name: 'id', title : 'ID'},
                // {
                //     "data": "id",
                //     title: 'Sr.',
                //     // render: function(data, type, row, meta) {
                //     //     return meta.row + meta.settings._iDisplayStart + 1;
                //     // }
                // },
                {
                    data: 'name',
                 
                    title: 'Name',
                    render: function(data, type, row, meta) {
                        return row.name;
                    }
                },
                {
                    data: 'cnic',
                    name: 'cnic',
                    title: 'CNIC',
                    width: '10%'
                },
                {
                    data: 'mobile',
                    name: 'mobile',
                    title: 'Mobile',
                    width: '10%'
                },
                {
                    data: 'email',
                    name: 'email',
                    title: 'Email',
                    width: '10%'
                },
               
                {
                    data: 'address',
                    name: 'address',
                    title: 'Address',
                    width: '15%'
                },
                // {
                //     data: 'address',
                //     name: 'address',
                //     title: 'Address',
                //     width: '15%'
                // },
                // {
                //     data: 'city_name',
                //     name: 'city_name',
                //     title: 'City',
                //     width: '10%'
                // },
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
            order: [[0, 'desc']]
        });
    });
</script>
@endsection