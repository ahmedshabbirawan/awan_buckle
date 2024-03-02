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
            <h1>Purchase Detail</h1>
        </div>
        <div class="hard_copy" style="float: right;">


        <a href="javascript:void(0);" onclick="upload_document_popup('purchase_order');" class="btn btn-xs btn-light bigger hard_copy"><i class="ace-icon fa fa-upload"></i> Purchase Order </a> 
        <a href="javascript:void(0);" onclick="upload_document_popup('delivery_challan');" class="btn btn-xs btn-light bigger hard_copy"><i class="ace-icon fa fa-upload"></i> Delivery Challan & Invoice </a> 
        <a href="javascript:void(0);" onclick="upload_document_popup('inspection_report');" class="btn btn-xs btn-light bigger hard_copy"><i class="ace-icon fa fa-upload"></i> Inspection Report </a> 


            <a href="javascript:void(0);" onclick="form_print();" class="btn btn-xs btn-light bigger hard_copy"><i class="ace-icon fa fa-print"></i> Print Form </a> 
            <a href="{{ route('stocks.delivery.list') }}" class="btn btn-xs btn-light bigger hard_copy"><i class="ace-icon "></i> Back </a>
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
                                    <div class="col-xs-6 col-sm-6 ">
                                        <div class="profile-user-info profile-user-info-striped">

                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Project </div>
                                                <div class="profile-info-value">
                                                    <span class="editable editable-click" id="username">{{ optional($project)->name }}</span>
                                                </div>
                                            </div>
                                            
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Supplier </div>
                                                <div class="profile-info-value">
                                                    <span class="editable editable-click" id="username">{{ optional($row->supplier)->name }}</span>
                                                </div>
                                            </div>
                                            
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Receive By </div>
                                                <div class="profile-info-value">
                                                    <span class="editable editable-click" id="username">
                                                        {{ 'Name : ' . $row->rec_by_name }} <br> {{ 'Designation : ' . $row->rec_by_designation }} <br>   {{ 'Cell : ' . $row->rec_by_phone }}
                                                    </span>
                                                </div>
                                            </div>


                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Purchase Date </div>

                                                <div class="profile-info-value">
                                                    <span class="editable editable-click" id="username">{{ $row->purchased_date }}</span>
                                                </div>
                                            </div>



                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> POI </div>

                                                <div class="profile-info-value">
                                                    <span class="editable editable-click" id="username">{{ $row->po_loa_loi }}</span>
                                                </div>
                                            </div>


                                            <?php if($row->purschase_copy_file != ''){ ?>
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Purchase Copy  </div>

                                                <div class="profile-info-value">
                                                    <span class="editable editable-click" id="username">{{ $row->purschase_copy_file }}</span>
                                                </div>
                                            </div>
                                            <?php } ?>

                                            
                                        </div>
                                    </div>
                                    <div class="col-xs-6 col-sm-6">
                                        <div class="profile-user-info profile-user-info-striped">

                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> DG </div>
                                                <div class="profile-info-value">
                                                    <span class="editable editable-click" id="username">{{ optional(optional($project)->manager)->name }}</span>
                                                </div>
                                            </div>
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Delivery Amount </div>
                                                <div class="profile-info-value">
                                                    <span class="editable editable-click" id="username">{{ $row->delivery_amount }}</span>
                                                </div>
                                            </div>
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Handed Over By </div>

                                                <div class="profile-info-value">
                                                    <span class="editable editable-click" id="username">
                                                        <b>{{ 'Name : ' . $row->hand_name }} <br> {{ 'Designation : ' . $row->hand_designation }} <br> {{ 'Cell : ' . $row->hand_phone; }} </b></span>
                                                </div>
                                            </div>
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Created At </div>
                                                <div class="profile-info-value">
                                                    <span class="editable editable-click" id="username">{{ $row->created_at }}</span>
                                                </div>
                                            </div>
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Challan # </div>

                                                <div class="profile-info-value">
                                                    <span class="editable editable-click" id="username">{{ $row->delivery_challan_no }}</span>
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
                                            <th style="width:20%" >Product</th>
                                            <th style="width:20%" >Shop</th>
                                            <th style="width:5%" >Attribute</th>
                                            
                                            <th >Qty</th>
                                            <th >UOM</th>
                                            <th >Unit Price</th>
                                            <th class="hidden-480">Total Price</th>
                                            <th>Serial Numbers</th>
                                        </tr>
                                    </thead>
                                    <tbody id="product_list"></tbody>
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
      var ajaxFailBlock = function(jqXHR, textStatus, errorThrown) {
    // console.log(textStatus,jqXHR, errorThrown);
    $('#submit-delivery').removeAttr('disabled');
    if (jqXHR.status != 422) {
      $.confirm({
        title: 'Warning',
        content: jqXHR.responseJSON.message
      });
    } else if (jqXHR.status != 200) {
      if (typeof jqXHR.responseJSON !== 'undefined') {
        $.confirm({
          title: 'warning',
          content: jqXHR.responseJSON.message
        });
      }
    }
  }


    function form_print(){
        $('.page-content').printThis({
            importCSS: true,
            removeInlineSelector:"#hard_copy",
            beforePrint: function(){
                $('.hard_copy').hide();
            },          // function called before iframe is filled
            afterPrint: function(){
                $('.hard_copy').show();
            } 
        });
    }

    function show_serials(deliveryID) {
        $.ajax({
            dataType: 'json',
            type: 'get',
            url: "{{ route('stocks.dp.stock_item_by_product','') }}/" + deliveryID,
            success: function(res) {
                var html = '';
                var sr = 1;
                if (res.length > 0) {
                    $(res).each(function(index, ele) {
                        // ('+ ele.qty +')
                        html += '<b>' + sr + ' : ' + ele.serial_number + '  </b> <br>';
                        sr++;
                    });

                    console.log('---', html);

                    $.confirm({
                        title: 'Serail Number(s)',
                        content: html,
                        buttons: {
                            no: {
                                text: 'OK', // With spaces and symbols
                            }
                        }
                    });



                }

            }
        });
    }


    function upload_document_popup(key){
        var title = '';
        if(key == 'purchase_order'){
            title = 'Upload Purchase Order';
        }else if(key == 'delivery_challan'){
            title = 'Upload Delivery Challan & Invoice';
        }else if(key == 'inspection_report'){
            title = 'Upload Inspection Report / Satisfactory Certificate';
        }

        $('#document_key').val(key);

        $('#modal_title').html(title);
        $('#upload_document_modal').modal('show');
    }

    // file_upload_form upload_document


    function upload_document(key){
        
         // const formElement = document.querySelector("#product_form");
    let myform = document.getElementById("file_upload_form");
    let fdata = new FormData(myform);

 //    $('#submit-delivery').attr('disabled','on');

    var errorCount = 0; // validator.checkAll();
    if (errorCount == 0) {
      $.ajax({
        data: fdata,
        cache: false,
        contentType: false,
        processData: false,
        async: false,
        type: 'POST',
        dataType: "JSON",
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ route('stocks.delivery.upload_document') }}",
        success: function(res, textStatus, jqXHR) {

        //   $('#submit-delivery').removeAttr('disabled');
          // console.log('=======>>>>> ',res);
          if (jqXHR.status == 200) {

              $.confirm({
                title: 'Success',
                content: res.message,
                buttons: {
                  yes: {
                    text: 'OK',
                    action: function() {
                        $('#upload_document_modal').modal('hide');
                    }
                  }
                }
              });
            
          }





        },
        error: ajaxFailBlock
      });
    }
       
    }



    $(document).ready(function() {
        $.ajax({
            dataType: 'json',
            type: 'get',
            url: "{{ route('stocks.dp.list') }}?stock_delivery_id={{ $row->id }}",
            success: function(res) {
                // product_list
                var html = '';
                var sr = 1;
                if (res.data.length > 0) {
                    $(res.data).each(function(index, ele) {
                        var serial_number_link = '<a href="javascript:void(0);" onclick="show_serials(' + ele.id + ');" >Show Serial</a>'; 
                        html += '<tr><td>' + (sr) + '</td><td style="width:30%" >'+ ele.product_name +'<br>'+ ele.product_category_name + '</td><td>' + ele.shop_name + '</td><td>' + ele.product_attribute + '</td><td>' + ele.quantity + '</td><td>'+ele.uom_code+'</td><td>' + ele.unit_price + '</td><td>' + ele.total_price + '</td><td class="sn_tags col-lg-1" >' + ele.sn_tags + '</td></tr>';
                        sr++;
                    });
                    $('#product_list').html(html);
                } else {
                    $('#product_list').html('<tr><td colspan="7" style="text-align: center;" >No record found</td></tr>');
                }

             //    $('.sn_tags').tagging();
            }
        });




        $('#document_file').ace_file_input({
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

    });
</script>
@endsection