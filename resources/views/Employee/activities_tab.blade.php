<div id="feed" class="tab-pane">
									<div class="profile-feed row">
										<div class="col-sm-12">
											<div class="table-responsive">
												
												<table class="table table-striped table-bordered yajratable" id="yajra-table" style="width:100%"></table>
											</div>
										</div><!-- /.col -->
									</div><!-- /.row -->
									<div class="space-12"></div>
								</div><!-- /#feed -->
@section('activities_script')
<script>

        

	$(document).ready(function() {
		setTimeout(function() {
			table = $('#yajra-table').DataTable({
				processing: true,
				serverSide: true,
				ajax: "{{ route('issuance.list') }}?emp_id={{$row->id}}",
				columns: [{
						"data": "id",
						title: 'Sr.',
						width: '5%',
						render: function(data, type, row, meta) {
							return meta.row + meta.settings._iDisplayStart + 1;
						}
					},
					{
						data: 'product_detail',
						name: 'product_detail',
						title: 'Product Name',
						width: '20%'
					},
					{
						data: 'serial_number',
						name: 'serial_number',
						title: 'Serial Number',
						width: '20%'
					},
					{
						data: 'qty',
						name: 'qty',
						title: 'Quantity',
						width: '10%'
					},
					{
						data: 'issue_date',
						name: 'issue_date',
						title: 'Issue Date',
						width: '20%'
					},
					{ data: 'return_date', name: 'return_date',title: 'Return Date',width: '20%' },
					{ data: 'return_receipt_file', name: 'return_receipt_file',title: 'Receipt File',width: '20%' },

				],
				order: [
					[0, 'desc']
				]
			});

		}, 500);
    });
</script>
@endsection