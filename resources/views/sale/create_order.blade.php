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
    

    <h1> Sale Board / POS </h1> 

    
<div class="row" style="margin-top: 10px;" >

<div class="col-sm-6">
<div class="widget-box">
<div class="widget-header"><h5 class="widget-title smaller"> Sale Board / POS</h5></div>
<div class="widget-body">
    <div class="widget-main">
        <form id="customer_form" action="javascript:void(0);" >
        <input type="hidden" name="sale_key" value="{{ $salekey }}" >
        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <label for="customer_name"> Select Customer </label>
                <div class="input-group">
                    <select class="form-control" name="customer_id" id="customer_select2" ></select>
                    <span class="input-group-addon" onclick="pos_app.showCreateCustomerModal();"  title="Create Customer"  >
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
                <h5 class="widget-title smaller"> Sale Order</h5>
                <div class="widget-toolbar">
                    <a href="javascript:void(0);" onclick="pos_app.showOnHoldListModal();" class="btn btn-xs btn-light bigger"><i class="ace-icon fa fa-plus-o"></i> OnHolds </a> 
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
                        <span class="input-group-addon"><i class="fa fa-search bigger-110"></i></span>
                        <select class="form-control" name="product_select2" id="product_select2" ></select>
                    </div>
                    </div>
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
    <form id="order_form" action="javascript:void(0);" >
    <input type="hidden" name="sale_key" value="{{ $salekey }}" >
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
                <tr><td class="total-label" >Grand Total / BillAmount </td><td  class="billAmount" id="billAmount" class="total-label" > -- </td></tr>
                <tr><td class="total-label" > Cash Received </td><td   id="" class="total-label" > <input type="text" id="cash_received" onkeyup="calculateCash();" onblur="calculateCash();" name="cash_received" class="form-control" > </td></tr>
                <tr><td class="total-label" > Cash Return </td><td   id="" class="total-label" > <input type="text" id="cash_returned" name="cash_returned" class="form-control disabledInput" readonly > </td></tr>
            </table>
            <a href="javascript:void(0);" onclick="placeOrderConfirm();"  class="btn btn-primary btn-block" >Place Order With Cash</a>
            <a href="javascript:void(0);" onclick="multiplePayments();"  class="btn btn-primary btn-block" >Multiple Payment</a>
            <a href="javascript:void(0);" onclick="openPreviewPopUp();" class="btn btn-primary btn-block hidden" >Preview</a>
        </div>
        <div class="udar_contain" >
            <input type="hidden" name="payment_type" id="payment_type" value="" >
            
<!--- payment modal --->
<div>
<div class="modal" tabindex="-1" role="dialog" id="multiple_payment_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
                <div class="modal-header no-padding">
                    <h5 class="modal-title">Multiple Payments</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped table-bordered table-hover no-margin-bottom no-border-top" >
                        <tr>
                            <th>Total Order</th>
                            <th>Orders Amount</th>
                            <th>Amount Pay</th>
                            <th>Amount Pending</th>
                        </tr>
                        <tr>
                            <th class="cus_total_order" >--</th>
                            <th class="cus_order_amount" >--</th>
                            <th class="cus_amount_pay" >--</th>
                            <th class="cus_amount_due" >--</th>
                        </tr>
                    </table>

                    <div class="" style="padding: 15px; width:15px;" ></div>

                    <table class="table table-striped table-bordered table-hover no-margin-bottom no-border-top" >
                        <tr>
                            <th colspan="2">Total Amount</th>
                            <th colspan="2" class="billAmount"></th>
                        </tr>
                        <tr><th colspan="4"></th></tr>

                        <tr>
                            <th>Sr</th>
                            <th>Mode</th>
                            <th>Amounts</th>
                            <th>Action</th>
                        </tr>

                        <tr>
                            <th>#</th>
                            <th><select name="payment_mode[]" class="form-control" > <option value="cash" >Cash</option> <option value="late_payment" >Late Payment</option> </select></th>
                            <th><input type="text" name="payment_amount[]" value="" id="payment_first"  class="" placeholder="Amount" ></th>
                            <th>X</th>
                        </tr>

                        <tr>
                        <th>#</th>
                        <th><select name="payment_mode[]" class="form-control" > <option value="cash" >Cash</option> <option value="late_payment" >Late Payment</option> </select></th>
                            <th><input type="text" name="payment_amount[]" value="" class="" placeholder="Amount" ></th>
                            <th>X</th>
                        </tr>

                        <th>#</th>
                        <th><select name="payment_mode[]" class="form-control" > <option value="cash" >Cash</option> <option value="late_payment" >Late Payment</option> </select></th>
                            <th><input type="text" name="payment_amount[]" value="" class="" placeholder="Amount" ></th>
                            <th>X</th>
                        </tr>
                        
                    </table>

                    

                </div>

                <div class="modal-body">

                    


                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" onclick="placeOrder();" >Place Order</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
        </div>
    </div>
</div>
</div>
<!--- payment modal end --->
        </div>
        </form>
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
<div id="on_hold_list_modal_con"></div>

@endsection




@section('script')
<script>
    var table;
    var selectID;
    var cart = [];
    var cartDataTable;
    var billAmount = 0;

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


    function openPreviewPopUp(){
        window.open("{{ route('sale.cart.preview',$salekey) }}");
    }

    function updateToCart(productID, ele){
        var val = $(ele).val();
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
                if(action == 'update'){
                    calculateSingleProduct(productID);
                }else{
                    cartDataTable.ajax.reload();
                }
                
            }
        });
    }

    function isFloat(n){
        return Number(n) === n && n % 1 !== 0;
    }

    function calculateSingleProduct(productID){
        var qty         = $('#'+productID+'_qty').val();
        var price       = $('#'+productID+'_price').val();
        var discount    = $('#'+productID+'_discount').val();
        var displayPrice = (qty * price) - (qty * discount);
        if(isFloat(displayPrice)){
            displayPrice = parseFloat(displayPrice).toFixed(2);
        }
        $('#'+productID+'_sub_price').html( displayPrice );

        var saleKey = $('#sale_key').val();   
        $.ajax({
            type: 'get',
            data:{'sale_key' :  saleKey},
            url: "{{ route('sale.cart.get_bill_total_amounts') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error:ajaxFailBlock,
            success: function(res) {
                var data = res; // ['responseJSON'];
                    $('#total_item').html(data.recordsTotal);
                    
                    $('#sub_total').html(data.subTotal);
                    $('#total_discount').html(data.totalDiscount);
                    $('.billAmount').html(data.billAmount);
                    billAmount = data.billAmount;


            }
        });


    }


    function calculateCash(){
        var cashAmount = parseFloat($('#cash_received').val()).toFixed(2);
        var cashReturn = cashAmount - billAmount;
        $('#cash_returned').val(cashReturn);
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
                // cartDataTable.ajax.reload();
                calculateSingleProduct(productID);
            }
        });
    }

    function updateProductPrice(productID, productPrice){
        var saleKey = $('#sale_key').val();   
        $.ajax({
            //  dataType: 'json',
            type: 'post',
            data:{'product_id':productID, 'product_price':productPrice,'sale_key' :  saleKey},
            url: "{{ route('sale.cart.update_product_price') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error:ajaxFailBlock,
            success: function(res) {
                console.log(res);
               //  cartDataTable.ajax.reload();
               calculateSingleProduct(productID);
            }
        });
    }


    function deleteCartItem(itemID){
        $.ajax({
            //  dataType: 'json',
            type: 'post',
            data:{'item_id':itemID},
            url: "{{ route('sale.cart.delete_cart_item') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error:ajaxFailBlock,
            success: function(res) {
                cartDataTable.ajax.reload();
            }
        });
    }


    function applyDiscountOnSingleProduct(productID, $inputField){
        var discountAmount = $($inputField).val();
        applyDiscount(productID, discountAmount);
    }



    
    function updateCartProductPrice(productID, $inputField){
        var productPrice = $($inputField).val();
     updateProductPrice(productID, productPrice);
        
    }


    function placeOrderConfirm(){
        
        $.confirm({
            title: 'Confirm',
            content: 'Do you really want to place order',
            buttons: {
                yes: {
                    text: 'OK',
                    action: function() {
                        $('#payment_type').val('cash');
                        placeOrder();
                    }
                },
                no: { text: 'Cancel', action: function() {} }
            }
        });
    }

    function multiplePayments(){
        var customerId = parseInt($('#customer_select2').val());
        if(isNaN(customerId)){
            alert('please select customer first!');
            return false;
        }
        $.ajax({
            dataType: 'json',
            type: 'GET',
            url:  "{{ route('customer.finance.stats','') }}/"+customerId,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error:ajaxFailBlock,
            success: function(res) {
                $('.cus_total_order').html(res.orders);
                $('.cus_order_amount').html(res.orders_amount);
                $('.cus_amount_pay').html(res.pay);
                $('.cus_amount_due').html(res.due);
            }
        });
        $('#payment_type').val('multiple');
        $('#multiple_payment_modal').modal('show');
    }


    function placeOrder(){
        var formData = new FormData($('#order_form')[0]);
        var customer_id = $('#customer_select2').val();
        formData.append('customer_id',customer_id);
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
                if(res.status){
                    // cartDataTable.ajax.reload();
                    var url = "{{ route('sale.order.detail','') }}/"+res.data.id+'?view=print';
                    window.open(url, '_blank').focus(); 
                    window.location = "{{ route('sale.board.create') }}";
                }
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
                    $('.billAmount').html(data.billAmount);
                    billAmount = data.billAmount;
                    $('#payment_first').val(billAmount);
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
                    width:'35%',
                },{
                    data: 'qty',
                    name: 'qty',
                    title: 'Qty',
                    width:'15%',
                    render: function(data, type, row, meta) {
                        return '<input type="text" id="'+row.product_id+'_qty"  onblur="updateToCart('+row.product_id+',this)" autocomplete="off" class="input-sm col-xs-12" tabindex="'+(row.id+0.1)+'" value="'+data+'" >';
                    }
                },{
                    data: 'offer_price',
                    name: 'offer_price',
                    title: 'Offer Price',
                    width:'15%',
                    render: function(data, type, row, meta) {
                        return '<input type="text" id="'+row.product_id+'_price" onblur="updateCartProductPrice('+row.product_id+',this)" autocomplete="off" value="'+data+'" tabindex="'+(row.id+0.2)+'" class="input-sm col-xs-12" >';
                    }
                },
                {
                    data: 'discount',
                    name: 'discount',
                    title: 'Discount',
                    width:'10%',
                    render: function(data, type, row, meta) {
                        return '<input type="text" id="'+row.product_id+'_discount" onblur="applyDiscountOnSingleProduct('+row.product_id+',this)" autocomplete="off" value="'+data+'" tabindex="'+(row.id+0.3)+'" class="input-sm col-xs-12" >';
                    }
                },
                {
                    data: 'product_total_price',
                    name: 'product_total_price',
                    title: 'Bill Price',
                    render: function(data, type, row, meta) {
                        return '<b id="'+row.product_id+'_sub_price" >'+data+'</b>';
                    }
                },
                { data: 'action',name: 'action',title: 'Action' },
            ]
           //  ,order: [[0, 'desc']]
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
            dataType: 'json',
            success: function(res) {
                    console.log('oooooooooooooooooooooooooooooooooooooooooo',res);
                   //  cartDataTable.ajax.reload();
                }
            // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
        }
    }).on('select2:select', function (e) {
            var obj = e.params.data;
            // console.log(obj.id);
            var saleKey = $('#sale_key').val();   
            $.ajax({
                //  dataType: 'json',
                type: 'post',
                data:{'sale_key':saleKey, 'customer_id':obj.id},
                url: "{{ route('sale.cart.attach_customer_with_cart') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error:ajaxFailBlock,
                success: function(res) {
                    console.log(res);
                   //  cartDataTable.ajax.reload();
                }
            });
    });


    //$("#customer_select2").select2('val',2);


    });

    

    
</script>
@endsection