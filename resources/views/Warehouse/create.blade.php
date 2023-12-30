@extends('Layout.master')

@section('title')
{{ $name }}
@endsection

@section('content')

<div class="page-content">
<div class="page-header"><h1>Warehouse</h1></div>


<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px;">

        <div class="card radius-10 border-top border-0 border-4 border-danger">

        @include('Layout.alerts')

<form method="post" action="{{ route('Settings.warehouse.store') }}" novalidate class="form-horizontal">
@csrf
            <div class="row">

            


                <div class="col-xs-12 col-sm-12">
                    <div class="widget-box">
                        <div class="widget-header">
                            <h4 class="widget-title">Create</h4>
                        </div>
                        <div class="widget-body" style="display: block;">
                            <div class="widget-main">


                            
                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Code </label>
                                    <div class="col-sm-9">
                                        <input type="text" class="col-xs-10 col-sm-5 form-control @error('code') is-invalid @enderror" value="{{old('code')}}" name="code" id="code" placeholder="code">
                                        
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Name </label>
                                    <div class="col-sm-9">
                                        <input type="text" class="col-xs-10 col-sm-5 form-control @error('name') is-invalid @enderror" value="{{old('name')}}" name="name" id="name" placeholder="Name">
                                        
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Address </label>
                                    <div class="col-sm-9">
                                    <textarea class="form-control  @error('address') is-invalid @enderror" name="address" id="address" placeholder="Address">{{old('address')}}</textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Status </label>
                                    <div class="col-sm-9">                                        
                                        {{ \App\Util\Form::statusSelect(old('status')) }}
                                    </div>
                                </div>




                            </div>
                        </div>
                        <div class="widget-footer">
                        <div class="clearfix form-actions" style="margin-bottom:0px;">
										<div class="col-md-offset-3 col-md-9">
											<button class="btn btn-info" type="submit">
												<i class="ace-icon fa fa-check bigger-110"></i>
												Submit
											</button>

											&nbsp; &nbsp; &nbsp;
											<button class="btn" type="reset">
												<i class="ace-icon fa fa-undo bigger-110"></i>
												Reset
											</button>
										</div>
									</div>
                        </div>

                    </div>
                </div><!-- /.span -->
            </div>
</form>




        </div>
    </div>
</div>
<!--end row-->

</div>
@endsection

@section('javascript')
<script>
    function deleteConfirmation(objID) {
        $('#delete_form').attr('action', '{{ $adminURL }}/' + objID);
        $('#deletePopup').modal('show');
    }

    $(document).ready(function() {
        //   update_all_account_balance();
    });
</script>
@endsection