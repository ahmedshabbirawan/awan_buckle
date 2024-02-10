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
$purchase_date = date('Y-m-d');
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
              <i class="ace-icon fa fa-undo bigger-120 green"></i>
              Sale Return
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

           
              
                <!-- <div class="table-responsive"> -->
                  <table class="table table-striped table-bordered" id="product_table">
                    <tr>
                      <th></th>
                      <th>Product</th>
                      <th style="width: 10%;">Current Quantity</th>
                      <th style="width: 30%;">Return Quantity</th>
                    </tr>
                  </table>
                <!-- </div> -->
                <!-- /.col -->

                
              


              <div class="wizard-actions">
<button type="button" onclick="addNewProduct();" id="add_new" class="btn btn-sm btn-primary btn-next" data-last="Finish">
Add New<i class="ace-icon fa fa-plus icon-on-right"></i></button>
</div>
              <div class="hr hr2"></div>
              
              


            </div>
          </div>


          <div class="widget-footer">
          <div class="col-lg-12 col-sm-12">
                <label for="form-field-1"> Reason / Description : </label>
                <div class="">  
                    <textarea name="description" id="description" class="form-control " placeholder="Description"></textarea>
                </div>
            </div>
          </div>
          <div class="widget-footer">
          <div class="wizard-actions">
                <button type="button" onclick="saveAdjustment();" style="margin-top: 5px;" id="submit-delivery" class="btn btn-success btn-next" data-last="Finish">
                  Submit
                  <i class="ace-icon fa fa-arrow-right icon-on-right"></i>
                </button>
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
    $('.chosen_select').chosen({allow_single_deselect:true});
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
    html += '<td><select name="product_id[]" class="chosen_select input-sm col-sm-12" onChange="selectProduct(this);" data-index="'+index+'"  id="prod_id_'+index+'" >'+productHTML+'</select></td>';
    html += '<td><input type="hidden" name="current_qty[]" class="current_qty_'+index+'" ><input type="text" disabled class="input-sm col-sm-12 current_qty_'+index+'"     placeholder="Qty"></td>';
   // html += '<td><div class="form-check"><input class="form-check-input" type="radio" name="stock[]" checked="checked" value="in" id="stock_in">';
   // html += '<label class="form-check-label" for="stock_in">In</label></div>';
   // html += '<div class="form-check"><input class="form-check-input" type="radio" name="stock" value="out" id="stock_out">';
   // html += '<label class="form-check-label" for="stock_out">Out</label></div></td>';
    html += '<td><input type="text" required class="input-sm col-sm-12" name="return_qty[]" id="adjust_qty_'+index+'" placeholder="Qty"></td>';
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
            url:  "{{ route('sale.order.unkonw_sale_return_save') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
              if(res.status){
                $.confirm({
                    title: 'Success',
                    content: 'Products return successfully!',
                    buttons: {
                        yes: {
                            text: 'OK',
                            action: function() {
                              $('#product_table').html('');
                              window.location = "{{ route('sale.order.list') }}";
                            }
                        }
                    }
                });
              }else{
                $.confirm({
                    title: 'Error',
                    content: res.message
                });
              }
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
    $('.chosen_select').chosen({allow_single_deselect:true}); 
    $('.date-picker').datepicker({
        autoclose: true,
        todayHighlight: true
      }).next().on(ace.click_event, function() {
        $(this).prev().focus();
      });
  });
 
</script>
@endsection