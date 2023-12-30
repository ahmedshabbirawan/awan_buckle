<div id="return_tab" class="tab-pane in ">
									<form action="javascript:void(0);" id="item_return_form">
										<input type="hidden" id="emp_id" name="emp_id" value="{{ $row->id }}">
										<input type="hidden" id="emp_cnic" name="emp_cnic" value="{{ $row->cnic }}">
										<div class="row">
											<div class="col-lg-12">
												<div class="">

												<table width="100%" border="0">
													<tr>
														<td width="120"><img src="{{asset('assets/images/logo/logo.jpg')}}" width="120" /></td>
														<td class="text-center">
													<h4 class="text-uppercase"><b>Company</b></h4>
													<h4><b>Company</b></h4>
													<h5>Address<br/>
													Pakistan</h5>
														</td>
														<td width="120"><img src="{{asset('assets/images/logo/logo-icon.png')}}" width="120"/></td>
													</tr>
													<tr>
														<td colspan="3">&nbsp;</td>
													</tr>
													<tr>
														<td colspan="3" class="text-center"><h4><b>HANDING TAKING REQUISITION FORM</b></h4></td>
													</tr>
													<tr>
														<td colspan="3">&nbsp;</td>
													</tr>
													<tr>
														<td colspan="3">
															<table width="100%" border="0">
																<tr>
																	<td width="150"><b>Employee Code</b>&nbsp;&nbsp;</td>
																	<td width="5%"><b>:</b></td>
																	<td>{{ $row->emp_code }}</td>
																	<td width="100" class="text-right"><b>Date</b></td>
																	<td width="5%"><b>:</b></td>
																	<td width="100">2022-12-29</td>
																</tr>
																<tr>
																	<td width="150"><b>Employee Name</b>&nbsp;&nbsp;</td>
																	<td width="5%"><b>:</b></td>
																	<td colspan="4">{{ $row->full_name }}</td>
																</tr>
																<tr>
																	<td width="150"><b>Designation</b>&nbsp;&nbsp;</td>
																	<td width="5%"><b>:</b></td>
																	<td colspan="4">{{ $row->designation }}</td>
																</tr>
																<tr>
																	<td width="150"><b>Project / Department</b>&nbsp;&nbsp;</td>
																	<td width="5%"><b>:</b></td>
																	<td colspan="4">{{ $row->project }}</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td colspan="3">&nbsp;</td>
													</tr>
													<tr>
														<td colspan="3">
															<div class="row">
																<div class="col-sm-12">
																	<div class="table-responsive">
																		<table class="table table-striped table-bordered yajratable" id="return-item-table" style="width:100%"></table>
																	</div>
																</div><!-- /.col -->
															</div><!-- /.row -->
														</td>
													</tr>
													<tr>
														<td colspan="3">&nbsp;</td>
													</tr>
													<tr>
														<td colspan="3">
															<div class="col-lg-6 col-sm-12 no-print">
																<label class="" for="form-field-1"> Return Report </label>
																<div><input type="file" name="return_report_file" id="return_report_file" /></div>
															</div>
														</td>
													</tr>
													<tr>
														<td colspan="3">&nbsp;</td>
													</tr>
													<tr>
														<td colspan="3">
															<table width="100%" border="0">
																<tr>
																	<td width="150" colspan="3" class="text-uppercase"><b>Issued BY</b>&nbsp;&nbsp;</td>
																	<td width="150" colspan="3" class="text-uppercase"><b>Received By</b></td>
																</tr>
																<tr><td colspan="6">&nbsp;</td></tr>
																<tr>
																	<td width="150"><b>Name</b>&nbsp;&nbsp;</td>
																	<td width="5%"><b>:</b></td>
																	<td>{{Auth()->user()->name}}</td>
																	<td width="150"><b>Contact #</b></td>
																	<td width="3%"><b>:</b></td>
																	<td width="150">{{$row->mobile}}</td>
																</tr>
																<tr>
																	<td width="150"><b>Signature</b>&nbsp;&nbsp;</td>
																	<td width="5%"><b>:</b></td>
																	<td>______________________</td>
																	<td width="150"><b>CNIC</b></td>
																	<td width="3%"><b>:</b></td>
																	<td width="150">{{$row->cnic}}</td>
																</tr>
																<tr>
																	<td width="150"><b>&nbsp;</b>&nbsp;&nbsp;</td>
																	<td width="5%">&nbsp;</td>
																	<td>&nbsp;</td>
																	<td width="150"><b>Signature</b>&nbsp;&nbsp;</td>
																	<td width="3%"><b>:</b></td>
																	<td width="150">______________________</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>



												</div><!-- /.col -->
											</div>
										</div> <!-- /.row -->

										<div class="space-8"></div>
										<div class="widget-footer no-print">
										<div class="row clearfix form-actions no-print">
											<div class="col-sm-12 text-right">
												<a href="javascript:void(0);" onclick="form_print();" class="btn btn-xs btn-primary bigger hard_copy no-print"><i class="ace-icon fa fa-print"></i> Print Form </a>
												<a href="javascript:void(0);" onclick="submit_return_form();" class="btn btn-xs btn-primary bigger hard_copy no-print"><i class="ace-icon fa fa-upload"></i> Submit Return Form </a>
											</div>
										</div><!-- /.row -->
										</div>
								</div>
@section('return_form_script')
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



	function show_remarks_popup(id) {
		$('#issuance_item_id').val(id);
		$('#add-remark-popup').modal('show');
	}

	function submit_remarks() {
		let myform = document.getElementById("remarks_form");
		let fdata = new FormData(myform);
		$.ajax({
			data: fdata,
			cache: false,
			processData: false,
			contentType: false,
			type: 'POST',
			dataType: "JSON",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: "{{ route('issuance.submit_item_remarks') }}",
			success: function(res, textStatus, jqXHR) {
				$('#add-remark-popup').modal('hide');
				if (jqXHR.status == 200) {
					$.confirm({
						title: 'Alert',
						content: res.message
					});
				}
			},
			error: ajaxFailBlock
		});
	}


	function form_print() {
		$('#return_tab').printThis({
			importCSS: true,
			removeInlineSelector: "#hard_copy",
			loadCSS: "{{asset('assets/css/bootstrap.min.css')}}",
			removeInline: true,            // remove all inline styles from print elements
       		// removeInlineSelector: "body *", // custom selectors to filter inline styles. removeInline must be true
			beforePrint: function() {
				$('.item-row').hide();
				$('.no-print').hide();
				$($('.item-checkbox')).each(function(index, ele) {
					var id = $(this).val();
					if ($(this).is(':checked')) {
						console.log('aaa', id);
						$('#' + id).show();
					} else {
						$('#' + id).hide();
					}
				});
			}, // function called before iframe is filled
			afterPrint: function() {
				$('.item-row').show();
				$('.no-print').show();
			}
		});
	}





	function submit_return_form() {
		let myform = document.getElementById("item_return_form");
		let fdata = new FormData(myform);
		$.ajax({
			data: fdata,
			cache: false,
			processData: false,
			contentType: false,
			type: 'POST',
			dataType: "JSON",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: "{{ route('issuance.items-return') }}",
			success: function(res, textStatus, jqXHR) {
				if (jqXHR.status == 200) {
					$.confirm({
						title: 'Alert',
						content: res.message
					});
					window.location.reload();
				}
			},
			error: ajaxFailBlock
		});
	} // fun end


    $(document).ready(function() {
        console.log('I am return form!');
				setTimeout(function() {
			table = $('#return-item-table').DataTable({
				// iDisplayLength : -1,
				paging: false,
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
				ajax: "{{ route('issuance.list') }}?emp_id={{$row->id}}&returnable=1",
				"fnCreatedRow": function(nRow, aData, iDataIndex) {
					$(nRow).attr('id', aData.id);
					$(nRow).addClass('item-row');
				},
				columns: [{
						"data": "id",
						title: 'Sr.',
						width: '5%',
						render: function(data, type, row, meta) {
							return meta.row + meta.settings._iDisplayStart + 1;
						}
					},
					{
						title: 'Select',
						data: "active",
						width: '5%',
						render: function(data, type, row) {
							if (type === 'display') {
								return '<input type="checkbox" value="' + row.id + '" name="item_ids[]" class="item-checkbox editor-active">';
							}
							return data;
						},
						className: "dt-body-center"
					},
					{
						data: 'product_detail',
						name: 'product_detail',
						title: 'Item Detail',
						width: '20%'
					},

					{
						data: 'product_attribute',
						name: 'product_attribute',
						title: 'Item Attribute',
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
						width: '10%'
					},
					// item_condition
					{
						data: 'item_condition',
						name: 'item_condition',
						title: 'Item Condition',
						width: '10%'
					},
					{
						data: 'id',
						name: 'id',
						title: 'Item Remarks',
						width: '30%',
						render: function(data, type, row, meta) {
							return '<textarea name="remarks_'+row.id+'" >'+row.remarks+'</textarea>';
							// return '<a href="javascript:void(0);" onclick="show_remarks_popup(' + row.id + ');" class="btn-success btn btn-xs" >Add Remarks</a>';
						}
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

$('#return_report_file').ace_file_input({
      no_file: 'No File ...',
      btn_choose: 'Choose',
      btn_change: 'Change',
      droppable: false,
      onchange: null,
      thumbnail: false //| true | large
      //whitelist:'gif|png|jpg|jpeg'
      //blacklist:'exe|php'
      //onchange:''
      //
    });





    });
</script>
@endsection