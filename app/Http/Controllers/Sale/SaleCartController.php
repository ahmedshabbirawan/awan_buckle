<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ReturnDetail;
use Yajra\DataTables\DataTables;

use App\Models\Products;
use App\Models\Employee;
use App\Models\Product;
use App\Models\ProductAvailable;
use App\Models\SaleCart;
use App\Models\Customer;
use App\Models\SaleItem;
use App\Models\SaleOrder;
use App\Models\SaleOrderItem;
use App\Models\Shop;
use App\Models\StockAudit;
use App\Models\StockItem;
use Illuminate\Support\Facades\DB;
use Exception;

use Illuminate\Support\Facades\Validator;
use App\Services\LedgerService;

class SaleCartController extends Controller{
    




    function index(Request $request){
   
        $for = $request->get('for');
        if($request->ajax()){
            if($for == 'ajax'){
                $results = SaleCart::all();
                return response()->json(['status' => true, 'data' => $results]);
            }elseif($for == 'datatable'){

                $saleKey = $request->get('sale_key');

                $items = SaleCart::where('sale_key', $saleKey)->where(function($query) use ($request) {
                    //     $empID      = $request->get('emp_id');
                    //     if( $empID != '' ){
                    //         $query->where('emp_id',$empID);
                    //     }
                })->orderBy('id','ASC');
                
                $items = $items->get();

                $totalBill = 0; 
                $totalDiscount = 0;
                $subTotal = 0;
                $billAmount = 0;

                foreach($items as $res ){
                    $subTotal       += $res->offer_price * $res->qty;
                    $grandTotal     =  ( ($res->offer_price - $res->discount) * $res->qty);
                    $totalDiscount  += $res->discount * $res->qty;

                }
                $billAmount = $subTotal - $totalDiscount; 
                return Datatables::of($items)
                ->addColumn('discount', function ($row){
                    return ($row->discount)?$row->discount:0;
                })->addColumn('offer_price', function ($row) {
                    return $row->offer_price;
                })->addColumn('product_total_price', function ($row) {
                    return  round( ($row->offer_price - $row->discount) * $row->qty);    
                })->addColumn('action', function ($row) {
                    $dd =  '<a href="javascript:void(0);" onclick="deleteCartItem('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                    return $dd;
                })
                ->rawColumns(['item_count','action'])
                ->with('totalPrice', function() use ($totalBill) {
                    return round($totalBill);
                })->with('billAmount', function() use ($billAmount) {
                    return round($billAmount);
                })->with('subTotal', function() use ($subTotal) {
                    return round($subTotal);
                })->with('totalDiscount', function() use ($totalDiscount) {
                    return round($totalDiscount);
                })
                ->make(true);
            }
        }
        
    }


    function getBillTotalAmounts(Request $request){
        $saleKey = $request->get('sale_key');

        $items = SaleCart::where('sale_key', $saleKey)->where(function($query) use ($request) {
            //     $empID      = $request->get('emp_id');
            //     if( $empID != '' ){
            //         $query->where('emp_id',$empID);
            //     }
        })->orderBy('id','ASC');
        
        $items = $items->get();

        $totalBill = 0; 
        $totalDiscount = 0;
        $subTotal = 0;
        $billAmount = 0;

        foreach($items as $res ){
            $subTotal       += $res->offer_price * $res->qty;
            $grandTotal     =  ( ($res->offer_price - $res->discount) * $res->qty);
            $totalDiscount  += $res->discount * $res->qty;

        }
        $billAmount = $subTotal - $totalDiscount;


        /*
        
        ->with('totalPrice', function() use ($totalBill) {
                    return $totalBill;
                })->with('billAmount', function() use ($billAmount) {
                    return $billAmount;
                })->with('subTotal', function() use ($subTotal) {
                    return $subTotal;
                })->with('totalDiscount', function() use ($totalDiscount) {
                    return $totalDiscount;
                })
        
        */

        return response()->json([
            'totalPrice' => $totalBill,
            'billAmount' => $billAmount,
            'subTotal' => $subTotal,
            'totalDiscount' => $totalDiscount

        ]);


    }
    
    
    public function addToCart(Request $request){

        $validator = Validator::make($request->all(), [
            'product_id'    => 'required',
            'qty'           => 'required',
            'action'        => 'required',
            'sale_key'      => 'required'
        ]);
 
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()],406);
        }
        $productID  = $request->get('product_id');
        $qty        = (int) $request->get('qty');
        $action     = $request->get('action');
        $saleKey    = $request->get('sale_key');
        $shopID     = auth()->user()->shop_id;

        if($qty < 1){
            return response()->json(['status' => false, 'message' => 'Invalid Qty.'],406);
        }

        if(!($shopID)){
            return response()->json(['status' => false, 'message' => 'User have no shop assign. Please select shop first'],406);
        }


        $prodcutAvail = ProductAvailable::where(['product_id' => $productID, 'shop_id' => $shopID ])->first();
        $availableQty = $prodcutAvail ? (int)$prodcutAvail->qty : 0;

        if ($availableQty < 1) {
            return response()->json(['status' => false, 'message' => 'Product not available in stock.'], 406);
        }

        $product = Product::find($productID);
        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found.'], 404);
        }

        $itemObj = SaleCart::where('product_id', $productID)->where('sale_key', $saleKey)->first();

        $targetQty = $qty;
        if ($itemObj && $action === 'add') {
            $targetQty = $itemObj->qty + $qty;
        }

        if ($targetQty > $availableQty) {
            return response()->json([
                'status' => false, 
                'message' => "Requested quantity ({$targetQty}) exceeds available stock ({$availableQty})."
            ], 406);
        }

        if ($itemObj) {
            $finalPrice = $itemObj->offer_price - $itemObj->discount;

            $item = SaleCart::where(['product_id' => $productID, 'sale_key' => $saleKey])->update([
                'product_id'  => $product->id,
                'final_price' => $finalPrice, 
                'qty'         => $targetQty,
                'sale_key'    => $saleKey,
                'shop_id'     => $shopID
            ]);
        } else {
            $item = SaleCart::create([
                'product_id'    => $product->id,
                'product_name'  => $product->name,
                'orignal_price' => $product->price,
                'offer_price'   => $product->price,
                'final_price'   => $product->price,
                'qty'           => $targetQty,
                'sale_key'      => $saleKey,
                'shop_id'       => $shopID
            ]);
        }
        return response()->json(['status' => true, 'data' => $item , 'message' => 'Done']);
    }


    function deleteCartItem(Request $request){
        $itemID = $request->get('item_id');
        SaleCart::find($itemID)->delete();
        return response()->json(['status' => true, 'message' => 'Deleted Successfully']);
    }



    function applyDiscountOnSingleProduct(Request $request){
        $validator = Validator::make($request->all(), [
            'product_id'    => 'required',
            'sale_key'      => 'required',
            'discount_amount' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()],406);
        }
        $productID  = $request->get('product_id');
        $qty        = (int) $request->get('qty');
        $action     = $request->get('action');
        $saleKey    = $request->get('sale_key');
        $shopID     = auth()->user()->shop_id;
        $discountAmount = $request->get('discount_amount');

        if(!($shopID)){
            return response()->json(['status' => false, 'message' => 'User have no shop assign. Please select shop first'],406);
        }
        $itemObj = SaleCart::where('product_id', $productID)->where('sale_key',$saleKey)->first();
        $itemObj->discount =  $discountAmount;
        $itemObj->final_price =  ($itemObj->offer_price - $discountAmount);
        $itemObj->save();
        return response()->json(['status' => true, 'data' => $itemObj , 'message' => 'Done']);
    }

    function updatePriceOnSingleProduct(Request $request){
        $validator = Validator::make($request->all(), [
            'product_id'    => 'required',
            'sale_key'      => 'required',
            'product_price' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()],406);
        }
        $productID  = $request->get('product_id');
        $qty        = (int) $request->get('qty');
        $action     = $request->get('action');
        $saleKey    = $request->get('sale_key');
        $shopID     = auth()->user()->shop_id;
        $productPrice = $request->get('product_price');

        if(!($shopID)){
            return response()->json(['status' => false, 'message' => 'User have no shop assign. Please select shop first'],406);
        }
        $itemObj = SaleCart::where('product_id', $productID)->where('sale_key',$saleKey)->first();

        $finalPrice = ($productPrice - $itemObj->discount);

       //  dd($itemObj);
       // $itemObj->discount =  $discountAmount;
        $itemObj->offer_price =  $productPrice; // - $discountAmount;
        $itemObj->final_price = $finalPrice;
        $itemObj->save();
        return response()->json(['status' => true, 'data' => $itemObj , 'message' => 'Done']);
    }

    function searchProducts(Request $request){
        
        $loginShopID = auth()->user()->shop_id;
        $groupBy = $request->get('group_by');
        $shopID = $request->get('shop_id',$loginShopID);
        $term = $request->get('term');

        $data = ProductAvailable::
        select('*',DB::raw('sum(qty) as total_qty'))
        ->with(['product','shop:id,name'])
        ->whereHas('product', function($q) use ($term){
            $q->where('name','like','%'.$term.'%');
        })
        ->where('shop_id',$shopID)
        ->groupBy(DB::raw('product_id'))->get();

        $results = []; 
        foreach($data as $row):
            $results[] = [
                'id' => $row->product_id, 
                'shop_id' => $row->shop_id,
                'total_qty' => $row->total_qty, 
                'product_id' => $row->product_id,
                'product_img_url' => 'http://devlocal.pos.com/assets/images/placeholder/noimage.png',
                'text' => $row->product->name
            ];
        endforeach;
        return ['results' => $results]; 
    }



    public function cartPreview($saleKey,Request $request){


       //  $saleKey = $request->get('sale_key');

        $items = SaleCart::where('sale_key', $saleKey)->where(function($query) use ($request) {
            //     $empID      = $request->get('emp_id');
            //     if( $empID != '' ){
            //         $query->where('emp_id',$empID);
            //     }
        });
        
        $items = $items->get();

        $totalBill = 0; 
        $totalDiscount = 0;
        $subTotal = 0;
        $billAmount = 0;

        foreach($items as $res ){
            $subTotal       += $res->price * $res->qty;
            $grandTotal     =  ( ($res->offer_price - $res->discount) * $res->qty);
            $totalDiscount  += $res->discount;

        }

        $billAmount = $subTotal - $totalDiscount; 

        $shop = Shop::find(auth()->user()->shop_id);

        $saleCartWithCustomer = SaleCart::where('sale_key', $saleKey)->whereNotNull('customer_id')->first();

// dd($customer);

        $customer = null; 

        if($saleCartWithCustomer){
            $customer = Customer::find($saleCartWithCustomer->customer_id);
        }
        


        return view('sale.cart_preview',[
            'shop' => $shop,
            'customer' => $customer,
            'sale_key' => $saleKey,
            'products' => $items,
            'sub_total' => $subTotal,
            'discount_total' => $totalDiscount,
            'bill_amount' => $billAmount
        ]);



        // dd($items);        
        $for = $request->get('for');
        if( !($request->ajax()) ){
            return view('sale.cart_preview');
        }else{
            if($for == 'ajax'){
                $results = SaleCart::all();
                return response()->json(['status' => true, 'data' => $results]);
            }elseif($for == 'datatable'){
                $items = SaleCart::where(function($query) use ($request) {
                    //     $empID      = $request->get('emp_id');
                    //     if( $empID != '' ){
                    //         $query->where('emp_id',$empID);
                    //     }
                });
                
                $items = $items->get();

                $totalBill = 0; 

                foreach($items as $res ){
                   //  $res->id;
                    $price =  ( ($res->offer_price - $res->discount) * $res->qty);
                    $totalBill += $price; 
                }

                // echo $totalBill; exit;


                return Datatables::of($items)
                ->addColumn('discount', function ($row){
                    return ($row->discount)?$row->discount:0;
                })->addColumn('price', function ($row) {
                    return ( ($row->offer_price - $row->discount) * $row->qty);    
                })->addColumn('action', function ($row) {
                })->rawColumns(['item_count'])
                ->with('totalPrice', function() use ($totalBill) {
                    return $totalBill;
                })
                ->make(true);
            }
        }
    }


    function getItemStock($productID, $qty){
        
        $sql = '
        
        SELECT 
        q1.pro,
        q1.avail_qty,
        (@runtot := @runtot + q1.avail_qty) AS rt
     FROM 
        (SELECT product_id AS pro,
         available_qty AS avail_qty
         FROM  stock_items 
         WHERE available_qty > 0 AND product_id = '.$productID.'
         ORDER  BY available_qty
         ) AS q1
    WHERE @runtot + q1.avail_qty >= '.$qty;
        
        DB::raw();
    }



    
    public function finalPlaceOrder(Request $request){
        $validator = Validator::make($request->all(), [
            'sale_key'   => 'required'
        ]);

        $customerID = $request->get('customer_id');
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 406);
        }

        $saleKey            = $request->get('sale_key');
        $cartItems          = SaleCart::where('sale_key', $saleKey)->get();  
        $itemFound          = count($cartItems);
        if ($itemFound == 0) {
            return response(['message' => 'No items available'], 422);
        }

        $paymentMethod = $request->get('payment_method', 'cash'); // cash, credit, bank_transfer, split
        $paidAmountReq = $request->has('paid_amount') ? round((float) $request->get('paid_amount'), 2) : null;
        $dueAmountReq  = $request->has('due_amount') ? round((float) $request->get('due_amount'), 2) : null;

        // Non-negotiable rule: Credit sales require registered customer (no guest checkout for credit)
        if (in_array($paymentMethod, ['credit', 'split'])) {
            if (!$customerID || (int)$customerID <= 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Credit / On-Account sales require a registered customer. Guest checkout is prohibited for ledger credit.'
                ], 422);
            }
        }

        DB::beginTransaction();
        $cashReceived = (float) $request->get('cash_received', 0);
        $cashReturned = (float) $request->get('cash_returned', 0);

        try {
            $saleOrder = SaleOrder::create([
                'sale_key' => $saleKey,
                'item_count' => count($cartItems), 
                'customer_id' => $customerID ? (int)$customerID : null,
                'shop_id' => auth()->user()->shop_id, 
                'created_by' => auth()->user()->id,
                'cash_received' => (int) $cashReceived,
                'cash_returned' => (int) $cashReturned,
            ]);

            $totalOfferPrice = 0;
            $totalPrice = 0;
            $totalDiscount = 0;
            $totalCogs = 0;
            $itemWillDelete = [];

            foreach ($cartItems as $tItem) {
                $shopID = auth()->user()->shop_id;
                $proAvail = ProductAvailable::where('product_id', $tItem->product_id)->where('shop_id', $shopID)->first();
                $product = Product::find($tItem->product_id);

                if (!$proAvail) {
                    throw new Exception("Product (" . ($product ? $product->name : $tItem->product_id) . ") not available in current shop stock!");
                }

                if (($tItem->qty < 1) || ($proAvail->qty < 1) || ($proAvail->qty < $tItem->qty)) {
                    throw new Exception("Product (" . ($product ? $product->name : $tItem->product_id) . ") insufficient stock! Requested: {$tItem->qty}, Available: {$proAvail->qty}");
                }

                $costPrice = ($product && $product->cost_price > 0) ? (float) $product->cost_price : 0.00;

                $itemData = [
                    'product_id' => $tItem->product_id,
                    'order_id' => $saleOrder->id,
                    'customer_id' => $saleOrder->customer_id,
                    'qty' => $tItem->qty,
                    'offer_price' => $tItem->offer_price,
                    'discount' => $tItem->discount,
                    'price' => $tItem->final_price,
                    'cost_price' => $costPrice,
                ];

                $lineTotal = ($tItem->final_price * $tItem->qty);
                $totalOfferPrice += ($tItem->offer_price * $tItem->qty);
                $totalPrice += $lineTotal;
                $totalDiscount += ($tItem->discount * $tItem->qty);
                $totalCogs += ($costPrice * $tItem->qty);

                SaleOrderItem::create($itemData);

                ProductAvailable::manageStockByShopAndProductId(
                    $tItem->qty,
                    StockAudit::STOCK_ACTION_MINUS,
                    $tItem->product_id,
                    $shopID,
                    StockAudit::STOCK_OBJECT_TYPE_SALE,
                    $saleOrder->id
                );

                $itemWillDelete[] = $tItem->id;
            }

            $totalPrice = round($totalPrice, 2);

            // Determine financial distribution
            if ($paymentMethod === 'credit') {
                $paidAmount = 0.00;
                $dueAmount = $totalPrice;
                $paymentStatus = 'due';
            } elseif ($paymentMethod === 'split') {
                $paidAmount = ($paidAmountReq !== null) ? min($paidAmountReq, $totalPrice) : 0.00;
                $dueAmount = round($totalPrice - $paidAmount, 2);
                $paymentStatus = ($dueAmount <= 0) ? 'paid' : (($paidAmount <= 0) ? 'due' : 'partial');
            } else { // cash or bank_transfer
                $paidAmount = $totalPrice;
                $dueAmount = 0.00;
                $paymentStatus = 'paid';
            }

            $saleOrder->total_offer_price = $totalOfferPrice;
            $saleOrder->total_discount = $totalDiscount;
            $saleOrder->total_price = (int) $totalPrice;
            $saleOrder->total_amount = $totalPrice;
            $saleOrder->paid_amount = $paidAmount;
            $saleOrder->due_amount = $dueAmount;
            $saleOrder->payment_status = $paymentStatus;
            $saleOrder->payment_method = $paymentMethod;
            $saleOrder->save();

            // Clear temporary cart items
            SaleCart::whereIn('id', $itemWillDelete)->delete();

            // Record Double-Entry Ledger Transaction
            $ledgerService = app(LedgerService::class);
            $ledgerService->recordSaleLedger($saleOrder, [
                'total_amount' => $totalPrice,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_method' => $paymentMethod,
                'cogs_amount' => $totalCogs,
                'shop_id' => $shopID,
            ]);

            DB::commit();
            return response(['status' => true, 'message' => 'Order created successfully!', 'data' => $saleOrder]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 422);
        }
    }


    function saveUserAndAttachWithCart(Request $request){
        $validator = Validator::make($request->all(), [
            'customer_name'     => 'required',
            'customer_mobile'   => 'required',
            'sale_key'          => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()],406);
        }
        $array = array(
            'name' => $request->get('customer_name'),
            'mobile' => $request->get('customer_mobile')
        );

        try{
            DB::beginTransaction();
            $rec = Customer::create($array);
            DB::commit();
            return redirect(route('customer.list'))->with('success',' added successful!');
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->back()->withInput($request->all())->with('error','Error.Please Contact   Support');
        }
    }


    function attachCustomerWithCart(Request $request){
        $customerID = $request->get('customer_id');
        $saleKey            = $request->get('sale_key');
        if($customerID && $saleKey){
            SaleCart::where('sale_key',$saleKey)->update(['customer_id' => $customerID ]);
            return response(['status' => true, 'message' => 'Customer attched successfully!']);
        }else{
            return response()->json(['status' => false, 'message' => 'Information Missed!'],406);
        }

    }


            /********************************/
        // $customerName   = $request->get('customer_name');
        // $customerMobile = $request->get('customer_mobile');
        // $customerID     = 0;
        // if( ($customerName != '') && ($customerMobile != '') ){
        //     $customer = Customer::where('mobile',$customerMobile)->first();
        //     if( isset($customer->id) ){
        //         $customerID = $customer->id;
        //     }else{
        //         $customer   = Customer::create(['name' => $customerName, 'mobile' => $customerMobile]);
        //         $customerID = $customer->id;
        //     }
        // }
        /********************************/




}
