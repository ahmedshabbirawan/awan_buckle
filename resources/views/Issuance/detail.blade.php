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


<!------------------    NEW RETURN FORM   ------------------------------->
<div class="row">
  <div class="col-lg-12">
    <div class="">

    <table width="100%" border="0">
      <tr>
        <td width="120"><img src="{{asset('assets/images/logo/logo.jpg')}}" width="120" /></td>
        <td class="text-center">
      <h4 class="text-uppercase"><b>Punjab Information Technology Board</b></h4>
      <h4><b>Government of the Punjab</b></h4>
      <h5>11th Floor, Arfa Software Technology Park.<br/>
      346-B, Main Ferozepur Road, Lahore, Pakistan</h5>
        </td>
        <td width="120"><img src="{{asset('assets/images/logo/logo-icon.png')}}" width="120"/></td>
      </tr>
      <tr>
        <td colspan="3">&nbsp;</td>
      </tr>
      <tr>
        {{-- <td colspan="3" class="text-center"><h4><b>HANDING TAKING REQUISITION FORM</b></h4></td> --}}
        <td colspan="3" class="text-center"><h4><b>Issued Key : {{ $item->issue_key }} </b></h4></td>
      </tr>
      <tr>
        <td colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="3">
          <table width="100%" border="0">
            <tr>
              <td width="150"><b>Employee Code</b>&nbsp;&nbsp;</td>
              <td width="5%"><b>:</b></td>
              <td>{{ $row->emp_code }}</td>
              <td width="100" class="text-right"><b>Date</b></td>
              <td width="5%"><b>:</b></td>
              <td width="100">2022-12-29</td>
            </tr>
            <tr>
              <td width="150"><b>Employee Name</b>&nbsp;&nbsp;</td>
              <td width="5%"><b>:</b></td>
              <td colspan="4">{{ $row->full_name }}</td>
            </tr>
            <tr>
              <td width="150"><b>Designation</b>&nbsp;&nbsp;</td>
              <td width="5%"><b>:</b></td>
              <td colspan="4">{{ $row->designation }}</td>
            </tr>
            <tr>
              <td width="150"><b>Project / Department</b>&nbsp;&nbsp;</td>
              <td width="5%"><b>:</b></td>
              <td colspan="4">{{ $row->project }}</td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="3">
          <div class="row">
            <div class="col-sm-12">
              <div class="table-responsive">
                <table class="table table-striped table-bordered yajratable" id="return-item-table" style="width:100%"></table>
              </div>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </td>
      </tr>
      <tr>
        <td colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="3">
          {{-- <div class="col-lg-6 col-sm-12 no-print">
            <label class="" for="form-field-1"> Return Report </label>
            <div><input type="file" name="return_report_file" id="return_report_file" /></div>
          </div> --}}
        </td>
      </tr>
      <tr>
        <td colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="3">
          <table width="100%" border="0">
            <tr>
              <td width="150" colspan="3" class="text-uppercase"><b>Issued BY</b>&nbsp;&nbsp;</td>
              <td width="150" colspan="3" class="text-uppercase"><b>Received By</b></td>
            </tr>
            <tr><td colspan="6">&nbsp;</td></tr>
            <tr>
              <td width="150"><b>Name</b>&nbsp;&nbsp;</td>
              <td width="5%"><b>:</b></td>
              <td>{{Auth()->user()->name}}</td>
              <td width="150"><b>Contact #</b></td>
              <td width="3%"><b>:</b></td>
              <td width="150">{{$row->mobile}}</td>
            </tr>
            <tr>
              <td width="150"><b>Signature</b>&nbsp;&nbsp;</td>
              <td width="5%"><b>:</b></td>
              <td>______________________</td>
              <td width="150"><b>CNIC</b></td>
              <td width="3%"><b>:</b></td>
              <td width="150">{{$row->cnic}}</td>
            </tr>
            <tr>
              <td width="150"><b>&nbsp;</b>&nbsp;&nbsp;</td>
              <td width="5%">&nbsp;</td>
              <td>&nbsp;</td>
              <td width="150"><b>Signature</b>&nbsp;&nbsp;</td>
              <td width="3%"><b>:</b></td>
              <td width="150">______________________</td>
            </tr>
          </table>
        </td>
      </tr>
    </table>



    </div><!-- /.col -->
  </div>
</div> <!-- /.row -->


<div class="space-8"></div>
<div class="widget-footer no-print">
<div class="row clearfix form-actions no-print">
  <div class="col-sm-12 text-right">
    <a href="javascript:void(0);" onclick="form_print();" class="btn btn-xs btn-primary bigger hard_copy no-print"><i class="ace-icon fa fa-print"></i> Print Form </a>
    <a href="javascript:void(0);" onclick="submit_return_form();" class="btn btn-xs btn-primary bigger hard_copy no-print"><i class="ace-icon fa fa-upload"></i> Submit Return Form </a>
  </div>
</div><!-- /.row -->
</div>

<!-------------------------------------------------------------------------------------------------------->




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

        table = $('#return-item-table').DataTable({
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
            ajax: "{{ route('issuance.list') }}?emp_id={{ $emp->id }}&issue_key={{ $item->issue_key }}",
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

    $('#return-item-table_wrapper > .row:first-child').html('<strong id="item-title" >Issued Items</strong>');
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