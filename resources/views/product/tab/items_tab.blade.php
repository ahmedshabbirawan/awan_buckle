<div id="items-tab" class="tab-pane fade ">


<div class="row">
<div class="col-sm-12">
																	<div class="table-responsive">
																		<table class="table table-striped table-bordered yajratable" id="product_items_table" style="width:100%"></table>
																	</div>
																</div><!-- /.col -->
															</div><!-- /.row -->


</div>
@section('items_script')
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


	// historyTableReload

    $(document).ready(function() {
        console.log('I am return form!');
				setTimeout(function() {
			table = $('#product_items_table').DataTable({
				// iDisplayLength : -1,
		//		paging: false,
				ordering: false,
				info: false,
				searching: false,
				processing: true,
				serverSide: true,
				fnDrawCallback: function () {
var rows = this.fnGetData();
if ( rows.length === 0 ) {
	$('.dataTables_empty').attr('colspan',8);
}
},
				ajax: "{{ route('stocks.all_item.by_product') }}?product_id={{ $product_id }}",
				"fnCreatedRow": function(nRow, aData, iDataIndex) {
					$(nRow).attr('id', aData.id);
					$(nRow).addClass('item-row');
				},
				columns: [{
						"data": "id",
						title: 'Sr.',
						render: function(data, type, row, meta) {
							return meta.row + meta.settings._iDisplayStart + 1;
						}
					},
					{
						data: 'serial_number',
						name: 'serial_number',
						title: 'Serial Number',
					},
					{
						data: 'project_name',
						name: 'project_name',
						title: 'Project'
					},
					{
						data: 'employee_name',
						name: 'employee_name',
						title: 'Holding Employee',
					},
					{
						data: 'qty',
						name: 'qty',
						title: 'Quantity',
					},
					{
						data: 'expire_date',
						name: 'expire_date',
						title: 'Expire OR Warranty',
					},
					{
						data: 'condition_status_label',
						name: 'condition_status_label',
						title: 'Item Condition',
					},
					{ data: 'supplier_name', name: 'supplier_name', title: 'Supplier' },
					{ data: 'delivery_info', name: 'delivery_info', title: 'Delivery Info'},

					{
						"data": "action",
						"name" : "action",
						title: 'Action',
						
					},
					
				],
				order: [
					[0, 'desc']
				]
			});

			// <table class="table table-striped table-bordered"><tr><th colspan="6" style="padding: 5px 10px;">Employee Detail <input type="hidden" id="emp_id" name="emp_id" value=""> </th></tr> </table>
			$('#return-item-table_wrapper > .row:first-child').html('<strong id="item-title" >Issued Items</strong>');

		}, 500);

//-------------------------------------------------






    });
</script>
@endsection