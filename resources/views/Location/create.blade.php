@extends('Layout.master')

@section('title')
{{ $name }}
@endsection

@section('content')


<div class="page-content">
<div class="page-header"><h1>Location</h1></div>

<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px;">

        <div class="card radius-10 border-top border-0 border-4 border-danger">



<form method="post" action="{{$adminURL}}" novalidate class="form-horizontal">
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
                                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Type </label>
                                    <div class="col-sm-9">
                                        {{ \App\Util\Form::select('type',$typeArr,old('type'), 'onchange=selectChild(this.value)') }}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Name </label>
                                    <div class="col-sm-9">
                                        <input type="text" class="col-xs-10 col-sm-5 form-control @error('name') is-invalid @enderror" value="{{old('name')}}" name="name" id="name" placeholder="Name">
                                    </div>
                                </div>


                                <div class="form-group" id="country_id_sel" >
                                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Country </label>
                                    <div class="col-sm-9">
                                        <select name="country_id" id="country_id" onchange="getProvince(this.value);" class="col-xs-10 col-sm-5 form-control">
                                            <option value="" >-- Select --</option>
                                            <?php foreach($countries as $co_val => $co_title): ?>
                                                <option value="<?=$co_val?>" ><?=$co_title?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        @error('type')<div class="alert alert-danger">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="form-group" id="province_id_sel" >
                                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Province </label>
                                    <div class="col-sm-9">
                                        <select name="province_id" id="province_id" class="col-xs-10 col-sm-5 form-control"   >
                                        </select>
                                        @error('type')<div class="alert alert-danger">{{ $message }}</div>@enderror
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Status </label>
                                    <div class="col-sm-9">
                                        {{ \App\Util\Form::statusSelect(old('status')) }}
                                        @error('status')<div class="alert alert-danger">{{ $message }}</div>@enderror
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

@section('script')
<script>
   


    function selectChild(type){
        if( type == 'CI' ){
            $('#province_id_sel').show();
            $('#country_id_sel').show();
        }else if(type == 'PR'){
            $('#province_id_sel').hide();
            $('#country_id_sel').show();
        }else if(type == 'CO'){
            $('#province_id_sel').hide();
            $('#country_id_sel').hide();
        }
    }


    function getProvince(country_id){
        var type = $('#type').val();
        if(type == 'CI'){
            $.ajax({
            // dataType: 'json',
            type:'get',
            url : "{{ url('ajax_view/get_province') }}?country_id="+country_id,
            success:function(res){
                $('#province_id').html(res);
                // console.log(res);
            }
        });
        }
        
    }





    $(document).ready(function() {
        //   update_all_account_balance();
        selectChild($('#type').val());
    });
</script>
@endsection