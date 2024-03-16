<div class="modal fade bd-example-modal-lg" id="change_user_current_shop_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update Shop</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form method="post" id="change_shop_form" action="{{ route('usermanagement.user_role.change_user_current_shop_save') }}" novalidate class="form-horizontal product_form">
                @csrf
                <div class="row" style="margin:0px 50px;">
                   

                <div class="form-group required">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Shop</label>
                <div class="col-sm-9">
                    <input type="hidden" class="col-xs-10 col-sm-5 form-control @error('name') is-invalid @enderror" 
                    value="{{old('name', (isset($customer))? $customer->name : '' )}}" name="name" id="name" placeholder="Name">

                    <select name="shop_id">
                        <option value="">No Shop</option>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->shop_id }}">{{ $shop->shop->name }}</option>
                        @endforeach
                    </select>

                </div>
            </div>

          


                </div>
            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="pos_app.saveChangeShopForm();">Confirm</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


