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
                <div id="customer_ledger_badge_container" style="margin-top: 6px; display: none;">
                    <div class="alert alert-info" style="margin-bottom: 0; padding: 6px 10px; font-size: 12px; border-radius: 4px;">
                        <i class="fa fa-book"></i> <strong>Customer Ledger:</strong>
                        <span id="customer_balance_text" style="font-weight: bold;">Rs. 0.00</span>
                        <span id="customer_balance_badge" class="label label-sm label-success pull-right" style="margin-top: -1px;">Settled</span>
                    </div>
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
                <tr><td class="total-label" >Total Discount</td> <td class="total-label"> <input type="text" id="total_discount" name="total_discount" class="form-control" readonly > </td></tr>
                <tr><td class="total-label" >Grand Total / BillAmount </td><td   id="billAmount" class="total-label" > -- </td></tr>
                <tr><td class="total-label" > Cash Received </td><td   id="" class="total-label" >  <input type="text" id="cash_received" onkeyup="calculateCash();" onblur="calculateCash();" name="cash_received" class="form-control" > </td></tr>
                <tr><td class="total-label" > Cash Return </td><td   id="" class="total-label" > <input type="text" id="cash_returned" name="cash_returned" class="form-control disabledInput" readonly > </td></tr>
            </table>

            <a href="javascript:void(0);" onclick="openCheckoutModal();" class="btn btn-primary btn-block btn-lg" style="font-weight: bold; font-size: 15px;"><i class="fa fa-shopping-cart"></i> Proceed to Checkout &amp; Settlement</a>
            <a href="javascript:void(0);" onclick="openPreviewPopUp();" class="btn btn-primary btn-block hidden" >Preview</a>

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

<!-- POS Checkout & Ledger Settlement Modal -->
<div class="modal fade" id="posCheckoutModal" tabindex="-1" role="dialog" aria-labelledby="posCheckoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="width: 720px; max-width: 95%;">
        <div class="modal-content" style="border-radius: 6px; overflow: hidden; box-shadow: 0 5px 25px rgba(0,0,0,0.3);">
            <div class="modal-header" style="background-color: #307ECC; color: #fff; padding: 14px 20px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff; opacity: 0.85; font-size: 24px;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="posCheckoutModalLabel" style="font-weight: 600; margin: 0;">
                    <i class="fa fa-shopping-cart"></i> POS Checkout &amp; Payment Settlement
                </h4>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <!-- Summary Card -->
                <div class="row" style="margin-bottom: 18px;">
                    <div class="col-xs-6">
                        <div class="well well-sm" style="background-color: #f5f9fc; border-left: 4px solid #307ECC; margin-bottom: 0; padding: 10px 14px;">
                            <span class="text-muted" style="font-size: 11px; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;">Grand Total Bill</span>
                            <h2 style="margin: 4px 0 0; color: #2C6AA0; font-weight: bold;" id="checkout_bill_display">Rs. 0.00</h2>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="well well-sm" style="background-color: #fbfbfb; border-left: 4px solid #87B87F; margin-bottom: 0; padding: 10px 14px;">
                            <span class="text-muted" style="font-size: 11px; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;">Customer &amp; Ledger Status</span>
                            <div style="margin-top: 4px;" id="checkout_customer_display">
                                <span class="label label-default"><i class="fa fa-user"></i> Walk-in / Guest Customer</span>
                            </div>
                            <div style="margin-top: 4px;" id="checkout_customer_balance_display">
                                <small class="text-muted">Balance: Rs. 0.00</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Selector -->
                <div class="form-group" style="margin-bottom: 18px;">
                    <label style="font-weight: bold; font-size: 13px; margin-bottom: 8px; display: block;">Select Payment Method:</label>
                    <div class="btn-group btn-group-justified" id="payment_method_btn_group">
                        <a href="javascript:void(0);" class="btn btn-default active" id="btn_pay_cash" onclick="switchPaymentMethod('cash');" style="padding: 10px;">
                            <i class="fa fa-money text-success" style="font-size: 16px;"></i><br><strong>Cash</strong>
                        </a>
                        <a href="javascript:void(0);" class="btn btn-default" id="btn_pay_credit" onclick="switchPaymentMethod('credit');" style="padding: 10px;">
                            <i class="fa fa-book text-danger" style="font-size: 16px;"></i><br><strong>Credit (Account)</strong>
                        </a>
                        <a href="javascript:void(0);" class="btn btn-default" id="btn_pay_split" onclick="switchPaymentMethod('split');" style="padding: 10px;">
                            <i class="fa fa-columns text-warning" style="font-size: 16px;"></i><br><strong>Split Payment</strong>
                        </a>
                        <a href="javascript:void(0);" class="btn btn-default" id="btn_pay_bank" onclick="switchPaymentMethod('bank_transfer');" style="padding: 10px;">
                            <i class="fa fa-university text-primary" style="font-size: 16px;"></i><br><strong>Bank Transfer</strong>
                        </a>
                    </div>
                </div>

                <!-- Guest warning for credit/split -->
                <div id="checkout_guest_warning" class="alert alert-danger" style="display: none; margin-bottom: 15px;">
                    <i class="fa fa-exclamation-triangle"></i>
                    <strong>Guest Checkout Blocked:</strong> Credit and Split payment require a registered customer account. Please select a customer from the customer dropdown before proceeding with credit.
                </div>

                <!-- Cash Method Panel -->
                <div id="panel_pay_cash" class="payment-method-panel">
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label style="font-weight: bold;">Cash Received (Tendered):</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-money"></i></span>
                                    <input type="number" step="0.01" class="form-control input-lg" id="checkout_cash_received" placeholder="0.00" oninput="calculateModalCash();">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label style="font-weight: bold;">Change to Return:</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-undo"></i></span>
                                    <input type="text" class="form-control input-lg" id="checkout_cash_return" readonly style="background-color: #f7f7f7; font-weight: bold; color: #2C6AA0;" value="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Credit Method Panel -->
                <div id="panel_pay_credit" class="payment-method-panel" style="display: none;">
                    <div class="alert alert-warning" style="margin-bottom: 15px;">
                        <i class="fa fa-info-circle"></i> 100% of the bill amount (<strong><span class="modal_due_preview">Rs. 0.00</span></strong>) will be posted to the customer's ledger as <strong>Accounts Receivable</strong>.
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>Amount Received Now:</label>
                                <input type="text" class="form-control input-lg" value="0.00" readonly style="background-color: #f9f9f9;">
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label style="font-weight: bold;">Balance to Ledger (Due Receivable):</label>
                                <input type="text" class="form-control input-lg" id="checkout_credit_due" readonly style="background-color: #fdf5f5; color: #d15b47; font-weight: bold;" value="0.00">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Split Method Panel -->
                <div id="panel_pay_split" class="payment-method-panel" style="display: none;">
                    <div class="alert alert-info" style="margin-bottom: 15px;">
                        <i class="fa fa-info-circle"></i> Enter the partial cash/bank payment collected now. The remaining balance will be charged to the customer's ledger.
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label style="font-weight: bold;">Amount Received Now (Cash/Bank):</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-money"></i></span>
                                    <input type="number" step="0.01" class="form-control input-lg" id="checkout_split_paid" placeholder="Enter amount paid" oninput="calculateModalSplit();">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label style="font-weight: bold;">Balance to Ledger (Due Receivable):</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-book"></i></span>
                                    <input type="text" class="form-control input-lg" id="checkout_split_due" readonly style="background-color: #fdf5f5; color: #d15b47; font-weight: bold;" value="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank Transfer Panel -->
                <div id="panel_pay_bank" class="payment-method-panel" style="display: none;">
                    <div class="alert alert-info" style="margin-bottom: 15px;">
                        <i class="fa fa-university"></i> Full bill amount will be settled via <strong>Bank Transfer Account</strong>. Paid: <strong><span class="modal_bank_preview">Rs. 0.00</span></strong>, Due: <strong>Rs. 0.00</strong>.
                    </div>
                </div>

                <!-- Live Settlement Impact Summary -->
                <div class="well well-sm" style="margin-top: 15px; margin-bottom: 0; background-color: #fafafa; border: 1px dashed #ccc; border-radius: 4px;">
                    <div class="row">
                        <div class="col-xs-4 text-center">
                            <small class="text-muted" style="text-transform: uppercase; font-size: 10px; font-weight: bold;">Total Bill</small>
                            <div style="font-weight: bold; font-size: 15px;" id="summary_total_bill">Rs. 0.00</div>
                        </div>
                        <div class="col-xs-4 text-center" style="border-left: 1px solid #e5e5e5; border-right: 1px solid #e5e5e5;">
                            <small class="text-muted" style="text-transform: uppercase; font-size: 10px; font-weight: bold;">Paid Now</small>
                            <div style="font-weight: bold; font-size: 15px; color: #87B87F;" id="summary_paid_amount">Rs. 0.00</div>
                        </div>
                        <div class="col-xs-4 text-center">
                            <small class="text-muted" style="text-transform: uppercase; font-size: 10px; font-weight: bold;">Balance to Ledger</small>
                            <div style="font-weight: bold; font-size: 15px; color: #D15B47;" id="summary_due_amount">Rs. 0.00</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="padding: 14px 20px; background-color: #f7f7f7;">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-success btn-lg" id="btn_confirm_checkout" onclick="executeFinalCheckout();">
                    <i class="fa fa-check-circle"></i> Confirm &amp; Finalize Order
                </button>
            </div>
        </div>
    </div>
</div>

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
                    $('#total_discount').val(data.totalDiscount);
                    $('#billAmount').html(data.billAmount);
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


    var currentCustomer = {
        id: null,
        name: null,
        balance: 0,
        formatted_balance: 'Rs. 0.00',
        status: 'Settled'
    };
    var currentPaymentMethod = 'cash';

    function fetchCustomerBalance(customerId, customerName) {
        if (!customerId || parseInt(customerId) <= 0) {
            currentCustomer = { id: null, name: null, balance: 0, formatted_balance: 'Rs. 0.00', status: 'Settled' };
            $('#customer_ledger_badge_container').hide();
            return;
        }

        $.ajax({
            url: "{{ route('customer.ledger.balance', '') }}/" + customerId,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                currentCustomer = {
                    id: customerId,
                    name: customerName || res.customer_name,
                    balance: parseFloat(res.balance),
                    formatted_balance: res.formatted_balance,
                    status: res.status
                };

                $('#customer_ledger_badge_container').show();
                $('#customer_balance_text').text(res.formatted_balance);
                var badge = $('#customer_balance_badge');
                badge.removeClass('label-success label-danger label-warning');
                if (currentCustomer.balance > 0) {
                    badge.addClass('label-danger').text(res.status || 'Due Receivable');
                } else if (currentCustomer.balance < 0) {
                    badge.addClass('label-warning').text(res.status || 'Advance Credit');
                } else {
                    badge.addClass('label-success').text('Settled (0.00)');
                }
            }
        });
    }

    function placeOrderConfirm() {
        openCheckoutModal();
    }

    function openCheckoutModal() {
        if (!billAmount || parseFloat(billAmount) <= 0) {
            $.confirm({
                title: 'Cart is Empty',
                content: 'Please add products to cart before proceeding to checkout.',
                type: 'red'
            });
            return;
        }

        var bill = parseFloat(billAmount);
        var formattedBill = 'Rs. ' + bill.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        $('#checkout_bill_display').text(formattedBill);
        $('.modal_due_preview').text(formattedBill);
        $('.modal_bank_preview').text(formattedBill);

        // Customer display
        if (currentCustomer.id) {
            $('#checkout_customer_display').html('<span class="label label-info" style="font-size: 13px;"><i class="fa fa-user"></i> ' + currentCustomer.name + '</span>');
            var balanceColor = currentCustomer.balance > 0 ? '#D15B47' : '#87B87F';
            $('#checkout_customer_balance_display').html('<span style="font-weight: bold; color: ' + balanceColor + ';">Current Balance: ' + currentCustomer.formatted_balance + ' (' + currentCustomer.status + ')</span>');
        } else {
            $('#checkout_customer_display').html('<span class="label label-default" style="font-size: 13px;"><i class="fa fa-user"></i> Walk-in / Guest Customer</span>');
            $('#checkout_customer_balance_display').html('<span class="text-danger"><i class="fa fa-exclamation-circle"></i> Credit / Split requires registered customer</span>');
        }

        // Initialize cash values
        $('#checkout_cash_received').val(bill);
        $('#checkout_cash_return').val('0.00');
        $('#checkout_credit_due').val(bill.toFixed(2));
        $('#checkout_split_paid').val('');
        $('#checkout_split_due').val(bill.toFixed(2));

        switchPaymentMethod('cash');
        $('#posCheckoutModal').modal('show');
    }

    function switchPaymentMethod(method) {
        currentPaymentMethod = method;
        $('#payment_method_btn_group a').removeClass('active btn-primary').addClass('btn-default');
        $('#btn_pay_' + (method === 'bank_transfer' ? 'bank' : method)).removeClass('btn-default').addClass('active btn-primary');

        $('.payment-method-panel').hide();
        if (method === 'cash') {
            $('#panel_pay_cash').show();
            calculateModalCash();
        } else if (method === 'credit') {
            $('#panel_pay_credit').show();
            calculateModalCredit();
        } else if (method === 'split') {
            $('#panel_pay_split').show();
            calculateModalSplit();
        } else if (method === 'bank_transfer') {
            $('#panel_pay_bank').show();
            calculateModalBank();
        }

        // Validate guest checkout for credit / split
        if ((method === 'credit' || method === 'split') && !currentCustomer.id) {
            $('#checkout_guest_warning').show();
            $('#btn_confirm_checkout').attr('disabled', 'disabled');
        } else {
            $('#checkout_guest_warning').hide();
            $('#btn_confirm_checkout').removeAttr('disabled');
        }
    }

    function calculateModalCash() {
        var bill = parseFloat(billAmount) || 0;
        var received = parseFloat($('#checkout_cash_received').val()) || 0;
        var change = received > bill ? (received - bill) : 0;
        $('#checkout_cash_return').val(change.toFixed(2));

        $('#summary_total_bill').text('Rs. ' + bill.toFixed(2));
        $('#summary_paid_amount').text('Rs. ' + bill.toFixed(2));
        $('#summary_due_amount').text('Rs. 0.00');
    }

    function calculateModalCredit() {
        var bill = parseFloat(billAmount) || 0;
        $('#checkout_credit_due').val(bill.toFixed(2));

        $('#summary_total_bill').text('Rs. ' + bill.toFixed(2));
        $('#summary_paid_amount').text('Rs. 0.00');
        $('#summary_due_amount').text('Rs. ' + bill.toFixed(2));
    }

    function calculateModalSplit() {
        var bill = parseFloat(billAmount) || 0;
        var paid = parseFloat($('#checkout_split_paid').val()) || 0;
        if (paid > bill) {
            paid = bill;
            $('#checkout_split_paid').val(bill.toFixed(2));
        }
        var due = Math.max(0, bill - paid);
        $('#checkout_split_due').val(due.toFixed(2));

        $('#summary_total_bill').text('Rs. ' + bill.toFixed(2));
        $('#summary_paid_amount').text('Rs. ' + paid.toFixed(2));
        $('#summary_due_amount').text('Rs. ' + due.toFixed(2));
    }

    function calculateModalBank() {
        var bill = parseFloat(billAmount) || 0;
        $('#summary_total_bill').text('Rs. ' + bill.toFixed(2));
        $('#summary_paid_amount').text('Rs. ' + bill.toFixed(2));
        $('#summary_due_amount').text('Rs. 0.00');
    }

    function executeFinalCheckout() {
        var bill = parseFloat(billAmount) || 0;
        var customerId = currentCustomer.id;

        if ((currentPaymentMethod === 'credit' || currentPaymentMethod === 'split') && !customerId) {
            $.confirm({
                title: 'Customer Required',
                content: 'Credit or Split payment requires a registered customer account. Please select or create a customer first.',
                type: 'red'
            });
            return;
        }

        var paidAmount = bill;
        var dueAmount = 0.00;
        var cashReceived = 0.00;
        var cashReturned = 0.00;

        if (currentPaymentMethod === 'cash') {
            paidAmount = bill;
            dueAmount = 0.00;
            cashReceived = parseFloat($('#checkout_cash_received').val()) || bill;
            cashReturned = parseFloat($('#checkout_cash_return').val()) || 0.00;
        } else if (currentPaymentMethod === 'credit') {
            paidAmount = 0.00;
            dueAmount = bill;
            cashReceived = 0.00;
            cashReturned = 0.00;
        } else if (currentPaymentMethod === 'split') {
            paidAmount = parseFloat($('#checkout_split_paid').val()) || 0.00;
            dueAmount = parseFloat($('#checkout_split_due').val()) || (bill - paidAmount);
            cashReceived = paidAmount;
            cashReturned = 0.00;
        } else if (currentPaymentMethod === 'bank_transfer') {
            paidAmount = bill;
            dueAmount = 0.00;
            cashReceived = bill;
            cashReturned = 0.00;
        }

        var formData = new FormData($('#order_form')[0]);
        formData.append('customer_id', customerId || '');
        formData.append('payment_method', currentPaymentMethod);
        formData.append('paid_amount', paidAmount);
        formData.append('due_amount', dueAmount);
        formData.append('cash_received', cashReceived);
        formData.append('cash_returned', cashReturned);

        $('#btn_confirm_checkout').attr('disabled', 'disabled').html('<i class="fa fa-spinner fa-spin"></i> Processing Order...');

        $.ajax({
            dataType: 'json',
            type: 'POST',
            data: formData,
            url: "{{ route('sale.cart.place_order') }}",
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#btn_confirm_checkout').removeAttr('disabled').html('<i class="fa fa-check-circle"></i> Confirm & Finalize Order');
                ajaxFailBlock(jqXHR, textStatus, errorThrown);
            },
            success: function(res) {
                $('#posCheckoutModal').modal('hide');
                if (res.status) {
                    var url = "{{ route('sale.order.detail', '') }}/" + res.data.id + '?view=print';
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
                    $('#billAmount').html(data.billAmount);
                    billAmount = data.billAmount;
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
        allowClear: true,
        ajax: {
            url: "{{ route('customer.search_select2') }}",
            dataType: 'json'
        }
    }).on('select2:select', function (e) {
            var obj = e.params.data;
            var custName = obj.text || (obj.name ? obj.name : 'Customer #' + obj.id);
            fetchCustomerBalance(obj.id, custName);
            var saleKey = $('#sale_key').val();   
            $.ajax({
                type: 'post',
                data:{'sale_key':saleKey, 'customer_id':obj.id},
                url: "{{ route('sale.cart.attach_customer_with_cart') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error:ajaxFailBlock,
                success: function(res) {
                    console.log('Customer attached to cart:', res);
                }
            });
    }).on('select2:unselect select2:clear', function(e) {
        fetchCustomerBalance(null, null);
    });


    //$("#customer_select2").select2('val',2);


    });

    

    
</script>
@endsection