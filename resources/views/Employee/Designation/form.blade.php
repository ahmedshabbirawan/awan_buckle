<style>
.input_div{ margin-top: 8px;}
</style>
<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px;">


    @include('Layout.alerts')

        <div class="card radius-10 border-top border-0 border-4 border-danger">



<form method="post" action="{{ isset($designation) ? route('Settings.designations.update', $designation->id) : route('Settings.designations.store') }}" novalidate class="form-horizontal">
@csrf
            <div class="row">

            


<div class="col-xs-12 col-sm-12">
    <div class="widget-box">
        <div class="widget-header">
            <h4 class="widget-title">{{ isset($employee) ? 'Update' : 'Create' }}</h4>
        </div>
        <div class="widget-body" style="display: block;">
            <div class="widget-main">

            <?php $colClass = 'col-lg-6 input_div'; ?>




                <div class="row">

            <div class="{{ $colClass }}">
                <label class="" for="form-field-1"> Name </label>
                
                    <input type="text" class="col-xs-10 col-sm-5 form-control @error('name') is-invalid @enderror" value="{{old('name', (isset($designation->name))? $designation->name : '' )}}" name="name" id="name" placeholder="Name">
            
            </div>





            <div class="{{ $colClass }}">
                <label class="" for="form-field-1"> Status </label>
                
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


@section('script')
<script>
</script>
@endsection