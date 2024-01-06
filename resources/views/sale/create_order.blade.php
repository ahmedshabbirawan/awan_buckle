@extends('Layout.master')

@section('title')
Issuance Items
@endsection

@section('content')

<style>
#cart_table_info{
    display: none;
}    
.total-label{
    font-weight: bold;
    margin-left: 10px;
    width: 75%;
}
.pro_img_sug{
    width: 50px;
    height: 50px;
}
</style>
<input type="hidden" id="sale_key" value="{{ $salekey }}" >
<div class="page-content">
<div class="page-header" style="min-height:40px;">
    

    <h1> Sale Order </h1> 

    
<div class="row" style="margin-top: 10px;" >

<div class="col-sm-6">

<div class="widget-box">
<div class="widget-header">
<h5 class="widget-title smaller"> Sale Order | {{ $salekey }}</h5>



        </div>
    
        <div class="widget-body">
                <div class="widget-main">
                <form id="customer_form" action="javascript:void(0);" >
        <input type="hidden" name="sale_key" value="{{ $salekey }}" >

<div class="row">
                <div class="col-xs-12 col-sm-12">
        <label for="customer_name"> Select Customer </label>
            <div class="input-group">
                <select class="form-control" name="customer_id" id="customer_select2" >
                    
                </select>
                <span class="input-group-addon" onclick="pos_app.showCreateCustomerModal();"  >
                    <i class="fa fa-plus bigger-110"></i>
                </span>
            </div>
        </div>

        </div>

                </form>



                </div>
            
            
            
            
            
            </div>
    
    
    
    </div>

</div>

    <div class="col-sm-6">
        <div class="widget-box">


            <div class="widget-header">
            <h5 class="widget-title smaller"> Sale Order | {{ $salekey }}</h5>

            <div class="widget-toolbar">
                <a href="{{ route('sale.board.create') }}" class="btn btn-xs btn-light bigger"><i class="ace-icon fa fa-plus-o"></i> Create New Order </a> 

                <a href="{{ route('stock_exchange.req_form') }}" class="btn btn-xs btn-light bigger"><i class="ace-icon fa fa-plus-o"></i> Request Stock Exchange </a> 
                
            </div>
            </div>
        



           
    
            <div class="widget-body">
                <div class="widget-main">






                <div class="row">
                <div class="col-xs-12 col-sm-12">
        <label for="customer_name"> Search Product </label>
            <div class="input-group">
            <span class="input-group-addon">
                    <i class="fa fa-search bigger-110"></i>
                </span>
                <select class="form-control" name="product_select2" id="product_select2" >
                    
                </select>
                
            </div>
        </div>

        </div>




<div class="row">
    <!-- <form id="customer_form_____" action="javascript:void(0);" >
        <input type="hidden" name="sale_key" value="{{ $salekey }}" >

    <div class="col-lg-3" >
        <label for="customer_name"> Customer Name </label>
        <div class="input-group"> <input name="customer_name" class="form-control input-mask-phone col-lg-12" type="text" id="customer_name"> </div>
    </div>
    <div class="col-lg-3" >
        <label for="customer_mobile"> Customer Mobile </label>
        <div class="input-group"> <input name="customer_mobile" class="form-control input-mask-phone col-lg-12" type="text"  id="customer_mobile"> </div>
    </div>

    </form> -->
</div>

                 
                </div>
            </div>
        </div>
    </div>
    
    </div>
    
    
    
    
</div>





<div class="row ">
    <div class="col-6 col-lg-6" >
        <div class="card radius-10 border-top border-0 border-4 border-danger">
          

            <div class="card-body">

                @include('Layout.alerts')

                <div class="table-responsive">
                    <table class="table table-striped table-bordered yajratable" id="item_table" style="width:100%"></table>
                </div>
            </div>



        </div>
    </div>

<!----------------------------------------------------------------->

<div class="col-6 col-lg-6" >
    <div class="widget-box widget-color-blue ui-sortable-handle" >
    <div class="widget-header"><h4 class="widget-title">Cart</h4></div>
    <div class="card radius-10 border-top border-0 border-4 border-danger">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered yajratable" id="cart_table" style="width:100%"></table>
            </div>
        </div>
        <div class="card-footer">
            <table class="table">
                

                <tr><td class="total-label" >Total Item</td><td id="total_item" class="total-label" > -- </td></tr>
                <tr><td class="total-label" >Sub Total</td><td id="sub_total" class="total-label" > -- </td></tr>
                <tr><td class="total-label" >Total Discount</td><td id="total_discount" class="total-label" > -- </td></tr>
                <tr><td class="total-label" >Grand Total / BillAmount </td><td   id="billAmount" class="total-label" > -- </td></tr>
            </table>

            <a href="javascript:void(0);" onclick="placeOrderConfirm();"  class="btn btn-primary btn-block" >Place Order</a>
            <a href="{{ route('sale.cart.preview',$salekey) }}" class="btn btn-primary btn-block" >Preview</a>

        </div>
    </div>
    </div>
</div>


</div>
<!--end row-->
</div>



<!--- delete confirmation modal --->
<div class="modal" tabindex="-1" role="dialog" id="deletePopup">
    <div class="modal-dialog" role="document">
        <div class="modal-content">


            <form action="#" method="post" id="delete_form">
                <input type="hidden" name="_method" value="delete" />
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Do you really want to delete ?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Confirm</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>

            </form>

        </div>
    </div>
</div>
<!--- delete confirmation modal end --->

<div id="create_customer_modal_con"></div>

@endsection




@section('script')
<script>
    var table;
    var selectID;
    var cart = [];
    var cartDataTable;

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

    function addOneToCart(productID){
        console.log('pki');
         addToCart(productID,'1','add');
    }

    function updateToCart(productID, ele){
        var val = $(ele).val();

        console.log(productID);
       
        addToCart(productID,val,'update');
    }

    function addToCart(productID,qty,action){
        var saleKey = $('#sale_key').val();   
        $.ajax({
            //  dataType: 'json',
            type: 'post',
            data:{'product_id':productID, 'qty':qty, 'action' : action, 'sale_key' :  saleKey},
            url: "{{ route('sale.cart.add') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error:ajaxFailBlock,
            success: function(res) {
                console.log(res);
                cartDataTable.ajax.reload();
            }
        });
    }


    function applyDiscount(productID, discountAmount){
        var saleKey = $('#sale_key').val();   
        $.ajax({
            //  dataType: 'json',
            type: 'post',
            data:{'product_id':productID, 'discount_amount':discountAmount,'sale_key' :  saleKey},
            url: "{{ route('sale.cart.apply_dicount_single') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error:ajaxFailBlock,
            success: function(res) {
                console.log(res);
                cartDataTable.ajax.reload();
            }
        });
    }

    function applyDiscountOnSingleProduct(productID, $inputField){
        var discountAmount = $($inputField).val();
        applyDiscount(productID, discountAmount);
    }

    function listCart(){

        $.ajax({
            //  dataType: 'json',
            type: 'get',
         //    data:{'item_id':itemID, 'qty':qty, 'action' : action },
            url: "{{ route('sale.cart.index') }}",
            // headers: {
            //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            // },
            error:ajaxFailBlock,
            success: function(res) {
                console.log(res);
            }
        });
    }


    function placeOrderConfirm(){
        $.confirm({
                    title: 'Confirm',
                    content: 'Do you really want to place order',
                    buttons: {
                        yes: {
                            text: 'OK',
                            action: function() {
                                placeOrder();
                            }
                        }
                    }
                });
    }


    function placeOrder(){

        var formData = new FormData($('#customer_form')[0]);

        console.log(formData);

        // var saleKey = $('#sale_key').val();   
        $.ajax({
            dataType: 'json',
            type: 'POST',
            data: formData,
            url:  "{{ route('sale.cart.place_order') }}",
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error:ajaxFailBlock,
            success: function(res) {
                $.confirm({
                    title: 'Response',
                    content: res.message,
                    buttons: {
                        yes: {
                            text: 'OK',
                            action: function() {
                                cartDataTable.ajax.reload();
                                window.location = "{{ route('sale.order.detail','') }}/"+res.data.id;
                            }
                        }
                    }
                });
            }
        });
    }

    $(document).ready(function() {
        var saleKey = $('#sale_key').val();  
        setTimeout(function(){
             
        table = $('#item_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('sale.board.product_items') }}?sale_key="+saleKey,
            columns: [
                // {
                //     "data": "id",
                //     title: 'Sr.',
                //     width:'5%',
                //     render: function(data, type, row, meta) {
                //         return meta.row + meta.settings._iDisplayStart + 1;
                //     }
                // },
                {"data": "image_url",title: 'Image',
                    render: function(data, type, row, meta) {
                        return '<image src="'+data+'" style="width:75px; height:75px;" >';
                    }, width: '10%'
                },
                {
                    data: 'product_detail',
                    name: 'product_detail',
                    title: 'Product',
                    width:'50%'
                   
                },
                {
                    data: 'product.price',
                    name: 'product.price',
                    title: 'Price',
                 
                },
                {
                    data: 'total_qty',
                    name: 'total_qty',
                    title: 'Qty',
                },{
                    data: 'action',
                    name: 'action',
                    title: 'Action',
                }
                // 
            ],
            order: [[0, 'desc']]
        });

    },500);



    /* Cart Table */
    
    cartDataTable = $('#cart_table').DataTable({
        dom: 'Bfrtip',
    buttons: [
       {
           extend: 'print',
           footer: false
       }        
    ] ,
            processing: true,
            serverSide: true,
            searching : false,
            paging : false,
            // ajax: "{{ route('sale.cart.index') }}?for=datatable",
            ajax: {
                url: "{{ route('sale.cart.index') }}?for=datatable&sale_key="+saleKey,
                // success:function(res){
                  //  console.log('hello :::: ', res);
                    // return res;
               //  },
                complete:function(res){
                    var data = res['responseJSON'];
                    $('#total_item').html(data.recordsTotal);
                    
                    $('#sub_total').html(data.subTotal);
                    $('#total_discount').html(data.totalDiscount);
                    $('#billAmount').html(data.billAmount);
                }
            },
            columns: [
                {
                    "data": "id",
                    title: 'Sr.',
                    width:'5%',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },{
                    data: 'product_name',
                    name: 'product_name',
                    title: 'Product',
                    width:'40%',
                },{
                    data: 'qty',
                    name: 'qty',
                    title: 'Qty',
                    render: function(data, type, row, meta) {
                        console.log(row);
                        return '<input type="text" id="'+row.item_id+'_qty"  onblur="updateToCart('+row.product_id+',this)" class="input-sm col-xs-12"  value="'+data+'" >';
                    }
                },{
                    data: 'offer_price',
                    name: 'offer_price',
                    title: 'Price'
                },{
                    data: 'discount',
                    name: 'discount',
                    title: 'Discount',
                    render: function(data, type, row, meta) {
                        return '<input type="text" id="'+row.item_id+'_discount" onblur="applyDiscountOnSingleProduct('+row.product_id+',this)" value="'+data+'" class="input-sm col-xs-12" >';
                    }
                },{
                    data: 'product_total_price',
                    name: 'product_total_price',
                    title: 'Final'
                },



            ],
            order: [[0, 'desc']]
        });
        


    
        function formatRepo (repo) {
//   if (repo.loading) {
//     return repo.text;
//   }

// console('helllll', repo);

  var $container = $(
    "<div class='select2-result-repository clearfix'>" +
      "<div class='select2-result-repository__avatar'><img class='pro_img_sug' src='" + repo.product_img_url + "' /></div>" +
      "<div class='select2-result-repository__meta'>" +
        "<div class='select2-result-repository__title'>"+repo.text+"</div>" +
      "</div>" +
    "</div>"
  );

//   $container.find(".select2-result-repository__title").text(repo.full_name);
//   $container.find(".select2-result-repository__description").text(repo.description);
//   $container.find(".select2-result-repository__forks").append(repo.forks_count + " Forks");
//   $container.find(".select2-result-repository__stargazers").append(repo.stargazers_count + " Stars");
//   $container.find(".select2-result-repository__watchers").append(repo.watchers_count + " Watchers");

  return $container;
}

function formatRepoSelection (repo) {
  return repo.text;
}
   

        
   
        $('#product_select2').select2({
        placeholder: "Search Product",
        ajax: {
            url: "{{ route('sale.cart.search_product') }}",
            dataType: 'json',
            // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            
        }
//         minimumInputLength: 1,
//   templateResult: formatRepo,
//   templateSelection: formatRepoSelection

        }).on('select2:select', function (e) {
            // Do something
            // console.log(e.params.data);
            var obj = e.params.data;
            addToCart(obj.product_id,'1','add');
        });


    $('#customer_select2').select2({
        placeholder: "Select Customer",
        ajax: {
            url: "{{ route('customer.search_select2') }}",
            dataType: 'json'
            // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
        }
    });


    });

    /*
     $('#customer_name').typeahead({
        minLength: 1,
       
        displayField: 'name'
    },{
        limit: 12,
        source: function(query, processSync, processAsync) {
        //  processSync(['This suggestion appears immediately', 'This one too']);
        return $.ajax({
          url: "{{ route('customer.search') }}",
          type: 'GET',
          displayField: 'name',
          valueField: "id",
          data: {
            query: query
          },
        dataType: 'json',
        preDispatch: function(query) {
            showLoadingMask(true);
            return {
              search: query
            }
        },
        preProcess: function(data) {
            if (data.success === false) {
              return false;
            } else {
              return data;
            }
          },
          success: function(result) {
            var resultList = result.results.map(function(item) {
              return item.text; //{first_name : item.text};
            });
            return processAsync(resultList);
          }
        });
      }
    }).on('typeahead:select', function(evt, item) {
        console.log('selected event', evt);
        // var arr = item.split("-");
        // console.log(arr);
        // do what you want with the item here

         var arr =  item.split("-");
        // setTimeout(function(){
            $('#customer_name').val(arr[0]);
            $('#customer_mobile').val(arr[1]);
      //  },3000);

    }).on('typeahead:close', function(evt, item) {
        var item = $('#customer_name').val();
        console.log('selected close', evt);
        var arr =  item.split("-");
         setTimeout(function(){
            $('#customer_name').val(arr[0]);
          //  $('#customer_mobile').val(arr[1]);
         },2000);
    }).on('typeahead:change',function(){
        // $('#customer_mobile').val('');
    });
    */


    // function saveCustomerInfo(productID,qty,action){
    //     var formData = new FormData($('#customer_form')[0]);
    //     console.log(formData);
    //    //  return false;   
    //     $.ajax({
    //         //  dataType: 'json',
    //         type: 'post',
    //         data:{'product_id':productID, 'qty':qty, 'action' : action, 'sale_key' :  saleKey},
    //         url: "{{ route('sale.cart.add') }}",
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         success: function(res) {
    //             console.log(res);
    //             cartDataTable.ajax.reload();
    //         }
    //     });
    // }

    
</script>
@endsection