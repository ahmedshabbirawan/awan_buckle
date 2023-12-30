<div id="suppliers-tab" class="tab-pane fade">
    <div class="row">
        <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered yajratable" id="supplier_table" style="width:100%"></table>
                </div>
        </div><!-- /.col -->
    </div><!-- /.row -->
</div>
@section('suppliers_script')
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
    


supplier_table = $('#supplier_table').DataTable({
            // lengthMenu: [
            //         [1, 2, 3, -1],
            //         [1, 2, 3, 'All'],
            //     ],
            // "aLengthMenu": [[1,5,10, 25, 50, 100, -1], [1,5,10, 25, 50, 100, "All"]],
            // 					"iDisplayLength": -1,
            // dom: 'Bfrtip',
            // buttons: ['excel'],
            processing: true,
            serverSide: true,
            ajax: "{{ route('supplier.list') }}",
            columns: [
                //   {data: 'id', name: 'id', title : 'ID'},
                {
                    "data": "id",
                    title: 'Sr.',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'name',
                 
                    title: 'Name',
                    render: function(data, type, row, meta) {
                        return row.name+' | '+ row.city_name;
                    }
                },
                {
                    data: 'ntn',
                    name: 'ntn',
                    title: 'NTN',
                    width: '10%'
                },
                {
                    data: 'phone',
                    name: 'phone',
                    title: 'phone',
                    width: '10%'
                },
                {
                    data: 'email',
                    name: 'email',
                    title: 'Email',
                    width: '10%'
                },
                {
                    data: 'status_label',
                    name: 'status',
                    title: 'Status'
                }
            ],
            // order: [[1, 'asc']]
        });

    }, 600);





    });
</script>
@endsection