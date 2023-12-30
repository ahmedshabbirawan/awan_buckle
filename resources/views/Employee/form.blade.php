<style>
.input_div{ margin-top: 8px;}
</style>
<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px;">


    @include('Layout.alerts')

        <div class="card radius-10 border-top border-0 border-4 border-danger">



<form method="post" action="{{ isset($employee) ? route('Settings.employee.update', $employee->id) : route('Settings.employee.store') }}" novalidate class="form-horizontal">
@csrf

<input type="hidden" name="id" value="{{ (isset($employee))? $employee->id : '' }}" >
<input type="hidden" name="source" value="non-" >

            <div class="row">

            


<div class="col-xs-12 col-sm-12">
    <div class="widget-box">
        <div class="widget-header">
            <h4 class="widget-title">{{ isset($employee) ? 'Update' : 'Create' }}</h4>
        </div>
        <div class="widget-body" style="display: block;">
            <div class="widget-main">

            <?php $colClass = 'col-lg-4 input_div'; ?>




                <div class="row">

            <div class="{{ $colClass }}">
                <label class="" for="form-field-1"> First Name </label>
                
                    <input type="text" class="col-xs-10 col-sm-5 form-control @error('first_name') is-invalid @enderror" value="{{old('first_name', (isset($employee))? $employee->first_name : '' )}}" name="first_name" id="first_name" placeholder="First Name">
            
            </div>

            <div class="{{ $colClass }}">
                <label class="" for="form-field-1"> Last Name </label>
                
                    <input type="text" class="col-xs-10 col-sm-5 form-control @error('last_name') is-invalid @enderror" value="{{old('last_name', (isset($employee))? $employee->last_name : '' )}}" name="last_name" id="last_name" placeholder="First Name">
              
            </div>

            <div class="{{ $colClass }}">
                <label class="" for="form-field-1"> CNIC </label>
                    <input type="text" class="col-xs-10 col-sm-5 form-control @error('cnic') is-invalid @enderror" value="{{old('cnic',(isset($employee))? $employee->cnic : '' ) }}" name="cnic" id="cnic" placeholder="email">
            </div>
            


            <div class="{{ $colClass }}">
                <label class="" for="form-field-1"> Designation </label>
                <?php $desID = old('designation_id', (isset($employee))? $employee->designation : '' ); ?>
                <select name="designation_id" class="col-xs-10 col-sm-5 form-control @error('cnic') is-invalid @enderror" >
                <option value=""  > -- Select -- </option>
                    <?php foreach($designations as $id => $name): ?>
                        <option value="{{ $id }}" <?=($desID == $id)?'selected="selected"':''?> >{{ $name }}</option>
                    <?php endforeach; ?>

                </select>
                   
            </div>

            

            <div class="{{ $colClass }}">
                <label class="" for="form-field-1"> Email </label>
                
                    <input type="text" class="col-xs-10 col-sm-5 form-control @error('email') is-invalid @enderror" value="{{old('email',(isset($employee))? $employee->email : '' ) }}" name="email" id="email" placeholder="email">
              
            </div>


            <div class="{{ $colClass }}">
                <label class="" for="form-field-1"> Mobile </label>
                
                    <input type="text" class="col-xs-10 col-sm-5 form-control @error('mobile') is-invalid @enderror" value="{{old('mobile',(isset($employee))? $employee->mobile : '' )}}" name="mobile" id="mobile" placeholder="mobile">
               
            </div>

            <div class="{{ $colClass }}">
                <label class="" for="form-field-1"> Project</label>

                <?php $proID = old('project_id', (isset($employee))? $employee->project_id : '' ); ?>
                <select name="project_id" class="col-xs-10 col-sm-5 form-control @error('project_id') is-invalid @enderror" >
                <option value="#"  > -- Select -- </option>
                    <?php foreach($projects as $id => $name): ?>
                        <option value="{{ $id }}" <?=($proID == $id)?'selected="selected"':''?> >{{ $name }}</option>
                    <?php endforeach; ?>

                </select>
            </div>

            
            <div class="{{ $colClass }}">
                <label class="" for="form-field-1"> Address </label>
                    <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" id="form-field-8" placeholder="Address">{{old('address', (isset($supplier))? $supplier->address : '' ) }}</textarea>              
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



    function getProvince(country_id){
        var selected_id = "{{ old('province_id', (isset($supplier))? $supplier->province_id : '' ) }}";
        $.ajax({
            // dataType: 'json',
            type:'get',
            url : "{{ url('ajax_view/get_province') }}?country_id="+country_id+'&selected_id='+selected_id,
            success:function(res){
                $('#province_id').html(res);
                var province_id = $('#province_id').val();
                getCity(province_id);
            }
        });
    }

    function getCity(province_id){
        var selected_id = "{{ old('city_id', (isset($supplier))? $supplier->city_id : '') }}";
        $.ajax({
            // dataType: 'json',
            type:'get',
            url : "{{ url('ajax_view/get_city') }}?province_id="+province_id+'&selected_id='+selected_id,
            success:function(res){
                $('#city_id').html(res);
            }
        });
    }

    $(document).ready(function() {

        var country_id = $('#country_id').val();
        getProvince(country_id);


        $('#country_id').on().change(function(){
            var country_id = $('#country_id').val();
            getProvince(country_id);
        });


        $('#province_id').on().change(function(){
            var province_id = $('#province_id').val();
            getCity(province_id);
        });




    });
</script>
@endsection