@extends('Layout.master')

@section('title')
Projects Ledger
@endsection

@section('content')

<div class="page-content">

<div class="page-header" style="min-height:40px;">
    <div class="" style="float: left;">
        <h1>Projects Ledger</h1>
    </div>
   
</div>



<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px;">

        <div class="card radius-10 border-top border-0 border-4 border-danger">


        <div class="modal-content">
            <!-- <div class="modal-header">
           
                <h5 class="modal-title">Ledger</h5>
            </div>
             -->
            <div class="modal-footer">
            <div class="col-sm-9">
            <input type="text" class="col-xs-5 col-sm-5 form-control " value="" name="name" id="name" placeholder="Type Ledger Name here ... ">
            </div>   
            <button type="button" class="btn btn-primary" onclick="addNewLedger();">Add Ledger</button>
                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
            </div>
        </div>



        </div>
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


    function addNewLedger(){
        var changeURL = "{{ route('Settings.project-ledger.store') }}";
        var name = $('#name').val();
        $.ajax({
            dataType: 'json',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:{'name' : name, 'project_id' : '{{ $projectID }}'},
            url : changeURL, //  + '/' + objID,
            success:function(res){
                console.log('yes');
                if(res.status){
                    table.ajax.reload();
                    $('#statusPopup').modal('hide');
                } 
            }
        });
    }


    function doChangeStatus(objID){
        var changeURL = "{{ route('Settings.project.status','') }}";
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

    function deleteConfirmation(id) {
        selectID = id;
        $.confirm({
            title: 'Confirmation 1',
            content: 'Do you really want to change status ?',
            buttons: {
                yes: {
                    text: 'Yes',
                    action: function() {
                        var changeURL = "{{ route('Settings.project-ledger.delete','') }}";
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


    function changeStatus(id) {
        selectID = id;
        $.confirm({
            title: 'Confirmation',
            content: 'Do you really want to change status ?',
            buttons: {
                yes: {
                    text: 'Yes',
                    action: function() {
                        // var changeURL = "{{ route('Settings.project.status','') }}";
                        // alert(changeURL);
                        // window.location = changeURL + '/' + selectID;
                        doChangeStatus(id)
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


        setTimeout(function(){


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
            ajax: "{{ route('Settings.project-ledger.list',$projectID) }}",
            columns: [
                //   {data: 'id', name: 'id', title : 'ID'},
                {
                    "data": "id",
                    title: 'Sr.',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    width:'30px'
                },
                {
                    data: 'name',
                    name: 'name',
                    title: 'Name',
                   // width: '40%'
                },
                
                {
                    data: 'action',
                    name: 'action',
                    title: 'Action',
                    width:'120px'
                }
            ],
            // order: [[1, 'asc']]
        });

    },200);
    });
</script>
@endsection