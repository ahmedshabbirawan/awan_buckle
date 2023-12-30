@extends('Layout.master')

@section('title')
Stock Delivery
@endsection

@section('content')

<style>
  /* Tagging Basic Style */
  .tagging {
    border: 1px solid #CCCCCC;
    font-size: 1em;
    height: auto;
    padding: 10px 10px 15px;
  }

  .tagging.editable {
    cursor: text;
  }

  .tag {
    background: none repeat scroll 0 0 #EE7407;
    border-radius: 2px;
    color: white;
    cursor: default;
    display: inline-block;
    position: relative;
    white-space: nowrap;
    padding: 4px 20px 4px 0;
    margin: 5px 10px 0 0;
  }

  .tag span {
    background: none repeat scroll 0 0 #D66806;
    border-radius: 2px 0 0 2px;
    margin-right: 5px;
    padding: 5px 10px 5px;
  }

  .tag .tag-i {
    color: white;
    cursor: pointer;
    font-size: 1.3em;
    height: 0;
    line-height: 0.1em;
    position: absolute;
    right: 5px;
    top: 0.7em;
    text-align: center;
    width: 10px;
  }

  .tag .tag-i:hover {
    color: black;
    text-decoration: underline;
  }

  .type-zone {
    border: 0 none;
    height: auto;
    width: auto;
    min-width: 20px;
    display: inline-block;
  }

  .type-zone:focus {
    outline: none;
  }

  .chosen-container {
    width: 100%;
  }

  /* 
.select2-container{
    display: initial;
} */


  .item_label {
    width: 100%;
  }

  .item_val {
    width: 100%;
  }

  .info_td {
    height: 80px;
    text-align: center;
    line-height: 5;
    font-weight: bold;
  }

  .twitter-typeahead {
    width: 100% !important;
  }

  .chosen-container{
  width: 100%;
}
</style>

<?php


$rec_name = 'N/A';
$rec_designation = 'N/A';
$rec_cnic = 'N/A';
$rec_phone = 'N/A';

$hand_name = 'N/A';
$hand_designation = 'N/A';
$hand_cnic = 'N/A';
$hand_phone = 'N/A';

$purchase_date = '';
$poi = '';
$amount_category = '';
$amount_delivery = '';
$challan_no = '';
$ledger = '';
$page_no = '';



$categoryID         = '';
$subCategoryID      = '';
$productCategoryID  = '';
$name = '';
$productID = '';
$uomID = '';
$qty = '';
?>



<div class="page-content">

  <div class="space-6"></div>

  <form method="post" id="product_form" action="javascript:void(0);" novalidate class="form-horizontal product_form">
    @csrf
    <input type="hidden" name="id" value="">


    <div class="row">
      <div class="col-sm-10 col-sm-offset-1">
        <div class="widget-box transparent">
          <div class="widget-header widget-header-large">
            <h3 class="widget-title grey lighter">
              <i class="ace-icon fa fa-leaf green"></i>
              Issuance Item
            </h3>

            <div class="widget-toolbar no-border invoice-info">
              <span class="invoice-info-label">Time:</span>
              <span class="red">{{ date('h:i a') }}</span>

              <br />
              <span class="invoice-info-label">Date:</span>
              <span class="blue">{{ date('d-m-Y',time()) }}</span>
            </div>

            <div class="widget-toolbar hidden-480">
              <!-- <a href="#">
                <i class="ace-icon fa fa-print"></i>
              </a> -->
            </div>
          </div>

          <div class="widget-body">
            <div class="widget-main padding-10">
              <div>


                <div class="row">


                  <div class="row">
                    <div class="col-lg-3">
                      <label>Issue Date</label>
                      <div class="input-group">

                        <input type="text" required class="input-sm col-sm-12 input_date" value="{{ $purchase_date }}" data-date-format="yyyy-mm-dd" name="issue_date" id="issue_date" placeholder="Issue Date">
                        <span class="input-group-addon">
                          <i class="fa fa-calendar bigger-110"></i>
                        </span>
                      </div>
                    </div>
                  </div>

                  <div class="space"></div>




                  <div class="row">
                    <div class="col-lg-3">
                      <label>Search CNIC / Employee Code</label>

                      <input type="text" id="employee_cnic" class="input-sm" value="" name="employee_cnic" placeholder="Search" style="width: 100%;">

                    </div>
                  </div>

                  <div class="space"></div>
                  <div class="row">
                    <div class="col-lg-12">

                      <div class="table-responsive">
                        <table class="table table-striped table-bordered">


                          <tbody class="employee_label">
                            <tr>
                              <td colspan="4" class="info_td"> Employee Info </td>
                            </tr>
                          </tbody>

                          <tbody class="employee_info" style="display: none;">

                            <tr>
                              <th colspan="4">Employee Detail <input type="hidden" id="emp_id" name="emp_id" value=""> </th>
                            </tr>
                            <tr>
                              <td width="200">Name</td>
                              <th id="e_name" class="emp_info" > -- </th>

                              <td width="200">Employee Code</td>
                              <th id="emp_code" class="emp_info"> -- </th>
                            </tr>

                            <tr>
                              <td width="200">Project</td>
                              <th id="e_project" class="emp_info"> -- </th>

                              <td width="200">Email</td>
                              <th id="e_email" class="emp_info"> -- </th>
                            </tr>

                            <tr>
                              <td width="200">Mobile</td>
                              <th id="e_mobile" class="emp_info"> -- </th>

                              <td width="200">CNIC</td>
                              <th id="e_cnic" class="emp_info"> -- </th>
                            </tr>
                          </tbody>
                        </table>
                      </div><!-- /.col -->

                    </div>
                  </div>

                  <div class="space-6"></div>
                  <div class="hr hr8 hr-double hr-dotted"></div>


                  <div class="widget-header widget-header-small">
                    <h4 class="widget-title blue smaller">Add Item(s)</h4>

                    <div class="widget-toolbar action-buttons">
                      <a href="javascript:void(0);" onclick="showModal_IssueProductBySerailNo();"> <i class="ace-icon fa fa-plus blue"></i> Serial No </a>
                      &nbsp; &nbsp; &nbsp;
                      <a href="javascript:void(0);" onclick="showModal_CreateProduct();"> <i class="ace-icon fa fa-plus blue"></i> Product </a>
                    </div>
                  </div>





                  <div>
                    <table class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th class="center">#</th>
                          <th class="hidden-xs">Category</th>
                          <th>Product</th>

                          <th class="hidden-xs">Description</th>
                          <th class="hidden-480">Qty</th>

                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody id="product_list"></tbody>
                    </table>
                  </div>

                  <div class="hr hr8 hr-double hr-dotted"></div>



                  <div class="hr hr2"></div>
                  <div class="space"></div>


                  <div class="wizard-actions">
                    <!-- <button class="btn btn-prev" disabled="disabled">
													<i class="ace-icon fa fa-arrow-left"></i>
													Prev
												</button> -->

                    <button type="button" onclick="submitIssuanceForm();" id="submit-delivery" class="btn btn-success btn-next" data-last="Finish">
                      Submit
                      <i class="ace-icon fa fa-arrow-right icon-on-right"></i>
                    </button>
                  </div>


                </div>
              </div>
            </div>
          </div>
        </div>

  </form>

</div>

<!------------------------------------------------------------------------------------------->

<div class="modal fade bd-example-modal-xl" id="CreateProduct" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" style="width: 1000px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h5 class="modal-title">Add Item / Product</h5>
      </div>
      <div class="modal-body" id="stock_itmes_container" style="padding: 2px;">


        <!-- form -->
        <form method="post" id="stock_item_form" action="" novalidate class="form-horizontal stock_item_form">
          <input type="hidden" name="id" value="">

          <div class="widget-main">
            <div class="row">

              <!-- Parent Category -->
              <div class="col-lg-4 col-sm-4">
                <label class="" for="form-field-1"> Category : </label>
                <div>
                  <select name="sub_category_id" id="sub_category_id" class="chosen-select form-control select21" required style="width:100%">
                    <option value=""> -- Select -- </option>
                    <?php foreach ($sub_category as $cat) : ?><optgroup label="<?= $cat->name ?>">
                        <?php foreach ($cat->subCategories as $subCat) { ?> <option value="<?= $subCat->id ?>"><?= $subCat->name ?></option> <?php } ?>
                      </optgroup> <?php endforeach; ?>
                  </select>
                </div>
              </div>

              <!-- Parent Category -->
              <div class="col-lg-4 col-sm-4">
                <label class="" for="form-field-1"> Product Category : </label>
                <div> <select name="product_category_id" id="product_category_id" class="form-control select21" required>
                    <option value=""> -- Select -- </option>
                  </select>
                </div>
              </div>

              <div class="col-lg-4 col-sm-4">
                <label>Project</label>

                <select name="project_id" id="project_id" class="col-lg-12 chosen_select select21" onchange="project_change(this);" required>

                  <option value=""> -- Select -- </option>
                  
                </select>
              </div>

            </div>
            <div class="space"></div>
            <div class="row" id="product_div">
              <div class="col-lg-12 col-sm-12">
                <label>Product</label>
                <select name="product_id" id="product_id" class="col-lg-12 chosen_select select21" onchange="product_select(this);" required>
                </select>
              </div>
            </div>

            <div class="space"></div>
            <div class="row">
              <div class="col-lg-8 col-sm-8">
                <label>Items Serial</label>
                <select name="item_id" id="item_id" class="col-lg-12 chosen_select select21" onchange="item_select(this);" required>

                </select>
              </div>
              <div class="col-lg-2 col-sm-2">
                <label>Quantity Available</label>
                <input type="text" id="avail_qty" class="input-sm col-sm-12" value="" name="avail_qty" placeholder="" readonly>
              </div>
              <div class="col-lg-2 col-sm-2">
                <label>Quantity</label>
                <input type="text" id="qty" class="input-sm col-sm-12" value="" min="1" name="qty" placeholder="">
              </div>
            </div>


            <div class="row" id="stock_info">

            </div> <!-- ROW END -->

            <div class="space"></div>

            <div class="row product_item_remarks"  >
              <div class="col-lg-5 col-sm-12">
              <div>
                  <label class="" for="form-field-1" > Return Date  </label>
                  <div class="input-group" >
                  <span class="input-group-addon"><input type="checkbox" class="stock_item_input" name="is_loan" id="is_expiry_date" value="1" checked="checked" > Is Loan basis</span>
                  <input type="text"  class="form-control input_date stock_item_input loan_return_date"   name="loan_return_date" id="loan_return_date" placeholder="Date">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  </div>
              </div>
              </div>
              <div class="col-lg-2 col-sm-2">
              <div class="control-group">
                  <!-- <label class="control-label bolder blue">Is Loan</label> -->
                  <label class="" for="form-field-1" > Data center  </label>
                  <div class="checkbox">
                    <label>
                      <input name="is_data_center" value="1" type="checkbox" class="ace">
                      <span class="lbl"> Yes</span>
                    </label>
                  </div>
                </div>
              </div>
              <div class="col-lg-5 col-sm-10">
                <div>
                  <label>Remarks</label>
                  <textarea class="form-control" name="remarks" placeholder="Remarks"></textarea>
                </div>
              </div>
            </div>



          </div>
        </form>
        <!-- form -->

      </div>
      <div class="modal-footer">
        <button type="button" id="add_item_btn-" class="btn btn-primary" onclick="addItem();">Add Item</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-------------------------------------------------------------------------------------------->


<!--------------------------------------------------------------------------------------------->

<div class="modal fade bd-example-modal-xl" id="IssueProductBySerial_Modal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" style="width: 1000px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h5 class="modal-title">Add Item / Product</h5>
      </div>
      <div class="modal-body" id="stock_itmes_container" style="padding: 2px;">


        <!-- form -->
        <form method="post" id="stock_item_sn_form" action="javascript:void(0);" novalidate class="form-horizontal stock_item_form">
          <input type="hidden" name="id" value="">

          <div class="widget-main">

            <div class="row">
              <div class="col-lg-6 col-sm-6">
                <label>Serial Number</label>
                <input type="text" id="serial_number_search" class="input-sm col-sm-12" value="" name="serial_number_search" placeholder="serial number">
                </select>
              </div>

              <!-- <div class="col-lg-2 col-sm-2">
              <label>&nbsp;</label>
              <input type="button"   class="input-sm col-sm-12" value=""   name="serial_number_search"  placeholder="Serial Number">
              </select>
            </div> -->


            </div> <!-- ROW END -->
            <div class="space"></div>
            <div class="row">
              <div class="col-lg-12 col-sm-12">
                <div class="table-responsive">
                  <table class="table table-striped table-bordered">




                    <tbody class="employee_info">
                      <!-- style="display: none;" -->

                      <tr>
                        <th colspan="4">Item Detail <input type="hidden" id="emp_id" name="emp_id" value=""> </th>
                      </tr>
                      <tr>
                        <td width="200">Product Name</td>
                        <th id="item_name" class="item_info" colspan="3"> -- </th>


                      </tr>
                      <tr>
                        <td width="200">serial Number</td>
                        <th id="item_sn" class="item_info" colspan="3"> -- </th>
                      </tr>
                      <tr>
                        <td width="200">Category</td>
                        <th id="item_category" class="item_info" colspan="3"> -- </th>


                      </tr>
                      <tr>
                        <td width="200">Project</td>
                        <th id="item_project_name" class="item_info" > -- </th>

                        <td width="200">Quantity</td>
                        <th id="item_qty" class="item_info" > -- </th>


                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

            </div><!-- /.row -->

           

             
            <div class="row sn_available"  >
              <div class="col-lg-5 col-sm-12">
              <div>
                  <label class="" for="form-field-1" > Return Date  </label>
                  <div class="input-group" >
                  <span class="input-group-addon"><input type="checkbox" class="stock_item_input" name="is_loan" id="is_expiry_date" value="1" checked="checked" > Is Loan basis</span>
                  <input type="text"  class="form-control input_date stock_item_input loan_return_date"   name="loan_return_date" id="loan_return_date1" placeholder="Date">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  </div>
              </div>
              </div>
              <div class="col-lg-2 col-sm-2">
              <div class="control-group">
                  <!-- <label class="control-label bolder blue">Is Loan</label> -->
                  <label class="" for="form-field-1" > Data center  </label>
                  <div class="checkbox">
                    <label>
                      <input name="is_data_center" value="1" type="checkbox" class="ace">
                      <span class="lbl"> Yes</span>
                    </label>
                  </div>
                </div>
              </div>
              <div class="col-lg-5 col-sm-10">
                <div>
                  <label>Remarks</label>
                  <textarea class="form-control" name="remarks" placeholder="Remarks"></textarea>
                </div>
              </div>
            </div>

             



        </form>
        <!-- form -->

      </div>
      <div class="modal-footer">
        <button type="button" id="add_item_btn-" class="btn btn-primary sn_available" onclick="addItemBySN();">Add Item</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!------------------------------------------------------------------------------------------------------------------>



@endsection
@section('script')
<script type="text/javascript">
  var ajaxFailBlock = function(jqXHR, textStatus, errorThrown) {
    // console.log(textStatus,jqXHR, errorThrown);
    $('#submit-delivery').removeAttr('disabled');
    if (jqXHR.status != 422) {
      $.confirm({
        title: 'Warning',
        content: jqXHR.responseJSON.message
      });
    } else if (jqXHR.status != 200) {
      if (typeof jqXHR.responseJSON !== 'undefined') {
        $.confirm({
          title: 'warning',
          content: jqXHR.responseJSON.message
        });
      }
    }
  }

  $('#add_item_btn').hide();


  jQuery(function($) {



    // $('#employee_id').select2({
    //   ajax: {
    //     url: "{{ route('Settings.employee.search') }}",
    //     dataType: 'json'
    //     // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
    //   }
    // });







    function getEmployeeDetailByCNIC(cnic) {

      $('#emp_id').val('');

      $('.employee_info').hide();
      $('.employee_label').show();

      // if(cnic.length < 13){
      //     return false;        
      // }



      $.ajax({
        cache: false,
        contentType: false,
        processData: false,
        async: false,
        type: 'GET',
        dataType: "JSON",
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ route('Settings.employee.search') }}?cnic=" + cnic,
        success: function(res, textStatus, jqXHR) {
          console.log('data', res);
          if (jqXHR.status == 200) {

            $('.employee_info').show();
            $('.employee_label').hide();


            $('#emp_id').val(res.id);
            $('#emp_code').html(res.emp_code);
            $('#e_project').html(res.project);
            $('#e_desi').html(res.designation);
            $('#e_name').html(res.full_name);
            $('#e_status').html(res.status_label);
            $('#e_email').html(res.email);
            $('#e_mobile').html(res.mobile);
            $('#e_cnic').html(res.cnic);
            getProductList();
          }
        },
        error: ajaxFailBlock
      });
    }



    $('#employee_cnic').blur(function() {
      var cnic = $(this).val();
      if(cnic.trim() != ''){
        getEmployeeDetailByCNIC(cnic);
      }
    });

    $('#employee_cnic').keydown(function() {
      $('#emp_id').val('');
      $('.employee_info').hide();
      $('.employee_label').show();
      $('.emp_info').html('--'); 
    });

    $('#serial_number_search').blur(function() {
      var sn = $(this).val();
      if(sn.trim() != ''){
        loadItemDetailBySN(sn);  
      }
    });

    // $('#serial_number_search').keydown(function(event){ 
    //   console.log('hi');
    //   var id = event.key || event.which || event.keyCode || 0;   
    //   if (event.which === 13) {
    //     console.log('hi world');
    //     var sn = $(this).val();
    //     loadItemDetailBySN(sn); 
    //   }
    // });

   







  });


  /******************************************         Delivery Work    **********************************************************/

  function submitIssuanceForm() {


    // const formElement = document.querySelector("#product_form");
    let myform = document.getElementById("product_form");
    let fdata = new FormData(myform);

    $('#submit-delivery').attr('disabled', 'on');

    var errorCount = 0; // validator.checkAll();
    if (errorCount == 0) {
      $.ajax({
        data: fdata,
        cache: false,
        contentType: false,
        processData: false,
        async: false,
        type: 'POST',
        dataType: "JSON",
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ route('issuance.save') }}",
        success: function(res, textStatus, jqXHR) {
          $('#submit-delivery').removeAttr('disabled');
          // console.log('=======>>>>> ',res);
          if (jqXHR.status == 200) {

            $.confirm({
              title: 'Success',
              content: res.message,
              buttons: {
                yes: {
                  text: 'OK',
                  action: function() {
                    window.location = "{{ route('issuance.list') }}";
                  }
                }
              }
            });

          }
        },
        error: ajaxFailBlock
      });
    }

    // console.log(validator.checkAll());
  }


  function project_change(ele) {
    $('#project_dg').html('<option >--Select--</option>');
    var id = $(ele).val();

    // getProducts();

    var pc_id = $('#product_category_id').val();

   

if(pc_id != ''){
    getItemByProduct();
}

   //  getItemByProduct();

  }



  /******************************************         Product work     **********************************************************/


  function loadItemDetailBySN(sn) {
    $.ajax({
      cache: false,
      processData: false,
      contentType: false,
      type: 'GET',
      dataType: "JSON",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('stocks.item.search') }}?sn=" + sn,
      success: function(res, textStatus, jqXHR) {
        console.log(res);
        if (jqXHR.status == 200) {
          // $('#add_item_btn').show();
          //  console.log('200');
          $('#item_name').html(res.product.name);
          $('#item_sn').html(res.serial_number);
          $('#item_category').html(res.category);
          $('#item_project_name').html(res.project.name);


          var qty = res.available_qty;

          if (qty > 0) {
            $('#item_qty').html(res.available_qty);
            $('.sn_available').show();
          } else {
            $.confirm({
              title: 'Warning',
              content: 'Sorry Item not available for issue.'
            });
            $('#item_qty').html('Not available');
          }




        }
      },
      error: ajaxFailBlock
    });
  }

  /*
  function searchItem(){
    var sn = $('#sn_input').val();
    $('#add_item_btn').hide();
    $.ajax({
      cache: false,
      processData: false,
      contentType: false,
      type: 'GET',
      dataType: "JSON",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('stocks.item.search') }}?sn="+sn,
      success: function(res, textStatus, jqXHR) {
        console.log('first');
        if (jqXHR.status == 200) {
          $('#add_item_btn').show();
          console.log('200');
          $('#p_name').html(res.product.name);
          $('#p_attri').html(res.attribute);
          $('#p_category').html(res.category);
        }
      },
      error: ajaxFailBlock
    });


  }

  */

  function addItem() {
    let myform = document.getElementById("stock_item_form");
    let fdata = new FormData(myform);


    var avail_qty = parseInt($('#avail_qty').val());
    var qty = parseInt($('#qty').val());

    if ((qty == '') || (qty < 1)) {
      $.confirm({
        title: 'Warning',
        content: 'Please provide Quantity.'
      });
      return false;
    } else if (avail_qty < qty) {
      $.confirm({
        title: 'Warning',
        content: 'Quantity exceed from available quantity!'
      });
      return false;
    }

    var emp_id = $('#emp_id').val();
    fdata.append('emp_id', emp_id);

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
      url: "{{ route('issuance.temp_item.add') }}",
      success: function(res, textStatus, jqXHR) {
        if (jqXHR.status == 200) {
          $('form#stock_item_form').trigger("reset");
          getProductList();
         //  $('#CreateProduct').modal('hide');

         
      $('#product_category_id').html('');
      $('#project_id').html('');
      $('#product_id').html('');

      $(".select21").trigger("chosen:updated");


         $.confirm({
        title: 'Success',
        content: 'Item add successfully.'
      });
        }
      },
      error: ajaxFailBlock
    });
  }


  function addItemBySN() {
    // $('.sn_available').hide();
    let myform = document.getElementById("stock_item_sn_form");
    let fdata = new FormData(myform);
    var emp_id = $('#emp_id').val();
    fdata.append('emp_id', emp_id);

    // alert(emp_id); return false;


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
      url: "{{ route('issuance.temp_item.add_by_sn') }}",
      success: function(res, textStatus, jqXHR) {
        if (jqXHR.status == 200) {
          $.confirm({
            title: 'Alert',
            content: res.message
          });
          $('form#stock_item_sn_form').trigger("reset");
          getProductList();
          $('#IssueProductBySerial_Modal').modal('hide');
        }
      },
      error: ajaxFailBlock
    });
  }


  function getProductList() {
    var empID = $('#emp_id').val();
    if (empID == '') {
      return false;
    }
    $.ajax({
      dataType: 'json',
      type: 'get',
      url: "{{ route('issuance.temp_item.list') }}?emp_id=" + empID,
      success: function(res) {
        var html = '';
        var sr = 1;
        if (res.data.length > 0) {
          $(res.data).each(function(index, ele) { 
            html += '<tr><td>' + (sr) + '</td><td>' + ele.product_category_name + '</td><td>' + ele.product_name + '</td><td>' + ele.product_description + '</td><td>' + ele.qty + '</td><td>' + ele.action + '</td></tr>';
            sr++;
          });
          $('#product_list').html(html);
        } else {
          $('#product_list').html('<tr><td colspan="7" style="text-align: center;" >No record found</td></tr>');
        }
      }
    });
  }



  function deleteConfirmation(id) {
    selectID = id;
    $.confirm({
      title: 'Confirmation',
      content: 'Do you really want to change status ?',
      buttons: {
        yes: {
          text: 'Yes',
          action: function() {
            var changeURL = "{{ route('issuance.temp_item.remove','') }}";
            $.ajax({
              url: changeURL + '?id=' + selectID,
              success: function(res, textStatus, jqXHR) {
                getProductList();
              },
              error: ajaxFailBlock
            });
          }
        },
        no: {
          text: 'No',
          action: function() {}
        }
      }
    });
  }





  function showModal_CreateProduct() {
    var empID = $('#emp_id').val();
    if (empID == '') {
      $.confirm({
        title: 'Warning',
        content: 'Please select employee first.'
      });
      return false;
    }
    $('#CreateProduct').modal('show');
  }


  function showModal_IssueProductBySerailNo() {
    var empID = $('#emp_id').val();
    if(empID == ''){
      $.confirm({
        title: 'Warning',
        content: 'Please select employee first.'
      });
      return false;
    }
    $('#IssueProductBySerial_Modal').modal('show');
  }


  function searchProductBySerail() {
    var sn = $('#').val();
    $.ajax({
      dataType: 'json',
      type: 'get',
      url: "{{ route('stocks.item.search') }}?serial_number=" + sn,
      success: function(res) {
        $('#sub_category_id').html(res);
        // var province_id = $('#province_id').val();
        getProductCategories();
        $('.select21').chosen({
          allow_single_deselect: true
        });
      }
    });
  }

  // issuance.temp_item.remove


  /*****************************************        Category work        *******************************************************************/
  function getCategories() {
    var parentCat = $('#parent_category_id').val();
    if (parentCat == '') {
      $('#sub_country_id').html('<option value=""> -- Select -- </option>');
      return false;
    }
    $.ajax({
      // dataType: 'json',
      type: 'get',
      url: "{{ route('product.ajax_sub_cat') }}?parent_id=" + parentCat + "&selected_id=<?= $subCategoryID ?>",
      success: function(res) {
        $('#sub_category_id').html(res);
        // var province_id = $('#province_id').val();
        getProductCategories();
        $('.select21').chosen({
          allow_single_deselect: true
        });
      }
    });
  }

  function getProductCategories() {
    // $('.select2').select2({allowClear:true});
    var subCategory = $('#sub_category_id').val();
    if (subCategory == '') {
      // $('#sub_country_id').html('<option value=""> -- Select -- </option>');
      return false;
    }
    $.ajax({
      // dataType: 'json',
      type: 'get',
      url: "{{ route('product.ajax_product_cat') }}?sub_cat_id=" + subCategory + '&selected_id=<?= $productCategoryID ?>',
      success: function(res) {
        $('#product_category_id').val('');
        $('#product_category_id').html(res);
        $(".select21").trigger("chosen:updated");
      }
    });
  }


  // 


  function getProducts() {
    var productCatID = $('#product_category_id').val();
    if (productCatID == '') {
      return false;
    }


    var projectID = $('#project_id').val();
    if (projectID == '') {
      return false;
    }


    $('#serial_number_div').hide();
    $('#is_serial_require').val('');
    $.ajax({
      // dataType: 'json',
      type: 'get',
      url: "{{ route('product.ajax_product_by_product_category') }}?product_cat_id=" + productCatID + "&project_id="+projectID,
      success: function(res) {
        $('#product_div').html(res);
        $('#product_id').chosen({allow_single_deselect:true}); 

        console.log('test');
       //  $("#product_id").trigger("chosen:updated");
      }
    });


    $.ajax({
      dataType: 'json',
      type: 'get',
      url: "{{ route('product.get_product_cat_detail') }}?id=" + productCatID + "&selected_id=",
      success: function(res) {
        if (res.sn_require == 1) {
          $('#serial_number_div').show();
          $('#is_serial_require').val('yes');
        } else {
          $('#serial_number_div').hide();
          $('#is_serial_require').val('');
        }
        // $('#serial_number_div')
      }
    });
  }




  function select_product(){

  }

  function getItemByProduct() {
    var id        = $('#product_id').val();
    $('#item_id').html('');
    if(id == ''){
      return false;
    }
    var projectID = $('#project_id').val();
    if(projectID == ''){
      $.confirm({
          title: 'Warning',
          content: 'Please select Project'
      });
      return false;
    }
    $.ajax({
      //  dataType: 'json',
      type: 'get',
      url: "{{ route('stocks.item.by_product') }}?product_id=" + id+"&project_id="+projectID,
      success: function(res) {
        $('#item_id').html(res);
        $(".select21").trigger("chosen:updated");
      }
    });

  }



  function item_select(ele) {
    var id  = $(ele).val();
    var qty = $('#item_id  option[value="' + id + '"]').attr("data-qty");
    $('#avail_qty').val(qty);
    if(parseInt(qty) > 0){
      $('.product_item_remarks').show();
    }else{
      $('.product_item_remarks').hide();
    }
  }



  function getProject(){
    var productCategory = $('#product_category_id').val();
    $.ajax({
      //  dataType: 'json',
      type: 'get',
      url: "{{ route('Settings.project.by-available-stock-and-product-cat') }}?product_category_id="+productCategory,
      success: function(res) {
        var option = '<option value=""> -- Select -- </option>';
        $(res).each(function(index,row){
          option += '<option value="'+row.id+'">'+row.name+'</option>';
        });
        console.log(option);
        $('#project_id').html(option);
        $(".select21").trigger("chosen:updated");
      }
    });
  }




  jQuery(function($) {

    getProductList();

    $(document).on('change', '#product_id', function() {
      var id = $('#product_category_id').val();

      if(id != ''){
         getItemByProduct();
      }
      
    });


    

    $('#is_expiry_date, #is_expiry_date1').change(function(){
        if($(this).is(':checked')){
            $('.loan_return_date').removeAttr('disabled');
        }else{
          $('.loan_return_date').val('');
          $('.loan_return_date').attr('disabled','on');
        }

      });

   

    $(document).on('change', '#sub_category_id', function() {

      $('#product_category_id').html('');
      $('#project_id').html('');
      $('#product_id').html('');

      getProductCategories();
    });

    $(document).on('change', '#product_category_id', function() {
      $('#product_id').html('');
      getProject(); 
    });


    $(document).on('change', '#project_id', function() {
      getProducts(); 
    });


    // 

    $(document).on('change', '#product_id', function() {
      getItemByProduct(); 
    });


   

    project_change($('#project_id'));




    $('#employee_cnic').typeahead({
      //  hint: true,
      //   highlight: true,
        minLength: 1,
      onSelect: function(item) {
        console.log(item);
      },
      displayField: 'first_name'
    }, {
      onSelect: function(item) {
        console.log(item);
      },
      limit: 12,
      //async: true,
      source: function(query, processSync, processAsync) {
        //  processSync(['This suggestion appears immediately', 'This one too']);
        return $.ajax({
          url: "{{ route('Settings.employee.auto_complete') }}",
          type: 'GET',
          displayField: 'first_name',
          valueField: "id",
          data: {
            query: query
          },
          dataType: 'json',
          preDispatch: function(query) {
            showLoadingMask(true);
            return {
              search: query
            }
          },
          preProcess: function(data) {

            if (data.success === false) {
              return false;
            } else {
              return data;
            }
          },
          success: function(result) {
            // in this example, json is simply an array of strings
            //  return processAsync(json);
            /*
          var resultList = result.results.map(function (item) {
                var aItem = item.text;
                return aItem; 
          });
          return processAsync(resultList);
      */

            var resultList = result.results.map(function(item) {
              return item.text; //{first_name : item.text};
            });
            return processAsync(resultList);

          }
        });
      }
    });



    $('.sn_available').hide();

    $('.select21').chosen({allow_single_deselect:true}); 
  //   $(".select21").trigger("chosen:updated");

    setTimeout(function(){
        $('.chosen-container').css('width','100%');
      },200);

  });


  // 
</script>
@endsection