<div class="modal" id="add_product_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create Product</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

      
      <form method="post" id="product_form_model" action="{{ isset($product) ? route('simple_product.update', $product->id) : route('simple_product.store') }}" novalidate class="form-horizontal product_form">
                @csrf
                <div class="row">
                   



                   

                                   
                                    <div class="col-lg-12 col-sm-12">
                                        <label for="form-field-1"> Product Name: </label>
                                        <div class="">
                                            <input type="text" required class="form-control @error('name') is-invalid @enderror" value="{{old('name', (isset($product))? $product->name : '' )}}" name="name" id="name" placeholder="Name">
                                        </div>
                                    </div>

                                    <!-- <div style="clear:both;"></div>
                                    <div class="space"></div> -->


                                    <div class="col-lg-12 col-sm-12">
                                        <label for="form-field-1"> Description/Specification: </label>
                                        <div class="">
                                           
                                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" id="form-field-8" placeholder="Description">{{old('description', (isset($product))? $product->description : '' ) }}</textarea>
                                        </div>
                                    </div>
                        
                                   

                                    <div class="col-lg-6 col-sm-6" >
                                        <label class="" for="form-field-1"> UOM</label>
                                        <div class="">
                                            <select name="uom_id" class="form-control">
                                                @foreach($uoms as $id => $name)
                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                            
                                        </div>
                                    </div>


                                    <div class="col-lg-6 col-sm-6" >
                                        <label style="font-size:11px;">Product Image</label>
                                        <input type="file" name="product_image" id="file_2" />
                                      </div>

                                      <div class="col-lg-6 col-sm-6" >
                                        <label class="" for="form-field-1"> Sale Price:</label>
                                        <div class="">
                                            <input type="text" class="col-xs-10 col-sm-5 form-control @error('price') is-invalid @enderror" value="{{ old('price',(isset($product->price))? $product->price : '') }}" name="price" id="price" placeholder="Price">
                                        </div>
                                    </div>




                                    <div class="col-lg-6 col-sm-6">
                                        <label class="" for="form-field-1"> Status </label>
                                        <div class="">
                                            {{ \App\Util\Form::statusSelect(old('status')) }}
                                        </div>
                                    </div>


                                    <div style="clear:both;"></div>


                             


                  
                   
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


