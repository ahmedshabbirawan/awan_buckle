@extends('Layout.master')

@section('title')
{{ $name }}
@endsection

@section('content')
<style>
	.profile-info-name {
		width: 190px;
	}

	#item-title {
		padding-left: 10px;
		font-weight: bolder;
	}
</style>





<!-------------------------------------------------------------------------------------------------------------------------------->

<div class="modal" tabindex="-1" role="dialog" id="add-remark-popup">
	<div class="modal-dialog" role="document">
		<div class="modal-content">


			<form action="#" method="post" id="remarks_form">

				@csrf
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h5 class="modal-title">Remarks</h5>

				</div>
				<div class="modal-body">
					<div class="row">


						<div class="col-lg-12 col-sm-12">
							<label class="" for="form-field-1"> Remarks</label>
							<div>
								<input type="hidden" name="issuance_item_id" id="issuance_item_id" value="">
								<textarea placeholder="Type remarks here....." name="remarks" class="form-control"></textarea>
							</div>
						</div>



						<div class="col-lg-12 col-sm-12">
							<div class="space-4"></div>
							<label class="" for="form-field-1"> Item Status</label>
							<div>


								<div class="radio">
									<label>
										<input name="condition_status" value="1" type="radio" class="ace">
										<span class="lbl"> Working Condition</span>
									</label>

									<label>
										<input name="condition_status" value="2" type="radio" class="ace">
										<span class="lbl"> Dead</span>
									</label>
								</div>

								<div class="radio">
									<label>
										<input name="condition_status" value="3" type="radio" class="ace">
										<span class="lbl"> Damaged</span>
									</label>

									<label>
										<input name="condition_status" value="4" type="radio" class="ace">
										<span class="lbl"> Faulty</span>
									</label>




								</div>


							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" onclick="submit_remarks();">Submit</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				</div>

			</form>

		</div>
	</div>
</div>

<!-------------------------------------------------------------------------------------------------------------------------------->


<div class="page-content">

	<div class="row ">
		<div class="col-12 col-lg-12" style="margin-top:20px;">
			<div class="card radius-10 border-top border-0 border-4 border-danger">
				<div class="card-body">
					<a href="{{$adminURL}}" class="btn btn-xs btn-light bigger"><i class="ace-icon "></i> Back </a>
				</div>
				<div class="card-body">
					<div id="user-profile-2" class="user-profile">
						<div class="tabbable">
							<ul class="nav nav-tabs padding-18">
								<li class="active"> <a data-toggle="tab" href="#home"> <i class="green ace-icon fa fa-user bigger-120"></i> Profile </a> </li>
								<li> <a data-toggle="tab" href="#feed"> <i class="orange ace-icon fa fa-rss bigger-120"></i> Inventory Activity </a> </li>
								@can('item_return.create')
								<li> <a data-toggle="tab" href="#return_tab"> <i class="orange ace-icon fa fa-undo bigger-120"></i> Return Form </a> </li>
								@endcan
							</ul>

							<div class="tab-content no-border padding-24">

								@include('Employee.profile_tab')
								@include('Employee.activities_tab')
								@include('Employee.return_form_tab')

								
								
								


								<!---------------------------------------------				RETURN FORM				----------------------------------------------------------------------->

								<!---------------------------------------------				RETURN FORM END		----------------------------------------------------------------------->
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--end row-->
	</div>
</div>




@endsection

@section('javascript')
<script>
	var table;
	var selectID;
</script>

@yield('profile_script')
@yield('activities_script')
@yield('return_form_script')

<script>


$(document).ready(function(){
	$('.dataTables_empty').attr('colspan',5);
});

</script>

@endsection