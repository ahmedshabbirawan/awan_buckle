@extends('Layout.master')

@section('title')
Order Detail
@endsection

@section('content')

<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px;">
        <div class="card radius-10 border-top border-0 border-4 border-danger">
        <div class="card-body">

		<div class="page-content">
	<div id="return_tab" class="tab-pane in ">
	<div class="invoice-box">
			<table cellpadding="0" cellspacing="0" class="table table-striped table-bordered table-hover">
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



                <table class="table table-striped table-bordered table-hover">
                    
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

		</div>
		
		</div>
	</div>
</div>

	
@endsection
@section('script')

@endsection