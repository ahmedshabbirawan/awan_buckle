<meta name="csrf-token" content="{{ csrf_token() }}" />
<?php
$categoryID         = '';
$subCategoryID      = '';
$productCategoryID  = '';
$name = '';
$productID = '';
$uomID = '';
$qty = '';
$attributeStr = '';
$actionURL =  route('product.store');
if (isset($product)) {
    $categoryID         = $categories['category']['id'];
    $subCategoryID      = $categories['sub_category']['id'];
    $productCategoryID  = $categories['product_category']['id'];
    $name = $product->name;
    $actionURL =  route('product.update', $product->id);
    $productID = $product->id;
    $uomID = $product->uom_id;
    $qty = $product->threshold_qty;
    $attributeStr = implode(',', $attributeIDs);
}
?>
<style>
    .chosen-container{
        display: inline;
    }
</style>
<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px; width:100%">
        @include('Layout.alerts')
        <div class="card radius-10 border-top border-0 border-4 border-danger">
            <form method="post" id="product_form" action="{{ isset($product) ? route('product.update', $product->id) : route('product.store') }}" novalidate class="form-horizontal product_form">
                @csrf
                <input type="hidden" name="id" value="{{ $productID }}">
                <div class="row">
                    <div class="col-xs-12 col-sm-12">
                        <div class="widget-box">
                            <div class="widget-header">
                                <h4 class="widget-title">{{ isset($product) ? 'Update' : 'Create' }}</h4>
                            </div>
                            <div class="widget-body" style="display: block;">
                                <div class="widget-main">
                                    <div class="col-lg-12 col-sm-12" style="float: unset;">
                                        <label for="form-field-1"> Product Name: </label>
                                        <div class="">
                                            <input type="text" required class="form-control @error('name') is-invalid @enderror" value="{{old('name', (isset($product))? $product->name : '' )}}" style="width:100%" name="name" id="name" placeholder="Name">
                                        </div>
                                    </div>
                                    <div style="clear:both;" class="space"></div>
                                    <div class="col-lg-12 col-sm-12" style="float: unset;">
                                        <label for="form-field-1"> Description/Specification: </label>
                                        <div class="">
                                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" id="form-field-8" placeholder="Description">{{old('description', (isset($product))? $product->description : '' ) }}</textarea>
                                        </div>
                                    </div>
                                    <div style="clear:both;" class="space"></div>
                                    <!--- category start ---->
                                    <div class="col-lg-4 col-sm-4">
                                        <label class="" for="form-field-1"> Category : </label>
                                        <div>
                                            <select name="sub_cat_id" id="sub_category_id" class="chosen-select form-control select21" required style="width:100%">
                                                <option> -- Select -- </option>
                                                <?php foreach ($sub_category as $cat) : ?><optgroup label="<?= $cat->name ?>">
                                                        <?php foreach ($cat->subCategories as $subCat) { ?> <option value="<?= $subCat->id ?>" <?= ($subCat->id == $subCategoryID) ? 'selected="selected"' : '' ?>><?= $subCat->name ?></option> <?php } ?>
                                                    </optgroup> <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-4">
                                        <label class="" for="form-field-1"> Product Category </label>
                                        <div>
                                            <select name="product_category_id" id="product_category_id" class="form-control select21" required>
                                                <option value=""> -- Select Product -- </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-4">
                                        <label class="" for="form-field-1"> UOM </label>
                                        <div class="">
                                            <select name="uom_id" id="uom_id" data-placeholder="Select UOM" class="col-xs-10 col-sm-5 form-control chosen-select" required>
                                                <option value=""></option>
                                                <?php foreach ($uoms as $key => $val) : ?>
                                                    <option value="<?= $key ?>" <?= ($uomID == $key) ? 'selected="selected"' : '' ?>><?= $val ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <!---------  category end  ----------->
                                    <div style="clear:both;" class="space"></div>
                                    <div class="col-lg-4 col-sm-4" style="display: none;">
                                        <label class="" for="form-field-1"> Threshold Quantity:</label>
                                        <div class="">
                                            <input type="text" class="col-xs-10 col-sm-5 form-control @error('threshold_qty') is-invalid @enderror" value="{{ old('threshold_qty') }}" name="threshold_qty" id="threshold_qty"  style="width:100%"  placeholder="Threshold Quantity">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <label style="font-size:11px;">Product Image</label>
                                        <input type="file" name="product_image" id="file_2" />
                                    </div>
                                    <div class="col-lg-4 col-sm-4">
                                        <label class="" for="form-field-1"> Status </label>
                                        <div class="">
                                            {{ \App\Util\Form::statusSelect(old('status')) }}
                                        </div>
                                    </div>
                                    <div style="clear:both;" class="space"></div>
                                    <div class="col-lg-12">
                                        <label style="font-size:14px;">Product Type</label>
                                        <div class="">
                                            <label class="form-check-label" for="single">
                                                <input class="form-check-input type_btns" type="radio"   name="product_type" value="single" id="single" checked > Single
                                            </label>
                                            <label class="form-check-label" for="space-type"> &nbsp; &nbsp; &nbsp; </label>
                                            <label class="form-check-label" for="variable">
                                                <input class="form-check-input type_btns" type="radio" name="product_type" value="variant" id="variable" >
                                                Variable
                                            </label>
                                        </div>
                                    </div>
                                    <!-------------  Product Variant Start  --------------->
                                    <div style="clear:both;" class="space"></div>
                                    <div class="row div_product_type col-lg-12" id="div_single">
                                        <div class="col-lg-4 col-sm-12">
                                            <label class="" for="form-field-1"> Price:</label>
                                            <div class="">
                                                <input type="text" name="price" id="price" placeholder="Price" class="col-xs-10 col-sm-12 form-control @error('price') is-invalid @enderror" value="{{ old('price',(isset($price))? $product->price : '') }}" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row div_product_type" style="display: none;"  id="div_variant" >
                                    <div class="col-lg-12">
                                        <h3 class="header smaller lighter blue">
                                            Product Variation
                                            <small style="float:right;"><a href="javascript:void(0);" onclick="addNewVariant();" >Add More Variant</a> </small>
                                        </h3>
                                        <div style="clear:both;"></div>
                                        <div class="col-lg-12">
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th class="center">#</th>
                                                        <th>Variant</th>
                                                        <th>Value</th>
                                                        <th>Sale Price</th>
                                                        <th>&nbsp;</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="variant_body">
                                                    <tr>
                                                        <td class="center">1</td>
                                                        <td>
                                                        <div class="col-lg-12" style="width: 100%;" >
                                                            <select name="attribute_type[]" data-index="1"  data-placeholder="Select Variant" onchange="getAttributeValue(this);" class=" form-control chosen-select" required>
                                                                <option value=""></option>
                                                                <?php foreach ($attribute_type as $key => $val) : ?>
                                                                    <option value="<?= $key ?>" <?= ($uomID == $key) ? 'selected="selected"' : '' ?>><?= $val ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        </td>
                                                        <td>
                                                            <select name="attribute_value[]" id="val-1"  data-placeholder="Select Value" class="col-xs-10 col-sm-5 form-control chosen-select" required>
                                                                <option value=""></option>
                                            
                                                            </select>
                                                        </td>
                                                        <td> <input type="text" value="" name="price_arr[]" id="price" placeholder="Price" class="col-xs-10 col-sm-5 form-control @error('price') is-invalid @enderror" > </td>
                                                        <td>&nbsp;</td>
                                                    </tr>


                                                    <tr>
                                                        <td class="center">2</td>
                                                        <td>
                                                        <div class="col-lg-12" style="width: 100%;" >
                                                            <select name="attribute_type[]" data-index="2"  data-placeholder="Select Variant" onchange="getAttributeValue(this);" class=" form-control chosen-select" required>
                                                                <option value=""></option>
                                                                <?php foreach ($attribute_type as $key => $val) : ?>
                                                                    <option value="<?= $key ?>" <?= ($uomID == $key) ? 'selected="selected"' : '' ?>><?= $val ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        </td>
                                                        <td>
                                                            <select name="attribute_value[]"  id="val-2" data-placeholder="Select Value" class="col-xs-10 col-sm-5 form-control chosen-select" required>
                                                                <option value=""></option>
                                                            </select>
                                                        </td>
                                                        <td> <input type="text" name="price_arr[]" id="price" class="col-xs-10 col-sm-5 form-control @error('price') is-invalid @enderror" value=""  placeholder="Price"> </td>
                                                        <td>&nbsp;</td>
                                                    </tr>


                                                </tbody>
                                            </table>
                                        </div>
                                        </div>
                                    </div>
                                    <div style="clear:both;" class="space"></div>
                                    <!-------------  Product Variant End  --------------->
                                </div>
                            </div>
                            <div class="widget-footer">
                                <div class="clearfix form-actions" style="margin-bottom:0px;">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button class="btn btn-info" onclick="checkValidation();" type="button">
                                            <i class="ace-icon fa fa-check bigger-110"></i>
                                            Submit
                                        </button>
                                        &nbsp; &nbsp; &nbsp;
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

    var selectHtml = '<?php foreach ($attribute_type as $key => $val) : ?><option value="<?= $key ?>" <?= ($uomID == $key) ? 'selected="selected"' : '' ?>><?= $val ?></option><?php endforeach; ?>';
    let validator;

    function getAttributeValue(attributeTypeEle){
        var attrTypeId = $(attributeTypeEle).val();
        var index = $(attributeTypeEle).data('index');
        
console.log(index);
        $.ajax({
            dataType: 'json',
            type: 'get',
            url: "{{ route('attribute_value.show','') }}/" + attrTypeId,
            success: function(res) {
                var html = '';
                $(res).each(function(i,ele){
                    html += '<option value="'+ele.id+'" >'+ele.value+'</option>';
                });
                $('#val-'+index).html(html);
                console.log('update',index);
                $('.chosen-select').chosen({
                    allow_single_deselect: true
                });

                $(".chosen-select").trigger("chosen:updated");
            }
        });

    }

    function getCategories() {
        var parentCat = $('#parent_category_id').val();
        if (parentCat == '') {
            $('#sub_country_id').html('<option value=""> -- Select -- </option>');
            return false;
        }
        $.ajax({
            // dataType: 'json',
            type: 'get',
            url: "{{ route('product.ajax_sub_cat') }}?parent_id=" + parentCat + "&selected_id=<?= $subCategoryID ?>",
            success: function(res) {
                $('#sub_category_id').html(res);
                $(".select21").trigger("chosen:updated");
                // var province_id = $('#province_id').val();
                // getProductCategories();
            }
        });
    }

    var variantIndex = 1;

    function addNewVariant(){
        var variantTr =  '<tr><td class="center">1</td>';
        variantTr += '<td><select name="attribute_type[]"  data-placeholder="Select Variant" class=" form-control chosen-select" required>'+selectHtml+'</select></td>';
        variantTr += '<td><select name="attribute_value[]"  data-placeholder="Select Value" class="col-xs-10 col-sm-5 form-control chosen-select" required></select></td>';                                   
        variantTr += '<td> <input type="text" class="col-xs-10 col-sm-5 form-control" name="price" id="price" placeholder="Price"> </td>';
        variantTr += '<td>&nbsp;</td></tr>';

        $('#variant_body').append(variantTr);

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
            url: "{{ route('product.ajax_product_cat') }}?sub_cat_id=" + subCategory + '&selected_id=<?= $productCategoryID ?>',
            success: function(res) {
                $('#product_category_id').html(res);
                // var province_id = $('#province_id').val();
                $(".select21").trigger("chosen:updated");
                //   getProductAttribute();
                <?php if ($subCategoryID != '') { ?>
                    setTimeout(function() {
                        getProductAttribute();
                    }, 200);
                <?php } ?>
            }
        });
    }

    // getProductAttribute

    function getProductAttribute() {


        var productCatID = $('#product_category_id').val();
        // if (productCatID == '') {
        //     // $('#sub_country_id').html('<option value=""> -- Select -- </option>');
        //     return false;
        // }
        $('#attr_heading').hide();
        $.ajax({
            // dataType: 'json',
            type: 'get',
            url: "{{ route('product.ajax_product_attribute') }}?product_cat_id=" + productCatID + "&selected_id=<?= $attributeStr ?>",
            success: function(res) {
                $('#product_attribute').html(res + '<div class="space"></div>');
                $('#attr_heading').show();
                // var province_id = $('#province_id').val();
                $(".select21").trigger("chosen:updated");
                validator.reload();
            }
        });
    }


    function checkValidation() {


        // const formElement = document.querySelector("#product_form");
        let myform = document.getElementById("product_form");
        let fdata = new FormData(myform);

        // var errorCount = validator.checkAll();
        // if (errorCount == 0) {
            if(true){
            $.ajax({
                data: fdata,
                cache: false,
                processData: false,
                contentType: false,
                type: 'POST',
                dataType: "JSON",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ $actionURL }}",
                success: function(res, textStatus, jqXHR) {
                    // console.log('=======>>>>> ',res);
                    if (jqXHR.status == 200) {
                        if (typeof res.data.product !== 'undefined') {
                            $.confirm({
                                title: 'Success',
                                content: res.message,
                                buttons: {
                                    yes: {
                                        text: 'OK',
                                        action: function() {
                                            window.location = "{{ route('product.create') }}";
                                        }
                                    }
                                }
                            });
                        }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // console.log(textStatus,jqXHR, errorThrown);
                    if (jqXHR.status != 200) {
                        if (typeof jqXHR.responseJSON !== 'undefined') {
                            $.confirm({
                                title: 'Error',
                                content: jqXHR.responseJSON.message
                            });
                        }
                    }
                }
            });
        }

        // console.log(validator.checkAll());
    }




    $(document).ready(function() {

        $('#file_2').ace_file_input({
            no_file: 'No File ...',
            btn_choose: 'Choose',
            btn_change: 'Change',
            droppable: false,
            onchange: null,
            thumbnail: false
        });


        validator = $('form.product_form').jbvalidator({
            allow_single_deselect: true,
            errorMessage: true,
            successClass: true,
            html5BrowserDefault: false,
            validFeedBackClass: 'valid-feedback',
            invalidFeedBackClass: 'invalid-feedback',
            validClass: 'is-valid',
            invalidClass: 'is-invalid'
        });



        $(document).on('change', '#sub_category_id', function() {
            getProductCategories();
        });

        $('.chosen-select').chosen({
            allow_single_deselect: true
        });


        // $(".chosen-select").chosen().change(function() {
        //     validator.checkAll();
        // });

        $(window)
            .off('resize.chosen')
            .on('resize.chosen', function() {
                $('.chosen-select').each(function() {
                    var $this = $(this);
                    $this.next().css({
                        'width': $this.parent().width()
                    });
                })
            }).trigger('resize.chosen');
        //resize chosen on sidebar collapse/expand
        $(document).on('settings.ace.chosen', function(e, event_name, event_val) {
            if (event_name != 'sidebar_collapsed') return;
            $('.chosen-select').each(function() {
                var $this = $(this);
                $this.next().css({
                    'width': $this.parent().width()
                });
            })
        });

        $('input[type=radio][name=product_type]').change(function() {
            $('.div_product_type').hide();
            $('#div_'+this.value).show();
            $('.chosen-select').chosen({
            allow_single_deselect: true
        });
        });


        //    getCategories();
        getProductCategories();
        getProductAttribute();

        $('.select21').chosen({
            allow_single_deselect: true
        });




    });
</script>
@endsection