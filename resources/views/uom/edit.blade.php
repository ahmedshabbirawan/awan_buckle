@extends('Layout.master')

@section('title')
{{ $name }}
@endsection

@section('content')

<div class="page-content">


<div class="page-header" style="min-height:40px;">
    <div class="" style="float: left;">
        <h1> Measure Units <small> <i class="ace-icon fa fa-angle-double-right"></i> List </h1>
    </div>
    <div class="" style="float: right;">
        
    </div>
</div>

<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px;">

        <div class="card radius-10 border-top border-0 border-4 border-danger">

        @include('Layout.alerts')

<form method="post" action="{{$adminURL.'/'.$row->id}}" novalidate class="form-horizontal">
<input type="hidden" name="_method" value="PUT">
                            @csrf
            <div class="row">

            


                <div class="col-xs-12 col-sm-12">
                    <div class="widget-box">
                        <div class="widget-header">
                            <h4 class="widget-title">Edit</h4>
                        </div>
                        <div class="widget-body" style="display: block;">
                            <div class="widget-main">

                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Name </label>
                                    <div class="col-sm-9">
                                        <input type="text" class="col-xs-10 col-sm-5 form-control @error('name') is-invalid @enderror" value="{{ (old('name'))?old('name'):$row->name }}" name="name" id="name" placeholder="Name">
                                        @error('name')<div class="alert alert-danger">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1">Short Code</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="col-xs-10 col-sm-5 form-control @error('code') is-invalid @enderror" value="{{ (old('code'))?old('code'):$row->code }}" name="code" id="code" placeholder="Short Code">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Status </label>
                                    <div class="col-sm-9">                                        
                                        {{ \App\Util\Form::statusSelect(old('status', (isset($project)) ? $project->status : '')) }}
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

    $(document).ready(function() {
        //   update_all_account_balance();
    });
</script>
@endsection