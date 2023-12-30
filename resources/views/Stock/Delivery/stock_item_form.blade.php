<link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}" />
<style>

/* Tagging Basic Style */
.tagging {
	border: 1px solid #CCCCCC;
	font-size: 1em;
	height: auto;
	padding: 10px 10px 15px;
}

.tagging.editable {
	cursor: text;
}

.tag {
	background: none repeat scroll 0 0 #EE7407;
	border-radius: 2px;
	color: white;
	cursor: default;
	display: inline-block;
	position: relative;
	white-space: nowrap;
	padding: 4px 20px 4px 0;
	margin: 5px 10px 0 0;
}

.tag span {
	background: none repeat scroll 0 0 #D66806;
	border-radius: 2px 0 0 2px;
	margin-right: 5px;
	padding: 5px 10px 5px;
}

.tag .tag-i {
	color: white;
	cursor: pointer;
	font-size: 1.3em;
	height: 0;
	line-height: 0.1em;
	position: absolute;
	right: 5px;
	top: 0.7em;
	text-align: center;
	width: 10px;
}

.tag .tag-i:hover {
	color: black;
	text-decoration: underline;
}

.type-zone {
	border: 0 none;
	height: auto;
	width: auto;
	min-width: 20px;
	display: inline-block;
}

.type-zone:focus {
	outline: none;
}


.select2-container{
    display: initial;
}

</style>

        <form method="post" id="stock_item_form" action="" novalidate class="form-horizontal stock_item_form">
                <input type="hidden" name="id" value="">

                <div class="widget-main">


                    <div class="row" >
                        <!-- Parent Category -->
                    


                        <!-- Parent Category -->
                        <div class="col-lg-4 col-sm-4">
                        <label class="" for="form-field-1"> Category : </label>
                        <div>
                        <select name="sub_category_id" id="sub_category_id" class="form-control select2" required >

                        <option> -- Select -- </option>

                        <?php foreach($sub_category as $cat): ?>
                            <optgroup label="<?=$cat->name?>">
                                <?php foreach($cat->subCategories as $subCat){ ?>
                                <option value="<?=$subCat->id?>"><?=$subCat->name?></option>
                                <?php } ?>
                            </optgroup>
                        <?php endforeach; ?>

                        </select>
                        </div>
                        </div>

                        <!-- Parent Category -->
                        <div class="col-lg-4 col-sm-4">
                        <label class="" for="form-field-1"> Product Category : </label>
                        <div>
                        <select name="product_category_id" id="product_category_id" class="form-control select2" required >
                            <option > -- Select -- </option>
                        </select>
                        </div>
                        </div>

                        <div class="col-lg-4 col-sm-4">
    <label class="" for="form-field-1" > Unit Price </label>
    <div>
    <input type="text" required class="form-control " value="" name="unit_price" id="unit_price" placeholder="Unit Price">
    </div>
</div>

                    </div>




<!------    Product Attributes Start    ------------------>
<div class="container" style="padding-top: 10px;" >
    <div class="row justify-content-center" ><b></b></div>
</div>
<div class="row" id="product_attribute" ></div>
<!------    Product Attributes End    ------------------>


<div class="row" id="stock_info" >

<div class="col-lg-4 col-sm-4">
    <label class="" for="form-field-1"> UOM : </label>
    <div>
    <select name="uom_id" id="uom_id" class="form-control" required>
    <!-- <option > -- Select -- </option> -->
    <?php foreach($uoms as $key => $val): ?>
                <option value="<?=$key?>"><?=$val?></option>
            <?php endforeach; ?>
    </select>
    </div>
</div>


<div class="col-lg-4 col-sm-4">
    <label class="" for="form-field-1" > Quantity </label>
    <div>
    <input type="text" required class="form-control " value="" name="qty" id="qty" placeholder="Quantity">
    </div>
</div>





<div class="col-lg-4 col-sm-4">
    <label class="" for="form-field-1" > Warranty / Expiry Date  </label>
    <div class="input-group" >
    <span class="input-group-addon">
    <label>
                          <input type="checkbox" class="" name="is_expiry_date" id="is_expiry_date" value="yes" checked="checked" >
                          
                        </label>
                      </span>
    <input type="text"  class="form-control input_date"   name="warranty_date" id="warranty_date" placeholder="Date">
    </div>
</div>






</div> <!-- ROW END -->


<div class="space"></div>

<div class="row">

<div class="control-group">

                      <div class="checkbox" style="display: none;">
                        <label>
                          <input name="is_serial_require" id="is_serial_require" type="hidden" class="ace">
                          <span class="lbl"> If Serial Number require</span>
                        </label>
                      </div>

                    </div>

</div>
<div class="space"></div>
<div class="row" id="serial_number_div" style="display: none;">



<div class="col-lg-4 col-sm-4">
    <label class="" for="form-field-1" > Serial Numbers File <a href="{{ route('stocks.dp.download') }}"> Download Sample </a></label>
    <div>
    <input type="file" required class="form-control" value="" name="serial_number_file" id="serial_number_file" placeholder="serial_number_file">
    </div>
</div>

<div class="col-lg-8 col-sm-8">
    <label class="" for="form-field-1" > Serial Numbers </label>
    <div>

    <div class="tagging-js" data-tags-input-name="sn" id="input_zone"></div>

<!-- <input type="text" required class="form-control" value="" name="serial_number" id="serial_number" data-provide="tag" placeholder="Serial Numbers"> -->
    

</div>
</div>

</div>






                </div>

        </form>