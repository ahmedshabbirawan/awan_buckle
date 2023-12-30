<div class="row">
<div class="col-lg-3 col-sm-3">
    <label class="" for="form-field-1"> Parent Category </label>
    <div>
        <select name="parent_category_id" id="parent_category_id" class="form-control" required >
            <option value=""> -- Select -- </option>
            <?php foreach ($parentCat as $co_val => $co_title) : ?>
                <option value="<?= $co_val ?>"><?= $co_title ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="col-lg-3 col-sm-3">
    <label class="" for="form-field-1"> Category </label>
    <div>
        <select name="sub_category_id" id="sub_category_id" class="form-control" required >
            <option value=""> -- Select -- </option>
        </select>
    </div>
</div>

<div class="col-lg-3 col-sm-3">
    <label class="" for="form-field-1"> Product Category </label>
    <div>
        <select name="product_category_id" id="product_category_id" class="form-control" required >
            <option value=""> -- Select Product -- </option>
        </select>
    </div>
</div>




</div>


<!-- <div class="row" id="product_attribute" style="min-height: 50px; margin-top:10px" >

</div> -->


<div class="row">
<div class="col-lg-3 col-sm-3">
    <label class="" for="form-field-1"> &nbsp; </label>
    <div>
        <button type="button" class="btn btn-xs btn-dark bigger" onclick="apply_Filter();" >Apply Filter</button>
    </div>
</div>
</div>



@section('category_script')
<script>

let validator;

    function getCategories() {
        var parentCat = $('#parent_category_id').val();
        if (parentCat == '') {
            $('#sub_country_id').html('<option value=""> -- Select -- </option>');
            return false;
        }
        $.ajax({
            // dataType: 'json',
            type: 'get',
            url: "{{ route('product.ajax_sub_cat') }}?parent_id=" + parentCat,
            success: function(res) {
                $('#sub_category_id').html(res);
                // var province_id = $('#province_id').val();
            }
        });
    }

    function getProductCategories() {
        
        var subCategory = $('#sub_category_id').val();

        if (subCategory == '') {
            // $('#sub_country_id').html('<option value=""> -- Select -- </option>');
            return false;
        }
        $.ajax({
            // dataType: 'json',
            type: 'get',
            url: "{{ route('product.ajax_product_cat') }}?sub_cat_id=" + subCategory,
            success: function(res) {
                $('#product_category_id').html(res);
                // var province_id = $('#province_id').val();
            }
        });
    }

    // getProductAttribute

    function getProductAttribute() {
        var productCatID = $('#product_category_id').val();
        if (productCatID == '') {
            // $('#sub_country_id').html('<option value=""> -- Select -- </option>');
            return false;
        }
        $("#product_attribute").loading();
        $.ajax({
            // dataType: 'json',
            type: 'get',
            url: "{{ route('product.ajax_product_attribute') }}?product_cat_id=" + productCatID,
            success: function(res) {
                $("#product_attribute").loading('stop');
                $('#product_attribute').html(res);

                // var province_id = $('#province_id').val();
              //   validator.reload();
            },
            error:function(res){
                $("#product_attribute").loading('stop');
            }
        });
    }



    




    $(document).ready(function() {



    //     validator = $('form.product_form').jbvalidator({
    //         allow_single_deselect: true,
    //         errorMessage: true,
    //         successClass: true,
    // html5BrowserDefault: false,
    // validFeedBackClass: 'valid-feedback',
    // invalidFeedBackClass: 'invalid-feedback',
    // validClass: 'is-valid',
    // invalidClass: 'is-invalid'
    //     });


        $(document).on('change', '#parent_category_id', function() {
            getCategories();
        });

        $(document).on('change', '#sub_category_id', function() {
            getProductCategories();
        });

        $(document).on('change', '#product_category_id', function() {
            getProductAttribute();
        });

        $('.chosen-select').chosen({allow_single_deselect:true}); 


        $(".chosen-select").chosen().change(function(){
            validator.checkAll();
        });


    });
</script>
@endsection