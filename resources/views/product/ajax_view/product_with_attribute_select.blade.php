    <div class="col-lg-12 col-sm-12">
        <label class="" for="form-field-1"> Product </label>
        <div class="">
            <select name="product_id" id="product_id" required class="col-xs-10 col-sm-5 form-control product_id chosen_select select21" onchange="select_product(this);" >
                <option value=""> -- Select -- </option>
                <?php foreach($products as $pro): ?>
                    <option value="<?=$pro->id?>" data-cost-price="<?=$pro->cost_price?>" data-qty="{{ optional($pro->productQtyShopWise)->qty }}" data-uom-id="<?=$pro->uom_id?>" data-uom-code="<?=$pro->code?>"  ><?=$pro->name?> {{ (strip_tags($pro->attributeHTML()) != '') ? '('. strip_tags($pro->attributeHTML()). ')' : '' }}  </option>
                <?php endforeach;?>
            </select>
        </div>
    </div>