@extends('Layout.master')

@section('title')
Order Detail
@endsection

@section('content')
<style>.heading-item td{font-weight: bold;} </style>
<div class="page-content">


						<div class="page-header">
							<h1>
								Sale Return
							</h1>
						</div><!-- /.page-header -->

						<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->
								

							

								<div class="widget-box">
									<div class="widget-header widget-header-blue widget-header-flat">
										<h4 class="widget-title lighter">Bill No : <b>{{ $order->id }} </b></h4>

										<div class="widget-toolbar">
											<label>
											Date: <b>{{ $order->created_at->format('d-m-Y / h:i a') }} </b>
											</label>
										</div>
									</div>

									<div class="widget-header widget-header-blue widget-header-flat" style="padding:0px;">
									<table class="table table-striped table-hover no-margin-bottom no-border-top">
	<tr>
		<td>
			Customer : <b> <?php if($customer){ ?>
			{{ $customer->name }} / {{ $customer->mobile }}
			<?php }else{ ?>
			Walking Customer
			<?php } ?> </b> 
		</td>
		<td>
			Manager : @if($manager)
			<b>{{ $manager->name }} </b>
			@endif
		</td>
	</tr>
	@if(!empty($order->vehicle_no) || !empty($order->bilty_no))
	<tr>
		<td>
			Vehicle No : <b>{{ $order->vehicle_no ?: '-' }}</b>
		</td>
		<td>
			Bilty No : <b>{{ $order->bilty_no ?: '-' }}</b>
		</td>
	</tr>
	@endif
	</table>
	</div>
		<div class="widget-header widget-header-blue widget-header-flat" style="padding:0px;">
		<table class="table table-striped table-bordered table-hover no-margin-bottom no-border-top" style="text-align:center">
		<tr>
		<td> Sub Total </td>
		<td> Discount </td>
		<td> Total </td>
	</tr>
	<tr>
		<td>{{ $order->total_price }}</td>
		<td>{{ $order->total_discount }}</td>
		<td>{{ $order->total_offer_price }}</td>
	</tr>
	</table>
									</div>
									<div class="widget-body">
										<div class="widget-main">
	<form method="post" id="return_form" action="{{  route('sale.order.sale_return_save')  }}" novalidate class="form-horizontal return_form">
		<input type="hidden" name="order_id" value="{{ $order->id }}" >

											<div id="fuelux-wizard-container" class="no-steps-container">
												<div>
												</div>
												<hr>
												<div class="step-content pos-rel">
													<div class="step-pane active" data-step="1">
													<!------------------------		PRODUCT-ITEM	------------------------------>
													<table class="table table-striped table-hover no-margin-bottom table-bordered">
                    <thead>
                    <tr class="heading-item">
                        <td width="50%">Item</td>
                        <td>Price</td>
                        <td>Qty</td>
						<td>Return Qty</td>
                        <td>Return Amount</td>
                    </tr>
					</thead>
<tbody>
                    <?php foreach($orderItem as $item): ?>
                    <tr class="item">
                        <td>
							<input type="hidden" name="order_item_id[]" value="{{ $item->id }}" class="item_ids item_input" >
							<input type="hidden" name="product_id[]" value="{{ $item->product_id }}" class="product_ids item_input" >
							<input type="hidden" name="order_item_price[]" value="{{ $item->price }}" class="order_item_price item_input" >
							<input type="hidden" name="qty[]" value="{{ $item->qty }}" class="item_qty item_input" >
							
							{{ optional($item->product)->name }}
						</td>
                        <td>{{ $item->price }}</td>
                        <td>{{ $item->qty }}</td>
						<td><input name="return_qty[]" type="text" value="0" onblur="calculateReturnItem();" class="retun_qty item_input" ></td>
                        <td id="order_item_id_total_{{ $item->id }}" >0</td>
                    </tr>
                    <?php endforeach; ?>

					<tr class="total">
                        <td >Total Amount:</td>
                        <td></td>
						<td></td>
                        <td></td>
                        <td id="total_return_amount" >0</td>
                    </tr>
					</tbody>
                    </table>							
</div>
</div>
</div>
											<hr>
											<div class="wizard-actions">
												<button class="btn btn-success btn-next" onclick="placeOrderConfirm();" type="button" data-last="Finish">
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
				totalReturn = totalReturn + (productPrice*retunQty);
				console.log(index,itemID, productID, retunQty, productPrice );
				$('#order_item_id_total_'+itemID).html(productPrice*retunQty);
			});
			$('#total_return_amount').html(totalReturn);
			return isValid;
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


		


	</script>
@endsection