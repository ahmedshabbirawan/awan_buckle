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
</style>
<input type="hidden" id="exchange_key" value="{{ $exchangeKey }}" >
<div class="page-content">
<div class="page-header" style="min-height:40px;">
    

    <h1> Request Stock Exchange </h1> 

    
<div class="row" style="margin-top: 10px;" >

    

    <div class="col-sm-12">
        <div class="widget-box" style="display:none;" >


            <div class="widget-header" >
            <h5 class="widget-title smaller"> Request Stock Exchange | {{ $exchangeKey }}</h5>

            <div class="widget-toolbar">
                <a href="{{ route('sale.board.create') }}" class="btn btn-xs btn-light bigger"><i class="ace-icon fa fa-plus-o"></i> Create New Order </a> 

                <a href="{{ route('stock_exchange.req_form') }}" class="btn btn-xs btn-light bigger"><i class="ace-icon fa fa-plus-o"></i> Request Stock Exchange </a> 
                
            </div>
            </div>
        



           
    
            <div class="widget-body">
                <div class="widget-main">

                    {{-- form-inline --}}


<div class="row">
    <form id="customer_form" action="javascript:void(0);" >
        <input type="hidden" name="exchange_key" value="{{ $exchangeKey }}" >

    <div class="col-lg-3" >
        <label for="customer_name"> Shops </label>
        <div class="input-group"> 
            <select name="shop_id" id="shop_id">
                @foreach($shops as $id => $name)
                <option value="{{ $id }}"> {{ $name }} </option>
                @endforeach
            </select>
        </div>
    </div>
    </form>
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
    <div class="widget-header"><h4 class="widget-title">Exchange Cart</h4></div>
    <div class="card radius-10 border-top border-0 border-4 border-danger">

    <form action="#" method="post" id="cart_items_form">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered yajratable" id="cart_table" style="width:100%"></table>
            </div>
        </div>
        <div class="card-footer">
            <table class="table">
                

                <!-- <tr><td class="total-label" >Total Item</td><td id="total_item" class="total-label" > -- </td></tr>
                <tr><td class="total-label" >Sub Total</td><td id="sub_total" class="total-label" > -- </td></tr>
                <tr><td class="total-label" >Total Discount</td><td id="total_discount" class="total-label" > -- </td></tr>
                <tr><td class="total-label" >Grand Total / BillAmount </td><td   id="billAmount" class="total-label" > -- </td></tr> -->
            </table>

            <a href="javascript:void(0);" onclick="placeOrderConfirm();"  class="btn btn-primary btn-block" >Post Request</a>
            <!-- <a href="{{ route('sale.cart.preview',$exchangeKey) }}" class="btn btn-primary btn-block" >Preview</a> -->

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

    function addOneToCart(productID,shopID){
        console.log('pki');
         addToCart(productID,'1','add',shopID);
    }

    function updateToCart(productID, ele, shopID){
        var val = $(ele).val();
        addToCart(productID,val,'update', shopID);
    }

    function addToCart(productID,qty,action, shopID){
        var exchangeKey = $('#exchange_key').val();   
        $.ajax({
            //  dataType: 'json',
            type: 'post',
            data:{'product_id':productID, 'qty':qty, 'action' : action, 'exchange_key' :  exchangeKey, 'shop_id' : shopID},
            url: "{{ route('stock_exchange.add_item_to_exchange') }}",
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

    function deleteCartItem(itemID){
        $.ajax({
            //  dataType: 'json',
            type: 'post',
            data:{'item_id':itemID},
            url: "{{ route('stock_exchange.delete_cart_item') }}",
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

        var formData = new FormData($('#cart_items_form')[0]);

        console.log(formData);

        // var exchangeKey = $('#exchange_key').val();   
        $.ajax({
            dataType: 'json',
            type: 'POST',
            data: formData,
            url:  "{{ route('stock_exchange.place_request') }}",
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error:ajaxFailBlock,
            success: function(res) {
                // $.confirm({
                //     title: 'Response',
                //     content: res.message,
                //     buttons: {
                //         yes: {
                //             text: 'OK',
                //             action: function() {
                //                 cartDataTable.ajax.reload();
                //             }
                //         }
                //     }
                // });
            }
        });
    }

    $(document).ready(function() {
        var dataColumns = [
            { data: 'id', name: 'id', title: 'ID.', width: '10%'  },
            {"data": "image_url",title: 'Image',
                render: function(data, type, row, meta) {
                    return '<image src="'+data+'" style="width:75px; height:75px;" >';
                }, width: '10%'
            },

            {"data": "product_name",title: 'Image',
                render: function(data, type, row, meta) {
                    return data+' <br/> '+' Unit : '+row.code+' <br/> ';
                }, width: '15%'
            }
        ];
        <?php 
        foreach($shops as $id => $name):
            
        ?>
                dataColumns.push({ data: 'shop_<?=$id?>', name: 'shop_<?=$id?>', title: '<?=$name?>', render : function(data, type, row, meta){
                    <?php if(auth()->user()->shop_id != $id){ ?>
                            if(data){
                                return '<button type="button" class="btn btn-xs btn-info" onclick="addOneToCart('+row.id+',<?=$id?>)" ><span class="label label-sm label-primary"><b>'+data+' </b></span>  Add </button>';
                            }else{
                                return data;
                            }
                    <?php }else{ ?>
                        return data;
                    <?php } ?>
                } });
        <?php
        endforeach;
        ?>
        setTimeout(() => {
            listTable = $('#item_table').DataTable({
            sort:true,
            dom: 'Bfrtip',
            buttons: [
            'excel', 'pdf'
            ],
            // paging: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('product.list') }}",
                type: 'GET',
                data: function (d) {
                    var idz = [];
                    $($('.attributes_id')).each(function(index,ele){
                        idz.push($(ele).val());
                    });
                    d.product_category_id       = $('#product_category_id').val();
                    d.attribute_idz = idz;
                }
            },
            columns: dataColumns,
            order: [[ 2 , 'asc']]
        });
        }, 500);
        var exchangeKey = $('#exchange_key').val();  
            ///////////////////////////////////////////////////////////////////////////////////////
    /* Cart Table */

  //  url: "{{ route('stock_exchange.get_request_cart_data') }}?for=datatable&exchange_key="+exchangeKey,
        // cartDataTable 

        cartDataTable =    $('#cart_table').DataTable({
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
                url: "{{ route('stock_exchange.get_request_cart_data') }}?for=datatable&exchange_key="+exchangeKey,
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
                        return '<input type="hidden" name="item_ids[]" value="'+data+'" >'+(meta.row + meta.settings._iDisplayStart + 1);
                    }
                },{
                    data: 'product_name',
                    name: 'product_name',
                    title: 'Product',
                    width:'35%',
                },
                {
                    data: 'shop_name',
                    name: 'shop_name',
                    title: 'Shop',
                    width:'20%',
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    data: 'req_qty',
                    name: 'req_qty',
                    title: 'Qty',
                    width:'20%',
                    render: function(data, type, row, meta) {
                        return '<input type="text" id="'+row.id+'_qty"  onblur="updateToCart('+row.product_id+',this, '+row.req_receive_shop_id+' )" class="input-sm col-xs-12"  value="'+data+'" >';
                    }
                },
                { data: 'action',name: 'action',title: 'Action' },
            ],
            order: [[0, 'desc']]
        });
        


    
       

    });
</script>
@endsection