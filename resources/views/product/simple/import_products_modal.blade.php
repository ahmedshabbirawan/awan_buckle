<div class="modal" id="import_product_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Product Import</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">      
      <form method="post" id="import_products_form_model" action="{{ route('simple_product.save_import_products') }}" novalidate class="form-horizontal product_form">
                @csrf
                <div class="row">

                <div class="col-lg-12 col-sm-12">
          <label class="" for="form-field-1"> Shop</label>
          <div>
          <select name="shop_id" id="shop_id" class="chosen-select form-control select21" required style="width:100%" >
            <option value=""> -- Select -- </option>
            <?php foreach($shops as $id => $name): ?>
                <option value="<?=$id?>"><?=$name?></option>
            <?php endforeach; ?>
          </select>
          </div>
      </div>


                <div class="col-lg-12 col-sm-12">
                  <label style="font-size:11px;">Products CSV</label>
                  <input type="file" name="product_list_file" id="product_list_file" />
                </div>
                <div style="clear:both;"></div>
                </div>
            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary add-product-btn" onclick="pos_app.saveImportProductsForm();">Upload File</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>