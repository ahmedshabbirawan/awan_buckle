<div id="deliveries-tab" class="tab-pane in active">

<div class="table-responsive">
    <table class="table table-striped table-bordered yajratable" id="deliveries-table" style="width:100%"></table>
</div>

</div>
@section('delivery_script')
<script>
	
    $(document).ready(function() {
       
				setTimeout(function() {


			$('#deliveries-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('stocks.delivery.list') }}?product_id={{ $product_id }}",
            columns: [
                {
                    "data": "id",
                    title: 'Sr.',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'info_no',
                    name: 'info_no',
                    title: 'Invoice & Challan & POI',
                    width: '20%'
                },
                {
                    data: 'project_name',
                    name: 'project_name',
                    title: 'Project',
                    width: '20%'
                },
                {
                    data:'supplier',
                    name:'supplier',
                    title:'Supplier'
                },
                {
                    data:'purchased_date',
                    name:'purchased_date',
                    title:'Purchase Date'
                },
                {
                    data:'create',
                    name:'create',
                    title:'Created at'
                },
                {
                    data: 'items',
                    name: 'items',
                    title: 'Item Count',
                    width: '10%'
                }
                // {
                //     data: 'action',
                //     name: 'action',
                //     title: 'Action'
                // }
            ],
        });


		}, 500);
    });
</script>
@endsection