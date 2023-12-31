<div id="shop-wise-stock-tab" class="tab-pane fade">
    <div class="row">
        <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered yajratable" id="shop_wise_stock_table" style="width:100%"></table>
                </div>
        </div><!-- /.col -->
    </div><!-- /.row -->
</div>
@section('shop_wise_stock_script')
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




    $(document).ready(function() {
       

//-------------------------------------------------


setTimeout(() => {
    


    shop_wise_stock_table = $('#shop_wise_stock_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('stocks.shop_wise_product_stock.list') }}?product_id={{ $product_id }}",
            columns: [
                //   {data: 'id', name: 'id', title : 'ID'},
                // shop_name
                {
                    "data": "id",
                    title: 'Sr.',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'product_name',
                    name:'product_name',
                    title: 'Product Name',
                    width:'10%'
                },
                {
                    data: 'shop_name',
                    name:'shop_name',
                    title: 'Shop Name',
                    width:'10%'
                },
                {
                    data: 'qty',
                    name:'qty',
                    title: 'Qty',
                    width:'10%'
                }
            ],
            // order: [[1, 'asc']]
        });

    }, 600);





    });
</script>
@endsection