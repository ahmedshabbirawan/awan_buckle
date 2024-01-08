@extends('Layout.master')

@section('title')
Return Items Detail
@endsection

@section('content')


<div class="page-content">


<div class="page-header" style="min-height:40px;">
    <div class="" style="float: left;">
        <h1>Return Items Detail</h1>
    </div>
</div>


<div id="return_tab" class="tab-pane in ">
									<form action="javascript:void(0);" id="item_return_form">
										<input type="hidden" id="emp_id" name="emp_id" value="{{ $row->id }}">
										<input type="hidden" id="emp_cnic" name="emp_cnic" value="{{ $row->cnic }}">
										<div class="row">
											<div class="col-lg-12">
												<div class="">

												<table width="100%" border="0">
													<tr class="" >
														<td width="120"><img src="{{asset('assets/images/logo/logo.jpg')}}" width="120" /></td>
														<td class="text-center">
													<h4 class="text-uppercase"><b>POS</b></h4>
													<h4><b>---</b></h4>
													<h5>------<br/>
													-------</h5>
														</td>
														<td width="120"><img src="{{asset('assets/images/logo/logo-icon.png')}}" width="120"/></td>
													</tr>
													<tr>
														<td colspan="3">&nbsp;</td>
													</tr>
													<tr class="">
														<td colspan="3" class="text-center"><h4><b>Return Items Form</b></h4></td>
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
													<tr class="hidden" >
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
													<tr class="" >
														<td colspan="3">
															<table width="100%" border="0">
																<tr>
																	<td width="150" colspan="3" class="text-uppercase"><b>Received BY</b>&nbsp;&nbsp;</td>
																	<td width="150" colspan="3" class="text-uppercase"><b>Return By</b></td>
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
										<div class="row clearfix form-actions no-print ">
										<div class="col-sm-2 text-left">
										<a href="{{ route('return.list') }}"  class="btn btn-xs btn-primary bigger hard_copy no-print"><i class="ace-icon fa fa-print"></i> Back </a>
										</div>
											<div class="col-sm-10 text-right">
												<a href="javascript:void(0);" onclick="form_print();" class="btn btn-xs btn-primary bigger hard_copy no-print"><i class="ace-icon fa fa-print"></i> Print Form </a>
												<a href="javascript:void(0);" onclick="submit_return_form();" class="hidden btn btn-xs btn-primary bigger hard_copy no-print"><i class="ace-icon fa fa-upload"></i> Submit Return Form </a>
											</div>
										</div><!-- /.row -->
										</div>
								</div>

</div>


<!----------------------------------------------------------------------------------------------------------------------------->
@endsection




@section('script')
<script>
    var table;
    var selectID;

    $(document).ready(function() {
        setTimeout(function(){

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
				ajax: "{{ route('issuance.list') }}?return_detail_id={{ $detail_id }}",
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
					// 0333 4287300
					// {
					// 	title: 'Select',
					// 	data: "active",
					// 	width: '5%',
					// 	render: function(data, type, row) {
					// 		if (type === 'display') {
					// 			return '<input type="checkbox" value="' + row.id + '" name="item_ids[]" class="item-checkbox editor-active">';
					// 		}
					// 		return data;
					// 	},
					// 	className: "dt-body-center"
					// },
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
					{
						data: 'return_condition_status_label',
						name: 'return_condition_status_label',
						title: 'Return Condition',
						width: '10%'
					},
					{
						data: 'id',
						name: 'id',
						title: 'Item Remarks',
						width: '30%',
						render: function(data, type, row, meta) {

                            return row.remarks;
							// nreturn '<textarea name="remarks_'+row.id+'" >'+row.remarks+'</textarea>';
							// return '<a href="javascript:void(0);" onclick="show_remarks_popup(' + row.id + ');" class="btn-success btn btn-xs" >Add Remarks</a>';
						}
					},
				],
				order: [
					[0, 'desc']
				]
			});

    },500);

    
    });


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
</script>
@endsection