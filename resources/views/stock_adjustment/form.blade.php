@extends('Layout.master')

@section('title')
Stock Exhange
@endsection

@section('content')

<script>

  var productHTML = '<option value="">Select Product</option><?php foreach($products as $pro){
    echo '<option value="'.$pro->id.'" data-qty="'.optional($pro->productQtyShopWise)->qty.'" >'.$pro->name.'</option>';
  } ?>';


</script>

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

.chosen-container{
  width: 100%;
}

/* 
.select2-container{
    display: initial;
} */

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

$purchase_date = date('Y-m-d');
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

  <form method="post" id="product_form" action="" novalidate class="form-horizontal product_form">
    @csrf
    <input type="hidden" name="id" value="">


    <div class="row">
      <div class="col-sm-10 col-sm-offset-1">
        <div class="widget-box transparent">
          <div class="widget-header widget-header-large">
            <h3 class="widget-title grey lighter">
              <i class="ace-icon fa fa-leaf green"></i>
              Stock Adjustment
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
                <div class="table-responsive">


                
<!-- <tr>
<td>*</td>
<td>
  <select name="product_name" id="prod_name_0" ></select>
</td>
<td><input type="text" required class="input-sm col-sm-12"  name="current_qty" id="current_qty" placeholder="Qty"></td>
<td>
<div class="form-check">
<input class="form-check-input" type="radio" name="stock" value="in" id="stock_in">
<label class="form-check-label" for="stock_in">In</label>
</div>
<div class="form-check">
<input class="form-check-input" type="radio" name="stock" value="out" id="stock_out">
<label class="form-check-label" for="stock_out">Out</label>
</div>
</td>
<td>
<input type="text" required class="input-sm col-sm-12" name="hand_cnic" id="hand_cnic" placeholder="Qty">
</td><td>
<textarea required class="input-sm col-sm-12" name="hand_cnic" rows="4"  placeholder="Description"></textarea>
</td>
</tr> -->
                  <table class="table table-striped table-bordered" id="product_table">
                    <tr>
                      <th></th>
                      <th>Product</th>
                      <th style="width: 80px;">Current Quantity</th>
                      <th>Stock</th>
                      <th style="width: 80px;">Adjustment Quantity</th>
                      <th>Description</th>
                    </tr>
                  </table>
                </div><!-- /.col -->
              </div>


              <div class="wizard-actions">
<button type="button" onclick="addNewProduct();" id="add_new" class="btn btn-sm btn-primary btn-next" data-last="Finish">
Add New<i class="ace-icon fa fa-plus icon-on-right"></i></button>
</div>


              <div class="hr hr2"></div>
     
      
     
              <div class="space"></div>


              <div class="wizard-actions">
                <button type="button" onclick="saveAdjustment();" id="submit-delivery" class="btn btn-success btn-next" data-last="Finish">
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



@endsection
@section('script')
<script type="text/javascript">
  
  var indexCount = 1;
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

  function addNewProduct(){
    var newRow = singleRow(indexCount);
    $('#product_table').append(newRow);
  }

  function selectProduct(ele){
    var index = $(ele).attr('data-index');
    var value = $(ele).val();

    

    var qty = $('#prod_id_'+index+' option:selected').attr('data-qty');

    if(qty == ''){
      qty = '0';
    }

    $('.current_qty_'+index).val(qty);


    console.log(index, value, qty);
  }


  function singleRow(index){
    var html = '<tr id="row_'+index+'" ><td>*</td>';
    html += '<td><select name="product_id[]" onChange="selectProduct(this);" data-index="'+index+'"  id="prod_id_'+index+'" >'+productHTML+'</select></td>';
    html += '<td><input type="hidden" name="current_qty[]" class="current_qty_'+index+'" ><input type="text" disabled class="input-sm col-sm-12 current_qty_'+index+'"     placeholder="Qty"></td>';
   // html += '<td><div class="form-check"><input class="form-check-input" type="radio" name="stock[]" checked="checked" value="in" id="stock_in">';
   // html += '<label class="form-check-label" for="stock_in">In</label></div>';
   // html += '<div class="form-check"><input class="form-check-input" type="radio" name="stock" value="out" id="stock_out">';
   // html += '<label class="form-check-label" for="stock_out">Out</label></div></td>';
    html += '<td><select name="stock[]" id="stock_'+index+'" ><option value="in">In</option><option value="out">Out</option></select></td>';
    html += '<td><input type="text" required class="input-sm col-sm-12" name="adjust_qty[]" id="adjust_qty_'+index+'" placeholder="Qty"></td>';
    html += '<td><textarea required class="input-sm col-sm-12" name="description[]" rows="4"  placeholder="Description"></textarea></td>';
    html += '</tr>';
    indexCount++;
    return html;
  }


      function saveAdjustment(){
        var formData = new FormData($('#product_form')[0]); 
        $.ajax({
          //   data: fdata,
            cache: false,
            processData: false,
            contentType: false,
            type: 'post',
            data: formData,
            url:  "{{ route('stock_adjustment.save') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
              $('#product_table').html('');
              $.confirm({
                                title: 'Success',
                                content: res.message,
                                buttons: {
                                    yes: {
                                        text: 'OK',
                                        action: function() {
                                            // window.location = "{{ route('product.create') }}";
                                        }
                                    }
                                }
                            });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                                // console.log(textStatus,jqXHR, errorThrown);
                                if (jqXHR.status != 200) {
                                    if (typeof jqXHR.responseJSON !== 'undefined') {
                                        $.confirm({
                                            title: 'Error',
                                            content: jqXHR.responseJSON.message
                                        });
                                    }
                                }
                            }
        });
    }



  jQuery(function($) {


    $('#add_new').click();

   //  $('#prod_name_0').html(productHTML);
  
    $('.chosen_select').chosen({allow_single_deselect:true}); 
  
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



      ///////////////////////////

      

  });




  // 
</script>
@endsection