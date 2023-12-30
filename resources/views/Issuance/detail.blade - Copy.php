@extends('Layout.master')

@section('title')
Issuance Detail
@endsection

@section('content')




<div class="page-content">
<div class="page-header" style="min-height:40px;">
    <div class="" style="float: left;">
        <h1>Issuance Detail</h1>
    </div>
    <div class="" style="float: right;">
        <a href="javascript:void(0);" onclick="form_print();" class="btn btn-xs btn-light bigger"><i class="ace-icon fa fa-print"></i> Print Form </a> 
    </div>
</div>
<div class="row">
                    <div class="col-lg-12">

                      <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                          <tbody class="employee_info" >

                            <tr>
                              <th colspan="4">Detail </th>
                            </tr>
                            <tr>
                              <td width="200">Employee Name</td>
                              <th id="e_name" class="emp_info" > {{ $emp->full_name }} </th>

                              <td width="200">Employee Code</td>
                              <th id="emp_code" class="emp_info"> {{ $emp->emp_code }} </th>
                            </tr>

                            <tr>
                              <td width="200">Project</td>
                              <th id="e_project" class="emp_info"> {{  ($emp->source == '')? $emp->project : optional($emp->project)->name }} </th>

                              <td width="200">Email</td>
                              <th id="e_email" class="emp_info"> {{ $emp->email }} </th>
                            </tr>

                            <tr>
                              <td width="200">Mobile</td>
                              <th id="e_mobile" class="emp_info"> {{ $emp->mobile }} </th>

                              <td width="200">CNIC</td>
                              <th id="e_cnic" class="emp_info"> {{ $emp->cnic }} </th>
                            </tr>

                            <tr>
                              <td width="200">Issuce Date</td>
                              <th class="emp_info"> {{ $row->issue_date }} </th>

                              <td width="200">Issued By</td>
                              <th id="e_cnic" class="emp_info"> {{ $row->getUserNameByID($emp->created_by); }} </th>
                            </tr>

                            <tr id="hard_copy" >
                              <td width="200"  >Hard Copy</td>
                              <th class="emp_info" colspan="3"> 
                                <?php if($row->hard_copy_file != ''){ ?>
                                  <a href="{{ asset('storage/documents/'.$row->hard_copy_file) }}" target="blank" >View File</a>
                             <?php }else{ ?>
                                
                                <a href="javascript:void(0);" onclick="uploadHardCopyFile_popup();" >Upload</a>

                                <?php
                              } ?>  </th>

                              
                            </tr>


                          </tbody>
                        </table>
                      </div><!-- /.col -->

                    </div>
                  </div>


<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px;">
        <div class="card radius-10 border-top border-0 border-4 border-danger">


        <div class="page-header" style="min-height:40px;">
    <div class="" style="float: left;">
        <h1>Items</h1>
    </div>

</div>
          

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
<div class="modal" tabindex="-1" role="dialog" id="file_upload">
    <div class="modal-dialog" role="document">
        <div class="modal-content">


            <form action="#" method="post" id="file_upload_form">
                <input type="hidden" name="issue_key" value="{{ $row->issue_key }}" >
                @csrf
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                <h5 class="modal-title">File Upload</h5>
                    
                </div>
                <div class="modal-body">
                    <div class="row">
                    <div class="col-lg-8">
                    <label>File Upload</label>
                    <label class="ace-file-input">
                      
                      <input type="file" name="hard_copy_file" id="file_2" />
                    </label>
                  </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="uploadFile();" class="btn btn-primary">Upload</button>
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

      $('#file_1, #file_2, #file_3, #file_4').ace_file_input({
      no_file: 'No File ...',
      btn_choose: 'Choose',
      btn_change: 'Change',
      droppable: false,
      onchange: null,
      thumbnail: false //| true | large
      //whitelist:'gif|png|jpg|jpeg'
      //blacklist:'exe|php'
      //onchange:''
      //
    });



        console.log('i am db');


        setTimeout(function(){

        table = $('#yajra-table').DataTable({
         // "iDisplayLength": -1,
          paging: false,
        ordering: false,
        info: false,
        searching: false,
            // lengthMenu: [
            //         [1, 2, 3, -1],
            //         [1, 2, 3, 'All'],
            //     ],
            // "aLengthMenu": [[1,5,10, 25, 50, 100, -1], [1,5,10, 25, 50, 100, "All"]],
            // 					"iDisplayLength": -1,
         //  dom: 'Bfrtip',
            // buttons: ['excel'],
            processing: true,
            serverSide: true,
            ajax: "{{ route('issuance.list') }}?emp_id={{ $emp->id }}&issue_key",
            columns: [
                //   {data: 'id', name: 'id', title : 'ID'},
                {
                    "data": "id",
                    title: 'Sr.',
                    width:'5%',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'project_detail',
                    name: 'project_detail',
                    title: 'Project',
                    width: '20%'
                },
                {
                    data: 'product_detail',
                    name: 'product_detail',
                    title: 'Product Name',
                    width: '20%'
                },
                {
                    data: 'serial_number',
                    name: 'serial_number',
                    title: 'Serial Number',
                    width: '20%'
                },
                // {
                //     data: 'employee_detail',
                //     name: 'employee_detail',
                //     title: 'Employee Name',
                //     width: '20%'
                // },
                {
                    data: 'is_loan',
                    name: 'is_loan',
                    title: 'Is Loan',
                    width: '10%',
                    render: function(data, type, row, meta) {
                        if( parseInt(row.is_loan) == 1){ return 'YES'; }else{ return 'NO'; }
                    }
                },

                {
                    data: 'is_data_center',
                    name: 'is_data_center',
                    title: 'Data Center',
                    width: '10%',
                    render: function(data, type, row, meta) {
                        if( parseInt(row.is_data_center) == 1){ return 'YES'; }else{ return 'NO'; }
                    }
                },

                {
                    data: 'remarks',
                    name: 'remarks',
                    title: 'Remarks',
                    width: '30%'
                },
                
                {
                    data: 'qty',
                    name: 'qty',
                    title: 'Quantity',
                    width: '10%'
                },
                // {
                //     data: 'issue_date',
                //     name: 'issue_date',
                //     title: 'Issue Date',
                //     width: '20%'
                // },
                // {
                //     data: 'status_label',
                //     name: 'status',
                //     title: 'Status'
                // },
                // {
                //     data: 'action',
                //     name: 'action',
                //     title: 'Action'
                // }
            ],
            order: [[0, 'desc']]
        });

    },500);

    
    });

    function form_print(){
       //  window.print();

        $('.page-content').printThis({
            importCSS: true,
       
            removeInlineSelector:"#hard_copy",
            beforePrint: function(){
                $('#hard_copy').hide();
            },          // function called before iframe is filled
            afterPrint: function(){
                $('#hard_copy').show();
            } 
        });
    }

    function uploadHardCopyFile_popup(){
        $('#file_upload').modal('show');
    }

    function uploadFile(){
        let myform  = document.getElementById("file_upload_form");
        let fdata   = new FormData(myform);
    // var errorCount =  formValidator.checkAll();
    $.ajax({
      data: fdata,
      cache: false,
      processData: false,
      contentType: false,
      type: 'POST',
      dataType: "JSON",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('issuance.upload_hard_copy') }}",
      success: function(res, textStatus, jqXHR) {
        if (jqXHR.status == 200) {
          $.confirm({
            title: 'Alert',
            content: res.message
          });
          $('#file_upload').modal('hide');

          window.location.reload();

        }
      }
    //  error: ajaxFailBlock
    });
    }



</script>
@endsection