<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Invoice</title>

		<style>
			.total{ font-weight: bold; }
			.invoice-box {
				max-width: 800px;
				margin: auto;
				padding: 30px;
				border: 1px solid #eee;
				box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
				font-size: 16px;
				line-height: 24px;
				font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
				color: #555;
			}

			.invoice-box table {
				width: 100%;
				line-height: inherit;
				text-align: left;
			}

			.invoice-box table td {
				padding: 5px;
				vertical-align: top;
			}

			.invoice-box table tr td:nth-child(2) {
				text-align: right;
			}

            .invoice-box table tr td:nth-child(5) {
				text-align: right;
			}

			.invoice-box table tr.top table td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.top table td.title {
				font-size: 45px;
				line-height: 45px;
				color: #333;
			}

			.invoice-box table tr.information table td {
				padding-bottom: 40px;
			}

			.invoice-box table tr.heading td {
				background: #eee;
				border-bottom: 1px solid #ddd;
				font-weight: bold;
			}

			.invoice-box table tr.details td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.item td {
				border-bottom: 1px solid #eee;
			}

			.invoice-box table tr.item.last td {
				border-bottom: none;
			}

			.invoice-box table tr.total td:nth-child(2) {
				border-top: 2px solid #eee;
				font-weight: bold;
			}

			@media only screen and (max-width: 600px) {
				.invoice-box table tr.top table td {
					width: 100%;
					display: block;
					text-align: center;
				}

				.invoice-box table tr.information table td {
					width: 100%;
					display: block;
					text-align: center;
				}
			}

			/** RTL **/
			.invoice-box.rtl {
				direction: rtl;
				font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
			}

			.invoice-box.rtl table {
				text-align: right;
			}

			.invoice-box.rtl table tr td:nth-child(5) {
				text-align: left;
			}
		</style>
	</head>

	<body>
		<div class="invoice-box">
			<table cellpadding="0" cellspacing="0">
				<tr class="top">
					<td colspan="5">
						<table>
							<tr>
								<td class="title">
									{{ env('APP_NAME', 'POS') }}
								</td>

								<td>
									@if($sale_key)
									Invoice #: {{ $sale_key }}
									@endif

									@if(isset($order))
									Invoice #: {{ $sale_key }}
									@endif


									<br />
									Created: {{ date('d-M-Y',time()) }}<br />
									Shop / Branch:  {{ optional($shop)->name }}
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr class="information">
					<td colspan="5">
						<table>
							<tr>
								<td>
									@if($customer)
										Customer : {{ $customer->name }}<br />
										Mobile : {{ $customer->mobile }}<br />
									@endif

								</td>

								<td>
								{{ optional($shop)->name }}<br />
								{{ auth()->user()->name }}<br />
								@if(isset($cart_item) && ($cart_item->vehicle_no || $cart_item->bilty_no))
									@if($cart_item->vehicle_no)
									Vehicle: <b>{{ $cart_item->vehicle_no }}</b><br />
									@endif
									@if($cart_item->bilty_no)
									Bilty: <b>{{ $cart_item->bilty_no }}</b><br />
									@endif
								@endif
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<!-- <tr class="heading">
					<td colspan="4" >Payment Method</td>

					<td>Check #</td>
				</tr>

				<tr class="details">
					<td colspan="4" >Check</td>

					<td>1000</td>
				</tr> -->

                <tr>


                <td colspan="5" >



                <table>
                    
                    <tr class="heading">
                        <td width="35%">Item</td>
                        <td>Offer</td>
                        <td>Discount</td>
                        <td>Commission</td>
                        <td>Qty</td>
                        <td width="20%" >Total</td>
                    </tr>
    
                    <?php
						$calcSubTotal = 0;
						$calcTotalDiscount = 0;
						$calcTotalCommission = 0;
						foreach($products as $product): 
							$lineCommission = ($product->is_commission_applied && $product->commission_amount > 0) ? (float)$product->commission_amount : 0.00;
							$calcSubTotal += ($product->offer_price * $product->qty); 
							$calcTotalDiscount += ($product->qty * $product->discount);
							$calcTotalCommission += $lineCommission;
							$lineTotal = ($product->offer_price - $product->discount) * $product->qty - $lineCommission;
					?>
    
                    <tr class="item">
                        <td>{{ $product->product_name }}</td>
                        <td>{{ $product->offer_price }}</td>
                        <td>{{ $product->discount }}</td>
                        <td>
                            @if($product->is_commission_applied && $product->commission_amount > 0)
                                {{ $product->commission_percentage }}% (-{{ number_format($product->commission_amount, 2) }})
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $product->qty }}</td>
                        <td>{{ number_format($lineTotal, 2) }}</td>
                    </tr>
    
                    <?php endforeach; ?>

					<?php 
						$displaySubTotal = isset($sub_total) ? $sub_total : $calcSubTotal;
						$displayDiscount = isset($discount_total) ? $discount_total : $calcTotalDiscount;
						$displayCommission = isset($commission_total) ? $commission_total : $calcTotalCommission;
						$displayFinalBill = isset($bill_amount) ? $bill_amount : ($displaySubTotal - $displayDiscount - $displayCommission);
					?>
    
                    <!-- class="item last" -->
    
                    <tr class="total">
                        <td colspan="5">Sub Total:</td>
                        <td> {{ number_format($displaySubTotal, 2) }}</td>
                    </tr>

					<tr class="total">
                        <td colspan="5">Discount:</td>
                        <td> - {{ number_format($displayDiscount, 2) }}</td>
                    </tr>

					@if($displayCommission > 0)
					<tr class="total">
                        <td colspan="5">Commission:</td>
                        <td style="color: #2e8965;"> - {{ number_format($displayCommission, 2) }}</td>
                    </tr>
					@endif

					<tr class="total">
                        <td colspan="5">Total:</td>
                        <td> <b>{{ number_format($displayFinalBill, 2) }}</b></td>
                    </tr>
    
                    </table>


                </td>


                </tr>


                <tr> <td colspan="5" style="text-align:center" > Develop By TechBite @ 03134222632 </td> </tr>
				
			</table>
		</div>
	</body>
</html>




