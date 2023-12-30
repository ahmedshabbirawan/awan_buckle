<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px;">


    @include('Layout.alerts')

        <div class="card radius-10 border-top border-0 border-4 border-danger">


        <form method="post" action="{{ isset($project) ? route('Settings.project.update', $project->id) : route('Settings.project.store') }}" novalidate class="form-horizontal">

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
            <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Organization </label>
            <div class="col-sm-9">
                {{ App\Util\Form::select('org_id',$org->toArray(),old('org_id',  (isset($project)) ? $project->org_id : '' ) ) }}
        
            </div>
        </div>
                            

        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Name </label>
            <div class="col-sm-9">
                <input type="text" class="col-xs-10 col-sm-5 form-control @error('name') is-invalid @enderror" value="{{old('name', (isset($project)) ? $project->name : '' )}}" name="name" id="name" placeholder="Name">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Code </label>
            <div class="col-sm-9">
                <input type="text" class="col-xs-10 col-sm-5 form-control @error('code') is-invalid @enderror" value="{{old('code', (isset($project)) ? $project->code : '' )}}" name="code" id="code" placeholder="Code">
            </div>
        </div>


        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> DG / ADG </label>
            <div class="col-sm-9">
           
            <select name="manager_id" id="manager_id" class="col-xs-10 col-sm-5 form-control" >
                <option value=""> -- Select -- </option>
            </select>
        
            </div>
        </div>

        

        
<!-- 
        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> DG / ADG </label>
            <div class="col-sm-9">
                <input type="text" class="col-xs-10 col-sm-5 form-control @error('dg') is-invalid @enderror" value="{{old('dg', (isset($project)) ? $project->dg : '' )}}" name="dg" id="dg" placeholder="DG / ADG">  
            </div>
        </div> -->

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


@section('script')
<script>



    function get_manager(org_id){
        var selected_id = "{{ old('manager_id', (isset($project))? $project->manager_id : '' ) }}";
        $.ajax({
            // dataType: 'json',
            type:'get',
            url : "{{ url('ajax_view/get_manager_by_organization') }}?org_id="+org_id+'&selected_id='+selected_id,
            success:function(res){
                $('#manager_id').html(res);
            }
        });
    }

    // get_manager


    $(document).ready(function() {

        // var country_id = $('#country_id').val();
        // getProvince(country_id);


        $('#org_id').on().change(function(){
            var org_id = $('#org_id').val();
            get_manager(org_id);
        });


        <?php if(isset($project)){ ?>
            var org_id = $('#org_id').val();
            get_manager(org_id);
        <?php } ?>

    });

   


</script>
@endsection