@extends('Layout.master')

@section('title')
Supplier Detail
@endsection

@section('content')

<style>
    .profile-info-name {
        width: 190px;
    }

    .widget-box {
        margin: 0px 20px;
    }

    .profile-user-info {
        width: 100%;
    }

    .sn_tags{
        width: 150px;
        max-width: 250px;
        /* position: static!important;
    float: none!important;
    display: grid!important; */
    overflow-wrap: break-word;
    }

.sn_tags span{
    background-color: lightsalmon;
    margin: 2px;
    padding: 5px;
}
</style>
    <div class="page-content">
    <div class="page-header" style="min-height:40px;">
        <div class="" style="float: left;">
            <h1>Purchase Return Detail</h1>
        </div>
    </div>
    <div class="row ">
        

            <div class="card radius-10 border-top border-0 border-4 border-danger">
               
                <div class="row">
                    <div class="col-xs-12 col-sm-12">
                        <div class="widget-box">
                            <div class="widget-header">
                                <h4 class="widget-title">Detail</h4>
                            </div>
                            <div class="widget-body" style="display: block;">


                                <div class="row ">
                                <div class="col-xs-12 col-sm-7">
																	<div class="space visible-xs"></div>

																	<div class="profile-user-info profile-user-info-striped">
                                                                        <div class="profile-info-row">
																			<div class="profile-info-name"> Return-ID </div>

																			<div class="profile-info-value">
                                                                            <span>{{ $row->id }}</span>
																			</div>
																		</div>
																		<div class="profile-info-row">
																			<div class="profile-info-name"> Supplier </div>

																			<div class="profile-info-value">
																				<span>{{ ($supplier)?$supplier->name:'' }}</span>
																			</div>
																		</div>

																		<div class="profile-info-row">
																			<div class="profile-info-name"> Return Date </div>

																			<div class="profile-info-value">
																				<span>{{ $row->created_at }}</span>
																			</div>
																		</div>

                                                                        <div class="profile-info-row">
																			<div class="profile-info-name"> Total Return Amount </div>

																			<div class="profile-info-value">
																				<span>{{ $row->total_amount }}</span>
																			</div>
																		</div>


                                                                        <div class="profile-info-row">
																			<div class="profile-info-name"> Description </div>

																			<div class="profile-info-value">
																				<span>{{ $row->description }}</span>
																			</div>
																		</div>


																		<div class="profile-info-row">
																			<div class="profile-info-name"> Return By </div>

																			<div class="profile-info-value">
																				<span>N/A</span>
																			</div>
																		</div>

														
																	</div>
																</div>
                                </div>
                                

                            </div>
                        </div><!-- /.span -->
                    </div>
                    
                    <div class="col-xs-12 col-sm-12">
                    <div class="space" ></div>
                        <div class="widget-box">
                            <div class="widget-header">
                                <h4 class="widget-title">Items</h4>
                            </div>
                            <div class="widget-body" style="display: block;">

                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="center">#</th>
                                            <th>Product</th>
                                            <th>Shop</th>
                                            <th>Qty</th>
                                            <th>Unit Price</th>
                                            <th>Return Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $item)
                                        <tr>
                                            <th class="center">{{ $item->id }}</th>
                                            <th>{{ optional($item->product)->name }}</th>
                                            <th>{{ $item->shop_id }}</th>
                                            <th>{{ $item->qty }}</th>
                                            <th>{{ $item->unit_price }}</th>
                                            <th>{{ $item->item_description }}</th>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
    </div>
    </div>
<!--end row-->


<!-------------------------------------------------------------------------------------------------------------------------------->

<div class="modal fade bd-example-modal-xl" id="upload_document_modal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" style="width: 1000px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h5 class="modal-title" id="modal_title" ></h5>
      </div>
      <div class="modal-body" id="stock_itmes_container" style="padding: 2px;">


        <!-- form -->
        <form method="post" id="file_upload_form" action="" novalidate class="form-horizontal stock_item_form">
          <input type="hidden" name="stock_delivery_id" value="{{ $row->id }}">
          <input type="hidden" name="document_key" id="document_key" value="">

          <div class="widget-main">
            <div class="row">

              <!-- Parent Category -->
              <div class="col-lg-6 col-sm-12">
                <label class="" for="form-field-1"> File Upload</label>
                <div>
                <input type="file" name="document_file" id="document_file" />
                </div>
              </div>

        
              

            </div>
            <div class="space"></div>
           



            </div> <!-- ROW END -->

            <div class="space"></div>

         



          </div>
        </form>
        <!-- form -->
        
      </div>
      <div class="modal-footer">
        <button type="button" id="add_item_btn-" class="btn btn-primary" onclick="upload_document();">Upload Document</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-------------------------------------------------------------------------------------------------------------------------------->


@endsection

@section('javascript')
<script>
</script>
@endsection