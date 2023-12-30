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

<div class="page-content">
<div class="page-header" style="min-height:40px;">
    <div class="" style="float: left;">
        <h1>Sale Order</h1>
    </div>
    <div class="" style="float: right;">
        <a href="{{ route('sale.board.create') }}" class="btn btn-xs btn-light bigger"><i class="ace-icon fa fa-floppy-o"></i> Add Order </a> 
    </div>
</div>


<div class="row ">
    

<!----------------------------------------------------------------->

<div class="col-5 col-lg-5" style="margin-top:20px;">
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
                

                <tr><td class="total-label" >Total Item</td><td id="total_item" > -- </td></tr>
                <tr><td class="total-label" >Sub Total</td><td id="sub_total" > -- </td></tr>
                <tr><td class="total-label" >Total Discount</td  id="total_discount" ><td> -- </td></tr>
                <tr><td class="total-label" >Grand Total </td><td   id="grand_total" > -- </td></tr>
            </table>

            <a href="javascript:void(0);" onclick="placeOrder();"  class="btn btn-primary btn-block" >Preview & Confirm</a>

        </div>
    </div>
    </div>
</div>


<div class="col-5 col-lg-5" id="console-result" style="margin-top:20px;">
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


    function placeOrder(){
        $.ajax({
            url: "{{ route('sale.cart.place_order') }}",
            method: "GET",
            data: {},
            success: function (econ) {
                console.log(econ);
                $('#console-result').html(econ);
            },
            statusCode: {
                404: function () {
                    console.log("Sub Incident Type not found!");
                }
            }

        });
    }


    function addOneToCart(itemID){
        console.log('pki');
         addToCart(itemID,'1','add');
    }

    function updateToCart(itemID, ele){
        var val = $(ele).val();
        addToCart(itemID,val,'update');
    }

    function addToCart(itemID,qty,action){   
        $.ajax({
            //  dataType: 'json',
            type: 'post',
            data:{'item_id':itemID, 'qty':qty, 'action' : action },
            url: "{{ route('sale.cart.add') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                console.log(res);
                cartDataTable.ajax.reload();
            }
        });
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
            success: function(res) {
                console.log(res);
            }
        });
    }

    $(document).ready(function() {
        setTimeout(function(){
            
        table = $('#item_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('sale.board.product_items') }}",
            columns: [
                // {
                //     "data": "id",
                //     title: 'Sr.',
                //     width:'5%',
                //     render: function(data, type, row, meta) {
                //         return meta.row + meta.settings._iDisplayStart + 1;
                //     }
                // },
                {
                    data: 'product_detail',
                    name: 'product_detail',
                    title: 'Product',
                    width:'60%',
                   
                },{
                    data: 'shop_name',
                    name: 'shop_name',
                    title: 'Shop',
                 
                },{
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
                url: "{{ route('sale.cart.index') }}?for=datatable",
                // success:function(res){
                  //  console.log('hello :::: ', res);
                    // return res;
               //  },
                complete:function(res){
                    console.log('hello :::: ', res['responseJSON']);

                    var data = res['responseJSON'];

                    $('#total_item').html(data.recordsTotal);
                    $('#grand_total').html(data.totalPrice);
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
                        return '<input type="text" id="'+row.item_id+'_qty" onblur="updateToCart('+row.item_id+',this)" class="input-sm col-xs-12"  value="'+data+'" >';
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
                        return '<input type="text" id="'+row.item_id+'_discount" value="'+data+'" class="input-sm col-xs-12" >';
                    }
                },{
                    data: 'price',
                    name: 'price',
                    title: 'Final'
                },



            ],
            order: [[0, 'desc']]
        });
        


    
       


   

    

    




       //  listCart();
    
    });

    
</script>
@endsection