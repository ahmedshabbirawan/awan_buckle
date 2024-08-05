<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px;">
        @include('Layout.alerts')
        <div class="card radius-10 border-top border-0 border-4 border-danger">
            <form method="post" action="{{ isset($attribute) ? route('attribute_type.update', $attribute->id) : route('attribute_type.store') }}" novalidate class="form-horizontal">
                @csrf
                <input type="hidden" name="id" value="{{ isset($attribute) ? $attribute->id : ''  }}">
                <div class="row">
                    <div class="col-xs-12 col-sm-12">
                        <div class="widget-box">
                            <div class="widget-header">
                                <h4 class="widget-title">Create</h4>
                            </div>
                            <div class="widget-body" style="display: block;">
                                <div class="widget-main">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Name </label>
                                        <div class="col-sm-9">
                                            <input type="text" class="col-xs-10 col-sm-5 form-control @error('name') is-invalid @enderror" value="{{old('name', (isset($attribute)) ? $attribute->name : '' )}}" name="name" id="name" placeholder="Name">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Status </label>
                                        <div class="col-sm-9">
                                            {{ \App\Util\Form::statusSelect(old('status', (isset($attribute)) ? $attribute->status : '')) }}
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
                                        <button class="btn" id="returnBack" onclick="javascript:history.back()">
                                            <i class="ace-icon fa fa-undo bigger-110"></i>
                                            Cancel / Back
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


@section('script')
<script>
    $(document).ready(function() {
     
    });
</script>
@endsection