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
                ->addColumn('vehicle_no', function ($row) {
                    return $row->vehicle_no ?: '-';
                })
                ->addColumn('bilty_no', function ($row) {
                    return $row->bilty_no ?: '-';
                })
                ->addColumn('action', function ($row) {
                    $action = '<div class="btn-group">';
                    $action .= '<a href="'.route('sale.order.detail',$row->id).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
                    $action .= '<a href="'.route('sale.order.edit',$row->id).'" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit Order" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';
                    $action .= '<a href="'.route('sale.order.sale_return_view',$row->id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Sale Return" ><i class="ace-icon fa fa-undo bigger-120"></i></a>';
                   //  $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->sale_key.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                    
                    // 
                    
                    $action .= '</div>';
                    return $action;
                })->rawColumns(['title','product_attribute','status_label','action'])
                ->make(true);
            }else{
                return view('order.lists');
            }     
        }

        public function edit($orderID){
            $order      = SaleOrder::findOrFail($orderID);
            $orderItem  = SaleOrderItem::with('product')->where('order_id',$order->id)->get();
            $customer   = Customer::find($order->customer_id);
            $shop       = Shop::find($order->shop_id);
            $manager    = User::find($order->created_by);

            return view('order.edit', [
                'order' => $order,
                'orderItem' => $orderItem,
                'customer' => $customer,
                'shop' => $shop,
                'manager' => $manager
            ]);
        }

        public function update(Request $request, $orderID){
            $validator = Validator::make($request->all(), [
                'vehicle_no'     => 'nullable|string|max:191',
                'vehicle_number' => 'nullable|string|max:191',
                'bilty_no'       => 'nullable|string|max:191',
                'bilty_number'   => 'nullable|string|max:191',
            ]);

            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
                }
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $order = SaleOrder::findOrFail($orderID);

            // Ensure the values remain unchanged when updating an existing order unless explicitly edited
            if ($request->exists('vehicle_no')) {
                $order->vehicle_no = $request->input('vehicle_no');
            } elseif ($request->exists('vehicle_number')) {
                $order->vehicle_no = $request->input('vehicle_number');
            }

            if ($request->exists('bilty_no')) {
                $order->bilty_no = $request->input('bilty_no');
            } elseif ($request->exists('bilty_number')) {
                $order->bilty_no = $request->input('bilty_number');
            }

            $order->save();

            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Order updated successfully!',
                    'data' => [
                        'id' => $order->id,
                        'vehicle_no' => $order->vehicle_no,
                        'bilty_no' => $order->bilty_no,
                    ]
                ]);
            }

            return redirect()->route('sale.order.detail', $order->id)->with('success', 'Order updated successfully!');
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
                'return_qty'        => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()],406);
            }

            $orderID        = $request->get('order_id');
            $items          = $request->get('order_item_id');
            $returnsQty     = $request->get('return_qty');
            $order          = SaleOrder::findOrFail($orderID);
            $loginShopID    = auth()->user()->shop_id ?? $order->shop_id; 
            $desc           = $request->get('description');

            try {
                DB::beginTransaction();

                $saleReturn = SaleReturn::create([
                    'order_id'    => $orderID,
                    'description' => $desc,
                    'shop_id'     => $loginShopID,
                    'customer_id' => $order->customer_id
                ]);

                $hasReturns = false;
                $index = 0;
                foreach($items as $item){
                    $returnQty = isset($returnsQty[$index]) ? (int)$returnsQty[$index] : 0;
                    $index++;
                    if ($returnQty <= 0) {
                        continue;
                    }

                    $saleOrder = SaleOrderItem::findOrFail($item);

                    // Check already returned quantity for this order and product
                    $alreadyReturned = (int)SaleReturnItem::whereHas('saleReturn', function($q) use ($orderID) {
                        $q->where('order_id', $orderID);
                    })->where('product_id', $saleOrder->product_id)->sum('qty');

                    $maxReturnable = $saleOrder->qty - $alreadyReturned;
                    if ($returnQty > $maxReturnable && $maxReturnable > 0) {
                        throw new \Exception("Return quantity ({$returnQty}) exceeds remaining returnable quantity ({$maxReturnable}) for item.");
                    }

                    SaleReturnItem::create([
                        'sale_return_id' => $saleReturn->id,
                        'product_id'     => $saleOrder->product_id,
                        'qty'            => $returnQty,
                        'shop_id'        => $loginShopID
                    ]);

                    // Restock at the login shop physically receiving the item
                    ProductAvailable::manageStockByShopAndProductId(
                        $returnQty, 
                        StockAudit::STOCK_ACTION_ADD,
                        $saleOrder->product_id, 
                        $loginShopID, 
                        StockAudit::STOCK_OBJECT_TYPE_SALE_RETURN,
                        $saleReturn->id
                    );

                    $hasReturns = true;
                }

                if (!$hasReturns) {
                    throw new \Exception("No valid return quantities entered.");
                }

                DB::commit();
                return response()->json(['status' => true, 'message' => 'Sale return processed successfully', 'url' => route('sale.order.detail',$orderID) ],200);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
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
