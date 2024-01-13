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
                        <td width="50%">Item</td>
                        <td>Offer</td>
                        <td>Discount</td>
                        <td>Qty</td>
                        <td width="20%" >Total</td>
                    </tr>
    
                    <?php
						$subTotal = 0;
						$totalDiscount = 0;
						$finalBill = 0; 
						foreach($products as $product): 
							$subTotal = $subTotal + $product->offer_price; 
							$totalDiscount = $totalDiscount + ( $product->qty * $product->discount);
							
					?>
    
                    <tr class="item">
                        <td>{{ $product->product_name }}</td>
                        <td>{{ $product->offer_price }}</td>
                        <td>{{ $product->discount }}</td>
                        <td>{{ $product->qty }}</td>
                        <td>{{ $product->offer_price * $product->qty }}</td>
                    </tr>
    
                    <?php endforeach; ?>

					<?php 
						$finalBill = $subTotal - $totalDiscount;
					?>
    
                    <!-- class="item last" -->
    
                    <tr class="total">
                        <td>Sub Total:</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td> {{ $subTotal }}</td>
                    </tr>

					<tr class="total">
                        <td>Discount:</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td> {{ $totalDiscount }}</td>
                    </tr>

					<tr class="total">
                        <td>Total:</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td> {{ $finalBill   }}</td>
                    </tr>
    
                    </table>


                </td>


                </tr>


                <tr> <td colspan="5" style="text-align:center" > Develop By TechBite @ 03134222632 </td> </tr>
				
			</table>
		</div>
	</body>
</html>




