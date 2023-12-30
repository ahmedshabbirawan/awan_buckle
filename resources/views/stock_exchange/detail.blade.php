@extends('Layout.master')

@section('title')
Stock Exchange Detail
@endsection

@section('content')


<div class="page-content">


<div class="page-header" style="min-height:40px;">
    <div class="" style="float: left;">
        <h1>Stock Exchange Detail</h1>
    </div>
</div>


<div id="return_tab" class="tab-pane in ">
									
									
										<div class="row">
											<div class="col-lg-12">
												<div class="">

										
<!---------------------------------------------------------------------------------------------------------------------------------------->
{{-- <td class="hidden-480"> --}}

	
<form action="javascript:void(0);" id="customer_form" method="post">
	<input type="hidden" name="exchange_key" value="{{ $exchange_key }}" >
<table id="simple-table" class="table  table-bordered table-hover">
	<thead>
		<tr>
			<th>Sr</th>
			<th>Product</th>
			<th>Available</th>
			<th>Require</th>
			<th>Send</th>
		</tr>
	</thead>

	<tbody>


		<?php 
			$index = 0;
			foreach($results as $row ){ 
				$index++;
			?>


		<tr>
			<td>
				{{ $index }}
				<input type="hidden" name="product_id[]" value="{{ $row->product_id }}" >
				<input type="hidden" name="req_id[]" value="{{ $row->id }}" > 
			</td>

			<td>
				{{ $row->product->name }}
			</td>
			<td>
				{{ optional($row->productAvailableShopWise)->qty }}
			</td>


			<td>
				{{ $row->req_qty }}
			</td>

			<td>
				<?php if($row->status == 'send'){ ?>
					<input type="text" name="send_qty[]" value="{{ $row->req_qty }}" >
				<?php }else{ ?>
					{{ $row->req_qty }}
				<?php } ?>
			</td>

		
		</tr>


		<?php 

	} ?>

	</tbody>
</table>
</form>
<!---------------------------------------------------------------------------------------------------------------------------------------->


												</div><!-- /.col -->
											</div>
										</div> <!-- /.row -->

										<div class="space-8"></div>
										<div class="widget-footer no-print">
										<div class="row clearfix form-actions no-print ">
										<div class="col-sm-2 text-left">
										<a href="{{ route('return.list') }}"  class="btn btn-xs btn-primary bigger hard_copy no-print"><i class="ace-icon fa fa-print"></i> Back </a>
										</div>
											<div class="col-sm-10 text-right">
												
												<a href="javascript:void(0);" onclick="submit_exchange_form();" class="btn btn-xs btn-primary bigger"><i class="ace-icon fa fa-upload"></i> Approve Exchange Request </a>
											</div>
										</div><!-- /.row -->
										</div>
								</div>

</div>


<!----------------------------------------------------------------------------------------------------------------------------->
@endsection
@section('script')
<script>


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
  	

		function submit_exchange_form(){

var formData = new FormData($('#customer_form')[0]);

console.log(formData);

// var saleKey = $('#sale_key').val();   
$.ajax({
		dataType: 'json',
		type: 'POST',
		data: formData,
		url:  "{{ route('stock_exchange.req_aprove') }}",
		contentType: false,
		processData: false,
		headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		error:ajaxFailBlock,
		success: function(res) {
				$.confirm({
						title: 'Response',
						content: res.message,
						buttons: {
								yes: {
										text: 'OK',
										action: function() {
												location.reload();
												// cartDataTable.ajax.reload();
										}
								}
						}
				});
		}
});
}


</script>
@endsection