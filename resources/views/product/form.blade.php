<meta name="csrf-token" content="{{ csrf_token() }}" />


<?php
$categoryID         = '';
$subCategoryID      = '';
$productCategoryID  = '';
$name = '';
$productID = '';
$uomID = '';
$qty = '';

// print_r($attributeIDs); exit;

$attributeStr = '';

$actionURL =  route('product.store');

// echo $attributeStr; exit;

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

<?php

?>

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

                                    <div class="col-lg-4 col-sm-4">
                                        <label class="" for="form-field-1"> Category : </label>
                                        <div>
                                            <select name="sub_cat_id" id="sub_category_id" class="chosen-select form-control select21" required style="width:100%">
                                                <option> -- Select -- </option>
                                                <?php foreach ($sub_category as $cat) : ?><optgroup label="<?= $cat->name ?>">
                                                        <?php foreach ($cat->subCategories as $subCat) { ?> <option value="<?= $subCat->id ?>" <?= ($subCat->id == $subCategoryID)?'selected="selected"':'' ?>   ><?= $subCat->name ?></option> <?php } ?>
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
                                            <select name="uom_id" id="uom_id" data-placeholder="Select UOM" class="col-xs-10 col-sm-5 form-control chosen-select" required >
                                            <option value=""></option>
                                                <?php foreach ($uoms as $key => $val) : ?>
                                                    <option value="<?= $key ?>" <?=($uomID == $key)?'selected="selected"':''?> ><?= $val ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div style="clear:both;"></div>
                                    <div class="space"></div>

                                    <div class="col-lg-12 col-sm-12">
                                        <label for="form-field-1"> Product Name: </label>
                                        <div class="">
                                            <input type="text" required class="form-control @error('name') is-invalid @enderror" value="{{old('name', (isset($product))? $product->name : '' )}}" name="name" id="name" placeholder="Name">
                                        </div>
                                    </div>

                                    <div style="clear:both;"></div>
                                    <div class="space"></div>


                                    <div class="col-lg-12 col-sm-12">
                                        <label for="form-field-1"> Description/Specification: </label>
                                        <div class="">
                                           
                                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" id="form-field-8" placeholder="Description">{{old('description', (isset($product))? $product->description : '' ) }}</textarea>
                                        </div>
                                    </div>

                                

                                    <div style="clear:both;"></div>

                                    <!------    Product Attributes Start    ------------------>

                                    <div class="container" id="attr_heading" style="margin-left:0px; display:none;">
                                        <div class="row justify-content-center" style="border-bottom: solid 1px; border-color: lightgrey; margin:14px 0px;"><b>Attribute</b></div>
                                    </div>
                                    <div class="row">
                                        <div id="product_attribute" class="col-lg-12 col-sm-12">
                                        </div>
                                    </div>
                                    <div class="space"></div>
                                    <!------    Product Attributes End    ------------------>
                                    <div style="clear:both;"></div>






                                    <div class="col-lg-4 col-sm-4" style="display: none;">
                                        <label class="" for="form-field-1"> Threshold Quantity:</label>
                                        <div class="">
                                            <input type="text" class="col-xs-10 col-sm-5 form-control @error('threshold_qty') is-invalid @enderror" value="{{ old('threshold_qty') }}" name="threshold_qty" id="threshold_qty" placeholder="Threshold Quantity">
                                        </div>
                                    </div>


                                    <div class="col-lg-4">
                                        <label style="font-size:11px;">Product Image</label>
                                        <input type="file" name="product_image" id="file_2" />
                                      </div>

                                      <div class="col-lg-4 col-sm-4" >
                                        <label class="" for="form-field-1"> Price:</label>
                                        <div class="">
                                            <input type="text" class="col-xs-10 col-sm-5 form-control @error('price') is-invalid @enderror" value="{{ old('price',(isset($price))? $product->price : '') }}" name="price" id="price" placeholder="Price">
                                        </div>
                                    </div>




                                    <div class="col-lg-4 col-sm-4">
                                        <label class="" for="form-field-1"> Status </label>
                                        <div class="">
                                            {{ \App\Util\Form::statusSelect(old('status')) }}
                                        </div>
                                    </div>


                                    <div style="clear:both;"></div>


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
                                        <!-- <button class="btn" type="reset">
                                            <i class="ace-icon fa fa-undo bigger-110"></i>
                                            Reset
                                        </button> -->
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
            url: "{{ route('product.ajax_sub_cat') }}?parent_id=" + parentCat + "&selected_id=<?= $subCategoryID ?>",
            success: function(res) {
                $('#sub_category_id').html(res);
                $(".select21").trigger("chosen:updated");
                // var province_id = $('#province_id').val();
                // getProductCategories();
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
            url: "{{ route('product.ajax_product_cat') }}?sub_cat_id=" + subCategory + '&selected_id=<?= $productCategoryID ?>',
            success: function(res) {
                $('#product_category_id').html(res);
                // var province_id = $('#province_id').val();
                $(".select21").trigger("chosen:updated");
                //   getProductAttribute();
                <?php if($subCategoryID != ''){ ?>
        setTimeout(function(){
            getProductAttribute();
        },200);
        <?php } ?>
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

        var errorCount = validator.checkAll();
        if (errorCount == 0) {
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

        // $('#description').ace_wysiwyg();

//         $('#description').ace_wysiwyg({
//   hotKeys: {
//     'ctrl+b meta+b': 'bold',
//     'ctrl+i meta+i': 'italic',
//     'ctrl+u meta+u': 'underline',
//     'ctrl+z meta+z': 'undo',
//     'ctrl+y meta+y meta+shift+z': 'redo'
//   },
//   add_images:false,
//   insert_images:false,
//   wysiwygConfigData:{add_images:false, insert_images : false}
// });


$('#file_2').ace_file_input({
      no_file: 'No File ...',
      btn_choose: 'Choose',
      btn_change: 'Change',
      droppable: false,
      onchange: null,
      thumbnail: false //| true | large
      //whitelist:'gif|png|jpg|jpeg'
      //blacklist:'exe|php'
      //onchange:''
      //
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

        // $(document).on('change', '#product_category_id', function() {
        //     getProductAttribute();
        // });


        



        $('.chosen-select').chosen({
            allow_single_deselect: true
        });


        $(".chosen-select").chosen().change(function() {
            validator.checkAll();
        });

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


        //custom validate methode
        // validator.validator.custom = function(el, event){
        //     if($(el).is('[name=password]') && $(el).val().length < 5){
        //         return 'Your password is too weak.';
        //     }
        // }

        // validator.validator.example = function(el, event){
        //     if($(el).is('[name=username]') && $(el).val().length < 3){
        //         return 'Your username is too short.';
        //     }
        // }

        //check form without submit
        // validator.checkAll(); //return error count




        //    getCategories();
        getProductCategories();
        getProductAttribute();

        $('.select21').chosen({
            allow_single_deselect: true
        });



    });
</script>
@endsection