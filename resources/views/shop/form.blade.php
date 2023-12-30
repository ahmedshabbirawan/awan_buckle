<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px;">


    @include('Layout.alerts')

        <div class="card radius-10 border-top border-0 border-4 border-danger">



<form method="post" action="{{ isset($shop) ? route('shop.update', $shop->id) : route('shop.store') }}" novalidate class="form-horizontal">
@csrf
<input type="hidden" id="id" name="id" value="{{old('id', (isset($shop))? $shop->id : '' )}}" >
            <div class="row">

            


<div class="col-xs-12 col-sm-12">
    <div class="widget-box">
        <div class="widget-header">
            <h4 class="widget-title">{{ isset($shop) ? 'Update' : 'Create' }}</h4>
        </div>
        <div class="widget-body" style="display: block;">
            <div class="widget-main">

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Shop Name </label>
                <div class="col-sm-9">
                    <input type="text" class="col-xs-10 col-sm-5 form-control @error('name') is-invalid @enderror" value="{{old('name', (isset($shop))? $shop->name : '' )}}" name="name" id="name" placeholder="Name">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> NTN </label>
                <div class="col-sm-9">
                    <input type="text" class="col-xs-10 col-sm-5 form-control @error('ntn') is-invalid @enderror" value="{{old('ntn' , (isset($shop))? $shop->ntn : '' ) }}" name="ntn" id="ntn" placeholder="NTN">
                </div>
            </div>


            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Address </label>
                <div class="col-sm-9">
                    <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" id="form-field-8" placeholder="Address">{{old('address', (isset($shop))? $shop->address : '' ) }}</textarea>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Country </label>
                <div class="col-sm-9">
                    <select name="country_id" id="country_id" class="col-xs-10 col-sm-5 form-control">
                        <?php foreach($countries as $co_val => $co_title): ?>
                            <option value="<?=$co_val?>" ><?=$co_title?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Province </label>
                <div class="col-sm-9">
                    <select name="province_id" id="province_id" class="col-xs-10 col-sm-5 form-control">
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> City </label>
                <div class="col-sm-9">
                    <select name="city_id" id="city_id" class="col-xs-10 col-sm-5 form-control">
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Email </label>
                <div class="col-sm-9">
                    <input type="text" class="col-xs-10 col-sm-5 form-control @error('email') is-invalid @enderror" value="{{old('email',(isset($shop))? $shop->email : '' ) }}" name="email" id="email" placeholder="email">
                </div>
            </div>


            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Phone </label>
                <div class="col-sm-9">
                    <input type="text" class="col-xs-10 col-sm-5 form-control @error('phone') is-invalid @enderror" value="{{old('phone',(isset($shop))? $shop->phone : '' )}}" name="phone" id="phone" placeholder="phone">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Fax </label>
                <div class="col-sm-9">
                    <input type="text" class="col-xs-10 col-sm-5 form-control @error('fax') is-invalid @enderror" value="{{old('fax',(isset($shop))? $shop->fax : '' )}}" name="fax" id="fax" placeholder="fax">
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


@section('script')
<script>



    function getProvince(country_id){
        var selected_id = "{{ old('province_id', (isset($shop))? $shop->province_id : '' ) }}";
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
        var selected_id = "{{ old('city_id', (isset($shop))? $shop->city_id : '') }}";
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