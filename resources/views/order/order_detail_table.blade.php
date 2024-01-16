<style>
	@if(!$hideInfo)
@page { size:  auto; margin: 0px !important; }
html  { background-color: #FFFFFF; margin: 0px; }
body{ margin : 0px;}
@endif
			.invoice-box {
				max-width: 800px;
				margin: auto;
				padding: 0px;
				border: 1px solid #eee;
				box-shadow: 0 0 0px rgba(0, 0, 0, 0.15);
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
				padding: 2px;
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
				border-bottom: 1px solid #333;
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

			.item td {
				border: 1px solid black;
  border-collapse: collapse;
  border-spacing:0;
			}

			.item_listing{
				border-spacing:0;
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

<div class="page-content">
	<div id="return_tab" class="tab-pane in ">
	<div class="invoice-box">
			<table cellpadding="0" cellspacing="0">
				<tr class="top">
					<td colspan="5">
						<table>

							<tr>
								<td colspan="4" style="padding-bottom:2px; text-align:center; " > <h1 style="padding: 0px; margin : 0px;">Awan Buckle</h1> </td>
							</tr>

							<tr>
								<td colspan="6" style="padding-bottom:2px; text-align:center;" >
									<span>Shop # 1-G Mustafa Center, Doctor Line Shoes Market Shah Alam Lahore.</span>
									<br/>
									<b> 0321-9999668 / 042-37659668</b> 
								</td>
								

								
							</tr>

							<tr>
								<td style="padding-bottom:2px; font-size:12px; width:35%" >Bill No : <b>{{ $order->id }} </b> </td> 
								<td colspan="3" style="text-align: right; padding-bottom:2px; font-size:12px;  width:80%" >Date: <b>{{ $order->created_at->format('d-m-Y / h:i a') }} </b> </td>
							</tr>

							<tr>
								<td colspan="4" style="padding-bottom: 2px; font-size:12px; height:12px;" >Customer : <b> <?php if($customer){ ?>
									{{ $customer->name }} / {{ $customer->mobile }}
								<?php }else{ ?>
									Walking Customer
								<?php } ?> </b> </td>
								</tr>
								<tr>
								<td  colspan="4" style="text-align: left; padding-bottom:2px;  font-size:12px; height:12px;" >Manager : @if($manager)
									 <b>{{ $manager->name }} </b>
									@endif</td>
								
							</tr>

						</table>
					</td>
				</tr>


			

                <tr>


                <td colspan="5" >



                <table class="item_listing">
                    
                    <tr class="heading item">
                        <td width="50%">Item</td>
                        <td>Price</td>
                        <td>Discount</td>
                        <td>Qty</td>
                        <td width="20%" >Total</td>
                    </tr>
    
                    <?php foreach($orderItem as $item): ?>
    
                    <tr class="item">
                        <td>{{ optional($item->product)->name }}</td>
                        <td>{{ $item->price }}</td>
                        <td>{{ $item->discount }}</td>
                        <td>{{ $item->qty }}</td>
                        <td>{{ ($item->offer_price - $item->discount) * $item->qty }}</td>
                    </tr>
                    <?php endforeach; ?>

					<tr class="total">
                        <td colspan="2" >Total Amount:</td>
                        <td></td>
						
                        <td></td>
                        <td style="text-align: right;"> <b>{{ $order->total_offer_price }} </b> </td>
                    </tr>

					<tr class="total">
                        <td>Discount:</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td> - {{ $order->total_discount }}</td>
                    </tr>

					<tr class="total">
                        <td> Payable :</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td> <b> {{ $order->total_price }} </b> </td>
                    </tr>

					


                    </table>
                </td>
                </tr>
                <tr> <td colspan="5" style="text-align:center; font-size: 10px;" > Develop By TechBite @ 03134222632 </td> </tr>	
			</table>
		</div>

	</div>

</div>
@if($viewType)
<script>
window.print();
</script>
@endif
<!----------------------------------------------------------------------------------------------------------------------------->