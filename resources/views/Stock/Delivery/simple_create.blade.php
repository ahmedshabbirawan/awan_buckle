@extends('Layout.master')

@section('title')
Add Purchase
@endsection
@section('content')
<style>
  @media only screen and (max-width: 600px) {
    .page-content > .row .col-lg-12, .page-content > .row .col-md-12, .page-content > .row .col-sm-12, .page-content > .row .col-xs-12{
      float: none;
      width: 100%;
    }
    /* .product_add_text{
      display: none;
    } */
    #product_attribute{
      min-height: 50px;
    }
    .product_list_table{
      overflow-y: scroll;
    }
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

$purchase_date = date('d-m-Y');
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
  <div class="row">
    <div class="col-sm-10 col-sm-offset-1">
      <div class="widget-box transparent">
        <div class="widget-header widget-header-large">
          <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-leaf green"></i>
            Add Purchase
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




        <div class="row">
          <div class="col-xs-12 col-sm-12">
            <div class="widget-box">
              <div class="widget-header widget-header-small">
                <strong>
                  Add Items
                </strong>
              </div>

              <div class="widget-footer"></div>
            </div>
          </div>

        </div>






        <form method="post" id="product_form" action="" novalidate class="form-horizontal product_form">
          @csrf
          <input type="hidden" name="id" value="">
          <div class="widget-body">
            <div class="widget-main padding-10">
              <div>
                <div class="row">

                  <div class="col-lg-3 col-sm-12">
                    <label for="supplier_id">Supplier</label>
                    <select name="supplier_id" id="supplier_id" class="col-lg-12 col-sm-12 chosen_select form-control" required>
                      <option value=""> -- Select -- </option>
                      <?php foreach ($suppliers as $key => $val) : ?>
                        <option value="<?= $key ?>"><?= $val ?></option>
                      <?php endforeach; ?>
                    </select>
                    <div id="supplier_ledger_info" style="margin-top: 6px; display: none;">
                      <div class="alert alert-info" style="margin-bottom: 0; padding: 4px 8px; font-size: 11px; border-radius: 3px;">
                        <i class="fa fa-university"></i> <strong>Payable:</strong>
                        <span id="supplier_balance_text" style="font-weight: bold;">Rs. 0.00</span>
                        <span id="supplier_balance_badge" class="label label-sm label-success pull-right" style="margin-top:-1px;">Settled</span>
                      </div>
                    </div>
                  </div>

                  <div class="col-lg-3 col-sm-12">
                    <label>Purchase Date</label>
                    <div class="input-group">
                      <input type="text" required class="input-sm col-sm-12 input_date" value="{{ $purchase_date }}" data-date-format="dd-mm-yyyy" name="purchased_date" id="purchased_date" placeholder="Purchase Date">
                      <span class="input-group-addon">
                        <i class="fa fa-calendar bigger-110"></i>
                      </span>
                    </div>
                  </div>

                  <div class="col-lg-3 col-sm-12">
                    <label for="delivery_challan_no" >Delivery Challan / Invoice No</label>
                    <input type="text" required class="input-sm col-sm-12" value="" name="delivery_challan_no" id="delivery_challan_no" placeholder="Challan/Invoice No">
                  </div>

                  <div class="col-lg-3 col-sm-12">
                    <label for="delivery_amount" >Total Delivery / Purchase Amount</label>
                    <input type="text" required class="input-sm col-sm-12 form-control" value="{{ $amount_delivery }}" name="delivery_amount" id="delivery_amount" placeholder="Delivery Amount">
                  </div>
                </div>

                <!-- Financial Settlement Block -->
                <div class="row" style="margin-top: 14px; background-color: #f7f9fa; border: 1px solid #dce4ec; border-radius: 4px; padding: 12px 10px 8px 10px;">
                  <div class="col-lg-3 col-sm-12">
                    <label for="payment_method" style="font-weight: bold;"><i class="fa fa-credit-card text-primary"></i> Payment Method / Account</label>
                    <select name="payment_method" id="payment_method" class="form-control" onchange="calculatePurchaseSettlement();">
                      <option value="cash" selected>Cash Account (Settled in Full)</option>
                      <option value="bank_transfer">Bank Account (Settled in Full)</option>
                      <option value="credit">Credit / On Account (Full Amount Payable)</option>
                      <option value="split">Split Payment (Part Cash/Bank, Part Credit)</option>
                    </select>
                  </div>

                  <div class="col-lg-3 col-sm-12">
                    <label for="paid_amount" style="font-weight: bold;"><i class="fa fa-money text-success"></i> Paid Amount (Now)</label>
                    <div class="input-group">
                      <span class="input-group-addon">Rs</span>
                      <input type="number" step="0.01" class="form-control" name="paid_amount" id="paid_amount" placeholder="0.00" oninput="calculatePurchaseSettlement();">
                    </div>
                  </div>

                  <div class="col-lg-3 col-sm-12">
                    <label for="due_amount" style="font-weight: bold;"><i class="fa fa-book text-danger"></i> Balance to Ledger (Due AP)</label>
                    <div class="input-group">
                      <span class="input-group-addon">Rs</span>
                      <input type="number" step="0.01" class="form-control" name="due_amount" id="due_amount" readonly placeholder="0.00" style="background-color: #fdf5f5; color: #d15b47; font-weight: bold;">
                    </div>
                  </div>

                  <div class="col-lg-3 col-sm-12">
                    <label style="font-weight: bold;"><i class="fa fa-info-circle text-info"></i> Posting Summary</label>
                    <div id="settlement_summary_badge" class="alert alert-success" style="padding: 6px 10px; margin-bottom: 0; font-size: 11px; border-radius: 3px;">
                      <span id="settlement_summary_text">Full Settlement: Paid Rs. 0.00 | Due Rs. 0.00</span>
                    </div>
                  </div>
                </div>
                <div class="space"></div>
              </div>
              <div class="space-6"></div>
              <div class="hr hr8 hr-double hr-dotted"></div>
              <div class="widget-header widget-header-small">
                <h4 class="widget-title blue smaller">Purchase Product(s)</h4>
              </div>
              <div class="widget-body">
                <input type="hidden" name="id" value="">
                <div class="widget-main">
                  <div class="row">

                    <div class="col-lg-10" id="product_attribute"></div>
                    <div class="col-lg-2 ">
                      <label class="" for="form-field-1">&nbsp;</label>
                      <div><button type="button" class="btn btn-success btn-next" onclick="pos_app.showAddProductModal();"> <i class="ace-icon fa fa-plus -right icon-on-right"></i> <span class="product_add_text">Add New Product</span></button></div>
                    </div>
                  </div>
                  <div class="row">

                    <div class="col-lg-3 col-sm-3">
                      <label class="" for="form-field-1"> Shop : </label>
                      <div>
                        <select name="shop_id" id="shop_id" class="chosen-select form-control select21" required style="width:100%">
                          <option value=""> -- Select -- </option>
                          <?php foreach ($shops as $id => $name) : ?>
                            <option value="<?= $id ?>"><?= $name ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-lg-3 col-sm-12">
                      <label class="" for="form-field-1"> Purchase Price </label>
                      <div>
                        <input type="text" required class="form-control stock_item_input" value="" name="unit_price" id="unit_price" placeholder="Unit Price">
                      </div>
                    </div>

                    <div class="col-lg-3 col-sm-3"><label class="" for="form-field-1"> Quantity  &nbsp; &nbsp; &nbsp; <span style="color: #ba4444;">Current Quantity : <b id="current_qty"> -- </b></span>   </label>
                      <div><input type="text" required class="form-control stock_item_input" value="" name="qty" id="qty" placeholder="Quantity"></div>
                    </div>

                    <div class="col-lg-3 col-sm-3">
                      <label class="" for="form-field-1"> UOM : </label>
                      <div>
                        <input type="hidden" required class="form-control stock_item_input" value="" readonly="on" name="uom_id" id="uom_id" placeholder="UOM">
                        <input type="text" required class="form-control stock_item_input" value="" readonly="on" name="uom_code" id="uom_code" placeholder="UOM">
                      </div>
                    </div>
                  </div>
                </div>

                <!------>
                <div class="modal-footer" style="margin: 15px 0px;">
                  <button type="button" class="btn btn-primary btn-block" id="add-item-purchase" onclick="saveProduct();">Add item to purchase</button>
                </div>
                <!----->

              </div>


              <div class="row">
                <div class="col-lg-12 col-sm-12 product_list_table">
                <table class="table table-striped table-bordered table-responsive">
                  <thead>
                    <tr>
                      <th class="center">#</th>
                      <th>Category</th>
                      <th>Product</th>
                      <th>Description</th>
                      <th>Qty</th>
                      <th>Purchase Unit Price</th>
                      <th>Total Price</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="product_list"></tbody>
                </table>
                </div>
              </div>

              <div class="hr hr8 hr-double hr-dotted"></div>

              <div>
                <div class="table-responsive">
                  <table class="table table-striped table-bordered">
                    <tr>
                      <th></th>
                      <th>Received By</th>
                      <th>Handed Over By</th>
                    </tr>
                    <tr>
                      <td>Name</td>
                      <td><input type="text" required class="input-sm col-sm-12" value="{{ $rec_name }}" name="rec_by_name" id="rec_by_name" placeholder="Name"></td>
                      <td>
                        <!-- <input type="text" name="rec_name" class="input-sm col-sm-12"> -->
                        <input type="text" required class="form-control" value="{{ $hand_name }}" name="hand_name" id="hand_name" placeholder="Name">
                      </td>
                    </tr>
                    <tr>
                      <td>CNIC</td>
                      <td>
                        <!-- <input type="text" name="rec_name" class="input-sm col-sm-12"> -->
                        <input type="text" required class="input-sm col-sm-12" value="{{ $rec_cnic }}" name="rec_by_cnic" id="rec_by_cnic" placeholder="CNIC">
                      </td>
                      <td>
                        <!-- <input type="text" name="rec_name" class="input-sm col-sm-12"> -->
                        <input type="text" required class="input-sm col-sm-12" value="{{ $hand_cnic }}" name="hand_cnic" id="hand_cnic" placeholder="CNIC">
                      </td>
                    </tr>
                    <tr>
                      <td>Phone #</td>
                      <td>
                        <input type="text" required class="form-control" value="{{ $rec_phone }}" name="rec_by_phone" id="rec_by_phone" placeholder="Phone">
                        <!-- <input type="text" name="rec_name" class="input-sm col-sm-12"> -->
                      </td>
                      <td>
                        <input type="text" required class="input-sm col-sm-12" value="{{ $hand_phone }}" name="hand_phone" id="hand_phone" placeholder="Phone">
                        <!-- <input type="text" name="rec_name" class="input-sm col-sm-12"> -->
                      </td>
                    </tr>
                    <tr>
                      <td>Designation</td>
                      <td>
                        <input type="text" required class="input-sm col-sm-12" value="{{ $rec_designation }}" name="rec_by_designation" id="rec_by_designation" placeholder="Designation">
                      </td>
                      <td>
                        <!-- <select name="designation" class="input-sm col-lg-12"><option>Please select</option></select> -->
                        <input type="text" required class="input-sm col-sm-12" value="{{ $hand_designation }}" name="hand_designation" id="hand_designation" placeholder="Designation">
                      </td>
                    </tr>
                  </table>
                </div><!-- /.col -->
              </div>

              <div class="hr hr2"></div>
              <div class="space"></div>


              <div class="wizard-actions">
                <!-- 
                <button class="btn btn-prev" disabled="disabled">
                      <i class="ace-icon fa fa-arrow-left"></i>
                      Prev
                </button> -->

                <button type="button" onclick="submitDeliveryForm();" id="submit-delivery" class="btn btn-success btn-next" data-last="Finish">
                  Submit
                  <i class="ace-icon fa fa-arrow-right icon-on-right"></i>
                </button>
              </div>


            </div>
          </div>
        </form>

      </div>
    </div>
  </div>



</div>






@endsection
@section('script')
<script type="text/javascript">
  var ajaxFailBlock = function(jqXHR, textStatus, errorThrown) {
    // console.log(textStatus,jqXHR, errorThrown);
    $("#add-item-purchase").LoadingOverlay("hide");
    $("#submit-delivery").LoadingOverlay("hide");
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



  jQuery(function($) {

    $('.chosen_select').chosen({
      allow_single_deselect: true
    });

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
    //pre-show a file name, for example a previously selected file
    //$('#id-input-file-1').ace_file_input('show_file_list', ['myfile.txt'])
    //datepicker plugin
    //link
    $('.date-picker').datepicker({
        autoclose: true,
        todayHighlight: true
      })
      //show datepicker when clicking on the icon
      .next().on(ace.click_event, function() {
        $(this).prev().focus();
      });

  });


  /******************************************         Delivery Work    **********************************************************/

  function submitDeliveryForm() {
    $("#submit-delivery").LoadingOverlay("show");

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
        url: "{{ route('stocks.simple.delivery.save') }}",
        success: function(res, textStatus, jqXHR) {
          $("#submit-delivery").LoadingOverlay("hide");
          $('#submit-delivery').removeAttr('disabled');
          // console.log('=======>>>>> ',res);
          if (jqXHR.status == 200) {
            if (typeof res.data.id !== 'undefined') {
              $.confirm({
                title: 'Success',
                content: res.message,
                buttons: {
                  yes: {
                    text: 'OK',
                    action: function() {
                      window.location = "{{ route('stocks.delivery.list') }}";
                    }
                  }
                }
              });
            }
          }
        },
        error: ajaxFailBlock
      });
    }

    // console.log(validator.checkAll());
  }






  /******************************************         Product work     **********************************************************/


  function saveProduct() {
    var serial_number = $('input[name="sn[]"]').length;
    var qty = $('#qty').val();
    var productID = $('#product_id').val();
    var shop_id = $('#shop_id').val();
    var unit_price = $('#unit_price').val();
    if ((serial_number > 0) && qty != serial_number) {
      $.confirm({
        title: 'Warning',
        content: 'Serial Numbers and quantity are not equal.',
        buttons: {
          yes: {
            text: 'OK'
          }
        }
      });
      return false;
    }

    // let myform = document.getElementById("stock_item_form");
    // let fdata = new FormData(myform);
    $("#add-item-purchase").LoadingOverlay("show");
    let fdata = new FormData();
    fdata.append('product_id', productID);
    fdata.append('shop_id', shop_id);
    fdata.append('qty', qty);
    fdata.append('unit_price', unit_price);
    fdata.append('product_cat_id', 0);
    fdata.append('sub_cat_id', 0);
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
      url: "{{ route('stocks.dp.save') }}",
      success: function(res, textStatus, jqXHR) {
        $("#add-item-purchase").LoadingOverlay("hide");
        $('.stock_item_input').val('');
        $('#input_zone').tagging("removeAll");



        // console.log('=======>>>>> ',res);
        if (jqXHR.status == 200) {
          getProductList();
          // if(typeof res.data.product !== 'undefined'){
          // $.confirm({
          //   title: 'Success',
          //   content: res.message,
          //   buttons: {
          //     yes: {
          //       text: 'OK',
          //       action: function() {
          //         getProductList();
          //         $('#CreateProduct').modal('hide');
          //       }
          //     }
          //   }
          // });
          // }
        }
      },
      error: ajaxFailBlock
    });


  }


  function getProductList() {
    $.ajax({
      dataType: 'json',
      type: 'get',
      url: "{{ route('stocks.dp.list') }}",
      success: function(res) {
        var html = '';
        var sr = 1;
        var totalCost = 0.00;
        if (res.data.length > 0) {
          $(res.data).each(function(index, ele) {
            html += '<tr><td>' + (sr) + '</td><td>' + ele.product_category_name + '</td><td>' + ele.product_name + '</td><td>' + ele.product_description + '</td><td>' + ele.quantity + ' (' + ele.uom_code + ') </td><td>' + ele.unit_price + '</td><td>' + ele.total_price + '</td><td>' + ele.action + '</td></tr>';
            sr++;
            totalCost += parseFloat(ele.total_price) || 0;
          });
          $('#product_list').html(html);
        } else {
          $('#product_list').html('<tr><td colspan="7" style="text-align: center;" >No record found</td></tr>');
        }
        if (totalCost > 0 || !$('#delivery_amount').val()) {
          $('#delivery_amount').val(totalCost.toFixed(2));
        }
        calculatePurchaseSettlement();
      }
    });
  }

  function calculatePurchaseSettlement() {
    var deliveryAmount = parseFloat($('#delivery_amount').val()) || 0;
    var method = $('#payment_method').val() || 'cash';
    var paidInput = $('#paid_amount');
    var dueInput = $('#due_amount');
    var badge = $('#settlement_summary_badge');
    var text = $('#settlement_summary_text');

    var paid = 0;
    var due = 0;

    if (method === 'cash' || method === 'bank_transfer') {
      paid = deliveryAmount;
      due = 0;
      paidInput.val(paid.toFixed(2)).attr('readonly', 'on');
      dueInput.val(due.toFixed(2));
      badge.removeClass('alert-warning alert-danger').addClass('alert-success');
      text.html('<strong>Full Settlement:</strong> Paid Rs. ' + paid.toFixed(2) + ' | Due Rs. 0.00');
    } else if (method === 'credit') {
      paid = 0;
      due = deliveryAmount;
      paidInput.val('0.00').attr('readonly', 'on');
      dueInput.val(due.toFixed(2));
      badge.removeClass('alert-success alert-warning').addClass('alert-danger');
      text.html('<strong>Full Credit / AP:</strong> Paid Rs. 0.00 | Added to Payable Rs. ' + due.toFixed(2));
    } else if (method === 'split') {
      paidInput.removeAttr('readonly');
      var enteredPaid = parseFloat(paidInput.val());
      if (isNaN(enteredPaid)) {
        enteredPaid = 0;
      }
      if (enteredPaid > deliveryAmount) {
        enteredPaid = deliveryAmount;
        paidInput.val(deliveryAmount.toFixed(2));
      }
      paid = enteredPaid;
      due = Math.max(0, deliveryAmount - paid);
      dueInput.val(due.toFixed(2));
      badge.removeClass('alert-success alert-danger').addClass('alert-warning');
      text.html('<strong>Split Settlement:</strong> Paid Rs. ' + paid.toFixed(2) + ' | Added to Payable Rs. ' + due.toFixed(2));
    }
  }

  function fetchSupplierBalance(supplierId) {
    if (!supplierId || parseInt(supplierId) <= 0) {
      $('#supplier_ledger_info').hide();
      return;
    }

    $.ajax({
      url: "{{ route('supplier.ledger.balance', '') }}/" + supplierId,
      type: 'GET',
      dataType: 'json',
      success: function(res) {
        $('#supplier_ledger_info').show();
        $('#supplier_balance_text').text(res.formatted_balance);
        var badge = $('#supplier_balance_badge');
        badge.removeClass('label-success label-danger label-warning');
        if (res.balance > 0) {
          badge.addClass('label-danger').text(res.status || 'Payable Liability');
        } else if (res.balance < 0) {
          badge.addClass('label-warning').text(res.status || 'Advance Debit');
        } else {
          badge.addClass('label-success').text('Settled (0.00)');
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
            var changeURL = "{{ route('stocks.dp.delete','') }}";
            $.ajax({
              url: changeURL + '/' + selectID,
              success: function(res, textStatus, jqXHR) {
                getProductList();
              }
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

  function select_product(ele) {
    var id = $(ele).val();
    var uom_id = $('#product_id  option[value="' + id + '"]').attr("data-uom-id");
    var uom_code = $('#product_id  option[value="' + id + '"]').attr("data-uom-code");
    var unit_price = $('#product_id  option[value="' + id + '"]').attr("data-cost-price");
    var current_qty = $('#product_id  option[value="' + id + '"]').attr("data-qty");
    console.log(current_qty);
    $('#uom_id').val(uom_id);
    $('#uom_code').val(uom_code);
    $('#unit_price').val(unit_price);
    $('#current_qty').html(current_qty);
  }


  function readSNFile() {
    console.log('i am readSNFile');
    let myform = document.getElementById("stock_item_form");
    let fdata = new FormData(myform);
    // var errorCount =  formValidator.checkAll();
    $.ajax({
      data: fdata,
      cache: false,
      contentType: false,
      processData: false,
      async: false,
      type: 'POST',
      dataType: "JSON",
      type: 'POST',
      dataType: "JSON",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('stocks.dp.read_sn_file') }}",
      success: function(res, textStatus, jqXHR) {
        // console.log(res);
        $('#input_zone').tagging("add", res);

      },
      error: ajaxFailBlock
    });
  }


  function showModal_CreateProduct() {
    $('#CreateProduct').modal('show');





    $('#input_zone').tagging();
    $('#is_expiry_date').change(function() {
      if ($(this).is(':checked')) {
        $('#warranty_date').removeAttr('disabled');
      } else {
        $('#warranty_date').val('');
        $('#warranty_date').attr('disabled', 'on');
      }

    });

    $('#is_serial_require').change(function() {
      if ($(this).is(':checked')) {
        $('#serial_number_div').toggle(300);
      } else {
        $('#serial_number_div').toggle(300);
      }
    });

    $('.select21').chosen({
      allow_single_deselect: true
    });

    setTimeout(function() {
      $('.chosen-container').css('width', '100%');
    }, 200);

    $('#serial_number_file').ace_file_input({
      no_file: 'No File ...',
      btn_choose: 'Choose',
      btn_change: 'Change',
      droppable: false,
      onchange: null,
      thumbnail: false //| true | large
    });

    return false;




    // $.ajax({
    //   // dataType: 'json',
    //   type: 'get',
    //   url: "{{ route('stocks.dp.create') }}",
    //   success: function(res) {
    //      $('#input_zone').tagging();
    //      $('#is_expiry_date').change(function(){
    //         if($(this).is(':checked')){
    //             $('#warranty_date').removeAttr('disabled');
    //         }else{
    //           $('#warranty_date').val('');
    //           $('#warranty_date').attr('disabled','on');
    //         }
    //     });
    //   }
    // });


  }


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

        // console.log('hello');

        $('#product_category_id').val('');
        $('#product_category_id').html(res);
        //        $('.select2').select2({allowClear:true});
        // var province_id = $('#province_id').val();
        $(".select21").trigger("chosen:updated");
        //  $('.select21').chosen({allow_single_deselect:true}); 
        getProductAttribute();
      }
    });
  }


  function getProductAttribute() {
    var productCatID = 0;
    $('#serial_number_div').hide();
    $('#is_serial_require').val('');
    $.ajax({
      // dataType: 'json',
      type: 'get',
      url: "{{ route('product.ajax_product_by_product_category') }}?product_cat_id=" + productCatID + "&selected_id=",
      success: function(res) {
        $('#product_attribute').html(res);
        $('#product_id').chosen({
          allow_single_deselect: true
        });
        // var province_id = $('#province_id').val();
        // validator.reload();
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




  jQuery(function($) {

    getProductAttribute();

    getProductList();



    $(document).on('change', '#sub_category_id', function() {
      $('#product_category_id').val('');
      $('#product_id').val('');
      $('#product_id').chosen({
        allow_single_deselect: true
      });
      getProductCategories();
    });

    $(document).on('change', '#product_category_id', function() {
      $('#product_id').val('');
      $('#product_id').chosen({
        allow_single_deselect: true
      });
      getProductAttribute();
    });


    $(document).on('change', '#serial_number_file', function() {
      readSNFile();
    });

    // project_change($('#project_id'));

    $(document).on('change', '#supplier_id', function() {
      fetchSupplierBalance($(this).val());
    });

    $(document).on('input change', '#delivery_amount', function() {
      calculatePurchaseSettlement();
    });

    if ($('#supplier_id').val()) {
      fetchSupplierBalance($('#supplier_id').val());
    }

    calculatePurchaseSettlement();

    $('#is_invoice_delivery').change(function() {
      if ($(this).is(':checked')) {
        console.log('i am checked . Hello Pakistan');
        $('#po_loa_loi').val('');
        $('#po_loa_loi').attr('readonly', 'on');
      } else {
        $('#po_loa_loi').removeAttr('readonly');
      }
    });

  });


  // 
</script>
@endsection