<div class="modal" id="order_back_search_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Order Return</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form method="post" id="product_form_model" action="{{ isset($product) ? route('order.search_order') : route('simple_product.store') }}" novalidate class="form-horizontal product_form">
                @csrf
                <div class="row">     
                                    <div class="col-lg-12 col-sm-12">
                                        <label for="form-field-1"> Order ID / Invoice No: </label>
                                        <div class="">
                                            <input type="text" required class="form-control @error('order_id') is-invalid @enderror" value="" name="order_id" id="order_id" placeholder="Order ID / Invoice No:">
                                        </div>
                                    </div>

                                     <div style="clear:both;"></div>
                                     
                                    <div class="space"></div>
                                    <label for="or" class="text-center"> OR </label>
                                    <div class="space"></div>

                                    <div class="col-lg-12 col-sm-12">
                                        <label for="form-field-1"> Sale Key: </label>
                                        <div class="">
                                            <input type="text" required class="form-control @error('order_id') is-invalid @enderror" value="" name="sale_key" id="sale_key" placeholder="Sale Key">
                                        </div>
                                    </div>
                        
                                   

                                    <div style="clear:both;"></div>


                             


                  
                   
                </div>
            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary add-product-btn" onclick="pos_app.submitSearchOrderForm();">Search Order</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


