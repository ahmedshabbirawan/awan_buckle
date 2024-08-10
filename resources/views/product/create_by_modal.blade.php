<div class="modal" id="add_product_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create Product</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form method="post" id="product_form_model" action="{{ isset($product) ? route('product.update', $product->id) : route('product.store') }}" novalidate class="form-horizontal product_form">
                @csrf
                <input type="hidden" name="id" value="{{ $productID }}">
                <div class="row">
                    <div class="col-xs-12 col-sm-12">
                      
                           

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
                                                        <div class="col-lg-10" style="width: 100%;" >
                                                            <select name="attribute_type[]" data-index="1"  data-placeholder="Select Variant" onchange="pos_app.getAttributeValue(this);" class=" form-control chosen-select" required>
                                                                <option value=""></option>
                                                                <?php foreach ($attribute_type as $key => $val) : ?>
                                                                    <option value="<?= $key ?>" <?= ($uomID == $key) ? 'selected="selected"' : '' ?>><?= $val ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        </td>
                                                        <td>
                                                        <div class="col-lg-10" style="width: 100%;" >
                                                            <select name="attribute_value[]" style="width: 100%;" id="val-1"  data-placeholder="Select Value" class="form-control " required>
                                                                <option value=""></option>
                                            
                                                            </select>
                                                        </div>
                                                        </td>
                                                        <td> <input type="text" value="" name="price_arr[]" id="price" placeholder="Price" class="col-xs-10 col-sm-5 form-control @error('price') is-invalid @enderror" > </td>
                                                        <td>&nbsp;</td>
                                                    </tr>


                                                    <tr>
                                                        <td class="center">2</td>
                                                        <td>
                                                        <div class="col-lg-12" style="width: 100%;" >
                                                            <select name="attribute_type[]" data-index="2"  data-placeholder="Select Variant" onchange="pos_app.getAttributeValue(this);" class=" form-control chosen-select" required>
                                                                <option value=""></option>
                                                                <?php foreach ($attribute_type as $key => $val) : ?>
                                                                    <option value="<?= $key ?>" <?= ($uomID == $key) ? 'selected="selected"' : '' ?>><?= $val ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        </td>
                                                        <td>
                                                        <div class="col-lg-10" style="width: 100%;" >
                                                            <select name="attribute_value[]" style="width: 100%;"  id="val-2" data-placeholder="Select Value" class="form-control " required>
                                                                <option value=""></option>
                                                            </select>
                                                        </div>
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
                           
   
                    </div><!-- /.span -->
                </div>
            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary add-product-btn" onclick="pos_app.saveProductWithForm();">Save Product</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


