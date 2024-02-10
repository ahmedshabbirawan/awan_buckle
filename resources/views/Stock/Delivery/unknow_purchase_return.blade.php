@extends('Layout.master')

@section('title')
Unknow Purchase Return
@endsection

@section('content')

<script>

  var productHTML = '<option value="">Select Product</option><?php foreach($products as $pro){
    echo '<option value="'.$pro->id.'" data-qty="'.optional($pro->productQtyShopWise)->qty.'" >'.$pro->name.'</option>';
  } ?>';

var shopHTML = '<?php foreach($shops as $shop){
    echo '<option value="'.$shop->id.'" >'.$shop->name.'</option>';
  } ?>';


</script>

<style>.heading-item td{font-weight: bold;} </style>
<div class="page-content">
						<div class="page-header">
							<h1>
								Unknow Purchase Return
							</h1>
						</div><!-- /.page-header -->
						<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->
								<div class="widget-box">
									<div class="widget-header widget-header-blue widget-header-flat">
										<h4 class="widget-title lighter">Purchase ID : Unknow<b></b></h4>

										<div class="widget-toolbar">
											<label>
											Date: {{ date('d-m-Y') }} <b> </b>
											</label>
										</div>
									</div>

								

		<div class="widget-body">
			<div class="widget-main">
				<form method="post" id="return_form" action="{{  route('stocks.unknow_purchase_return_save')  }}" novalidate class="form-horizontal return_form">
		
				<div class="widget-header widget-header-blue widget-header-flat" style="padding:0px;">
									<table class="table table-striped table-hover no-margin-bottom no-border-top">
	<tr>
		<td>
			Supplier : <select name="supplier_id">

				<option value="0">Local Supplier</option>
				@foreach($suppliers as $sup)
					<option value="{{ $sup->id }}">{{ $sup->name }}</option>
				@endforeach
			</select> 
		</td>
		<td>
			
		</td>
	</tr>
	</table>
	</div>


		<hr>
		<div class="step-content pos-rel">
		<div class="step-pane active" data-step="1">
		<!------------------------		PRODUCT-ITEM	------------------------------>
		<table class="table table-striped table-bordered" id="product_table">
                    <tr>
                      <th></th>
                      <th>Product</th>
					  <th>Shop</th>
                      <th style="width: 10%;">Current Quantity</th>
                      <th style="width: 30%;">Return Quantity</th>
                    </tr>
                  </table>							
</div>


<div class="wizard-actions">
<button type="button" onclick="addNewProduct();" id="add_new" class="btn btn-sm btn-primary btn-next" data-last="Finish">
Add New<i class="ace-icon fa fa-plus icon-on-right"></i></button>
</div>

</div>



          <div class="col-lg-12 col-sm-12">
                <label for="form-field-1"> Reason / Description : </label>
                <div class="">  
                    <textarea name="description" id="description" class="form-control " placeholder="Description"></textarea>
                </div>
            </div>
      



											<hr>
											
											<div class="wizard-actions">
												<button class="btn btn-success btn-next" style="margin-top: 15px;" onclick="placeOrderConfirm();" type="button" data-last="Finish">
													Return Item Submit
													<i class="ace-icon fa fa-arrow-right icon-on-right"></i>
												</button>
											</div>
										</form>
										</div><!-- /.widget-main -->
									</div><!-- /.widget-body -->
								</div> <!-- /.widget-box -->


							</div><!-- /.col -->
						</div><!-- /.row -->
					</div>
@endsection
@section('script')
	<script>

var indexCount = 1;

		function calculateReturnItem(){
			console.log('kikiki');
			var productsID 		= $('.product_ids');
			var productsPrice 	= $('.order_item_price');
			var itemQtys 		= $('.item_qty');
			var returnQtys 		= $('.retun_qty');
			var isValid 		= true;
			var totalReturn		= 0;

			$('.item_ids').each(function(index,ele){

				var itemID = $(ele).val();
				var productID = productsID.eq(index).val();
				var qty = itemQtys.eq(index).val();
				var retunQty = parseInt(returnQtys.eq(index).val());
				var productPrice = parseFloat(productsPrice.eq(index).val());
				if(retunQty < 0){
					isValid = false;
					alert('Return Qty is not allow less then Zero');
					return false;
				}
				if(retunQty > qty){
					isValid = false;
					alert('Return Qty is not allow more then Buy Qty');
					return false;
				}
				console.log(index,itemID, productID, retunQty, productPrice );
				totalReturn = totalReturn + (productPrice*retunQty);
				$('#order_item_id_total_'+itemID).html(productPrice*retunQty);
			});
			$('#total_return_amount').html(totalReturn);
			return isValid;
		}


		function singleRow(index){
    var html = '<tr id="row_'+index+'" ><td>*</td>';
    html += '<td><select name="product_ids[]" class="chosen_select input-sm col-sm-12" onChange="selectProduct(this);" data-index="'+index+'"  id="prod_id_'+index+'" >'+productHTML+'</select></td>';
    html += '<td><select name="shop_ids[]" >'+shopHTML+'</select></td><td><input type="hidden" name="current_qty[]" class="current_qty_'+index+'" ><input type="text" disabled class="input-sm col-sm-12 current_qty_'+index+'"     placeholder="Qty"></td>';
   // html += '<td><div class="form-check"><input class="form-check-input" type="radio" name="stock[]" checked="checked" value="in" id="stock_in">';
   // html += '<label class="form-check-label" for="stock_in">In</label></div>';
   // html += '<div class="form-check"><input class="form-check-input" type="radio" name="stock" value="out" id="stock_out">';
   // html += '<label class="form-check-label" for="stock_out">Out</label></div></td>';
    html += '<td><input type="text" required class="input-sm col-sm-12" name="return_qty[]" id="adjust_qty_'+index+'" placeholder="Qty"></td>';
    html += '</tr>';
    indexCount++;
    return html;
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


		function submitForm() {

			if(!calculateReturnItem()){
				alert('Invalid Data');
				return false;
			}

$(".btn-success").LoadingOverlay("show");
// const formElement = document.querySelector("#product_form");
let myform = document.getElementById("return_form");
let url = $('#return_form').attr('action');
let fdata = new FormData(myform);
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
		url: url,
		success: function(res, textStatus, jqXHR) {
			$(".btn-success").LoadingOverlay("hide");
			
			console.log('=======>>>>> ',res);
			if (jqXHR.status == 200) {
				window.location = res.url;
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			$(".btn-success").LoadingOverlay("hide");
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
	}).done(function(){
		$(".btn-info").LoadingOverlay("hide");
	});
}


function placeOrderConfirm(){
        $.confirm({
			title: 'Confirm',
			content: 'Do you really want to Submit Return',
			buttons: {
				yes: {
					text: 'OK',
					action: function() {
						submitForm();
					}
				},
				no: { text: 'Cancel', action: function() {} }
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