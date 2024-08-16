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
use App\Models\User;
use App\Models\SaleOrder;
use App\Models\SaleOrderItem;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\Shop;
use App\Models\StockItem;
use App\Models\StockAdjustment;
use App\Models\StockAudit;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Validator;

class SaleOrderController extends Controller{

        function index(Request $request){
            if($request->ajax()){
                $groupBy    = $request->get('group_by');
                $items      = SaleOrder::with('shop')->where(function($query) use ($request) {
                    if($request->get('customer_id') != ''){
                        $query->where('customer_id',$request->get('customer_id'));
                    }
                    if(auth()->user()->hasRole('Super Admin')){
                        
                    }else{
                        $query->where('shop_id',auth()->user()->shop_id);
                    }
                });
                
                $items = $items->get();
                return Datatables::of($items)
                ->addColumn('title', function ($row) {
                    return '<a href="'.route('sale.order.detail',$row->id).'" target="_blank" >'.$row->sale_key.'</a>';
                })
                ->addColumn('item_count', function ($row) {
                    return $row->item_count;
                })->addColumn('order_time', function ($row) {
                    return $row->created_at->format('h:i / d-m-Y');
                })->addColumn('shop_name', function ($row) {
                    return $row->shop->name;
                })->addColumn('customer_name', function ($row) {
                    if($row->customer_id == 0){
                        return 'Walking Customer';
                    }else{
                        return Customer::find($row->customer_id)->name;
                    }
                })
                ->addColumn('action', function ($row) {
                    $action = '<div class="btn-group">';
                    $action .= '<a href="'.route('sale.order.detail',$row->id).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
                    $action .= '<a href="'.route('sale.order.sale_return_view',$row->id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Sale Return" ><i class="ace-icon fa fa-undo bigger-120"></i></a>';
                    $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->sale_key.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                    
                    // 
                    
                    $action .= '</div>';
                    return $action;
                })->rawColumns(['title','product_attribute','status_label','action'])
                ->make(true);
            }else{
                return view('order.lists');
            }     
        }

        function detail($orderID,Request $request){

            $viewType   = $request->get('view');
            $order      = SaleOrder::findOrFail($orderID);
            $orderItem  = SaleOrderItem::with('product')->where('order_id',$order->id)->get();
            $customer   = Customer::find($order->customer_id);
            $shop       = Shop::find($order->shop_id);
            $manager    = User::find($order->created_by);
            $view       = 'order.detail';
            $data = [
                'viewType' => $viewType,
                'order' => $order,
                'orderItem' => $orderItem,
                'shop' => $shop,
                'customer' => $customer,
                'manager' => $manager
            ];
            if($viewType == 'print'){
                $data['hideInfo'] = false; 
                $view = 'order.order_detail_table';
            }
            return view($view,$data);
        }


        function OrderBackSearchModal(){
            
            return response()->json([
                'view' => View('order.order_back_search_modal',[])->render(),
                'data' =>   [],
                'status' => true,
                'message' => 'OK'
            ]);
        }


        function saleReturnView($orderID){

            $order      = SaleOrder::findOrFail($orderID);
            $orderItem  = SaleOrderItem::with('product')->where('order_id',$order->id)->get();
            $customer   = Customer::find($order->customer_id);
            $shop       = Shop::find($order->shop_id);
            $manager    = User::find($order->created_by);
            $view       = 'order.return_sale';
            $data = [
                'order' => $order,
                'orderItem' => $orderItem,
                'shop' => $shop,
                'customer' => $customer,
                'manager' => $manager
            ];
            return view($view,$data);
        }


        function saleReturnSave(Request $request){
            $validator = Validator::make($request->all(), [
                'order_id'          => 'required',
                'order_item_id'     => 'required',
                'product_id'        => 'required',
                'qty'               => 'required',
                'return_qty'        => 'required',
                'order_item_price'  => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()],406);
            }

            $orderID    = $request->get('order_id');
            $items      = $request->get('order_item_id');
            $returnsQty = $request->get('return_qty');
            $order      = SaleOrder::findOrFail($orderID);
            $shopID     = $order->shop_id; 
            $loginShopID    = auth()->user()->shop_id; 
            $desc           = $request->get('description');

            $saleReturn = SaleReturn::create([
                'order_id' => $orderID,
                'description' => $desc,
                'shop_id' => $loginShopID,
                'customer_id' => $order->customer_id
            ]);

            $index = 0;
            foreach($items as $item){
                $returnQty = $returnsQty[$index];
                $index++;
                if( ($returnQty == 0)){
                   
                    continue;
                }
                $saleOrder = SaleOrderItem::find($item);
                $saleOrder->qty = $saleOrder->qty - $returnQty;
                $saleOrder->save();

                SaleReturnItem::create([
                    'sale_return_id' => $saleReturn->id,
                    'product_id' => $saleOrder->product_id,
                    'qty' => $returnQty,
                    'shop_id' => $loginShopID
                ]);

                ProductAvailable::manageStockByShopAndProductId($returnQty, StockAudit::STOCK_ACTION_ADD,$saleOrder->product_id, $shopID, StockAudit::STOCK_OBJECT_TYPE_SALE_RETURN,$saleReturn->id);

               // ProductAvailable::where(['shop_id' => $shopID, 'product_id' => $saleOrder->product_id])->increment('qty', $returnQty);
               
            }


            // $priceOffer = SaleOrderItem::where('order_id', $orderID)->sum('offer_price');
            // $priceOffer = SaleOrderItem::where('order_id', $orderID)->sum('offer_price');

            $saleItems          = SaleOrderItem::where('order_id', $orderID)->get();
            $priceOfferTotal    = 0;
            $discountTotal      = 0;
            $priceTotal         = 0; 
            foreach($saleItems as $saleitem){
                $priceOfferTotal = $priceOfferTotal + ( $saleitem->qty *  $saleitem->offer_price );
                $discountTotal   = $discountTotal + ( $saleitem->qty *  $saleitem->discount );
                $priceTotal      = $priceTotal + ( $saleitem->qty *  $saleitem->price );
            }

            $order->total_offer_price   = $priceOfferTotal;
            $order->total_discount      = $discountTotal;
            $order->total_price         = $priceTotal;

            $order->save();

            return response()->json(['status' => true, 'message' => 'Done', 'url' => route('sale.order.detail',$orderID) ],200);

        }

        function unknowSaleReturnView(Request $request){

            $data['products'] = Product::with('productQtyShopWise')->get();
            return view('sale.unknow_sale_return_form',$data);
        }


        function unknowSaleReturnSave(Request $request){
            $validator = Validator::make($request->all(), [
                'product_id'    => 'required',
                'return_qty'    => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()],406);
            }
    
            $productIDzArr  = $request->get('product_id');
            $stockArr       = $request->get('stock');
            $qtyArr         = $request->get('return_qty');
            $desc           = $request->get('description');
            $customerID     = $request->get('customer_id');
            
            if( count($productIDzArr) > 0 && !($productIDzArr[0]) ){
                return response()->json(['status' => false, 'message' => 'No Product Found'],406);
            }
    
            if( count($qtyArr) > 0 && !($qtyArr[0]) ){
                return response()->json(['status' => false, 'message' => 'No Quantity Found'],406);
            }
            $loginShopID    = auth()->user()->shop_id; 
            $index          = 0;
            try{
                DB::beginTransaction();
                
                $saleReturn = SaleReturn::create([
                    'description' => $desc,
                    'shop_id' => $loginShopID,
                    'customer_id' => $customerID
                ]);
    
            foreach($productIDzArr as $id){
                $productID  = $productIDzArr[$index];
               //  $stock      = $stockArr[$index];
                $qty        = (int) $qtyArr[$index];
                $index++;
                if( $qty < 1 ){
                    continue;
                }
                
                SaleReturnItem::create([
                    'sale_return_id' => $saleReturn->id,
                    'product_id' => $productID,
                    'qty' => $qty,
                    'shop_id' => $loginShopID
                ]);

                ProductAvailable::manageStockByShopAndProductId($qty,StockAudit::STOCK_ACTION_ADD, $productID,
                $loginShopID, StockAudit::STOCK_OBJECT_TYPE_SALE_RETURN, $saleReturn->id);
            

                /*
                $product = ProductAvailable::where([
                    'shop_id'       => $loginShopID,
                    'product_id'    => $productID
                ])->first();
    
                if($product){
                    $product->qty = $product->qty + $qty;
                    $product->save();
                }else{
                    ProductAvailable::create([
                        'shop_id'       => $loginShopID,
                        'product_id'    => $productID,
                        'qty' => $qty
                    ]);
                }
                */
            }
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Successful']);
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['message' => 'Error.Please Contact with Support. '. $e->getMessage()], 500);
        }

        }


    }
