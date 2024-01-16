@extends('Layout.master')

@section('title')
Order Detail
@endsection

@section('content')

<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px;">
        <div class="card radius-10 border-top border-0 border-4 border-danger">
        <div class="card-body">

		@include('order.order_detail_table',['hideInfo' => true])
		</div>
		<div class="card-footer">
			<a href="{{ route('sale.order.detail',$order->id) }}?view=print" target="_blank" class="btn btn-primary btn-block">Print</a>
		</div>
		</div>
	</div>
</div>

	
@endsection
@section('script')

@endsection