<?php

namespace App\Http\Controllers\StockExchange;

use App\Http\Controllers\Controller;
use App\Models\IssuanceItem;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

use App\Models\ParentCategory;
use App\Models\ProductAvailable;
use App\Models\StockItem;
use App\Models\Shop;
use App\Models\Product;
use App\Models\StockExchange;
use App\Models\SubCategory;
use App\Models\Uom;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Exception;



class StockExchangeController extends Controller{

    function requestForm($exchangeKey=''){
        if($exchangeKey == ''){
            $exchangeKey = time();
        }
        $data['shops']  = Shop::all()->pluck('name','id');
        $data['exchangeKey'] = $exchangeKey;
        return view('stock_exchange.create_request',$data); 
    }

    function indexList(){
        return view('stock_exchange.lists'); 
    }


    function deleteCartItem(Request $request){
        $itemID = $request->get('item_id');
      //   dd(StockExchange::find($itemID));
        StockExchange::find($itemID)->delete();
        return response()->json(['status' => true, 'message' => 'Deleted Successfully']);
    }



    function placeRequest(Request $request){


        $validator = Validator::make($request->all(), [
            'item_ids'   => 'required'
        ]);

//         $customerID = $request->get('customer_id');
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()],406);
        }

        $itemsIDs           = $request->item_ids;
        $exchangeItems          = StockExchange::whereIn('id',$itemsIDs)->get();  
        $itemFound          = count($exchangeItems);
        if($itemFound == 0){
            return response(['message' => 'No items available'],422);
        }

      //   dd($exchangeItems);



        try{
            DB::beginTransaction();

            foreach($exchangeItems as $item):
                
                $reqQty         = $item->req_qty;
                $reqToShopID    = $item->req_receive_shop_id;
                $reqFromShopID  = $item->req_send_shop_id;
                $productID      = $item->product_id;

                $productAvail = ProductAvailable::where(['shop_id' => $reqToShopID, 'product_id' => $productID])->first();

                if($productAvail && ($productAvail->qty > 0) ){
                    
                    $productAvail->qty = $productAvail->qty - $reqQty;
                    $productAvail->save();


                    $item->status = 'post';
                    $item->save();


                 //    ProductAvailable::where(['shop_id' => $reqToShopID, 'product_id' => $productID])->first();
                $reqProductAvailable = ProductAvailable::where([
                    'product_id' => $productID, 
                    'shop_id'=> $reqFromShopID
                ])->first();
                    if($reqProductAvailable){
                        $reqProductAvailable->qty = $reqProductAvailable->qty + $reqQty;
                        $reqProductAvailable->save();
                    }else{
                        ProductAvailable::create([
                            'product_id' => $productID, 
                            'shop_id'=> $reqFromShopID,
                            'qty' => $reqQty
                        ]);
                    }



                }else{
                    throw new Exception("Product Qty not available");
                }
            endforeach;
            DB::commit();

            return response()->json(['status' => true, 'message' => 'Successfully Posted.'], 200);
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Error.Please Contact   Support'. $e->getMessage()], 500);
        }    






        // $saleOrder          = SaleOrder::create(['sale_key' => $saleKey, 'item_count' => count($cartItems), 'customer_id' => $customerID, 'shop_id' => auth()->user()->shop_id , 'created_by' => auth()->user()->id]);
        // $notFound           = 0;
        // $itemWillDelete     = [];

        // try{
        //     DB::beginTransaction();
        //     $totalOfferPrice    = 0;
        //     $totalPrice = 0;
        //     $billAmount = 0;
        //     $totalDiscount      = 0;
        //     foreach($cartItems as $tItem):
        //         //   echo $tItem->product_id; exit;
        //         //  $stockItem = StockItem::findOrFail($tItem->item_id);
        //         $shopID = auth()->user()->shop_id;
        //         $proAvail = ProductAvailable::where('product_id',$tItem->product_id)->where('shop_id',$shopID)->first();

        //       //   print_r($proAvail); exit;
        //         if( !($proAvail) ){
        //             $notFound++;
        //             continue;
        //         }

        //         if( ($tItem->qty < 1) || ($proAvail->qty < 1) || ($proAvail->qty <  $tItem->qty) ){
        //             $notFound++;
        //             continue;
        //         }
        //         // sale cart ==> ['product_id', 'product_name', 'item_id', 'sale_key', 'qty', 'offer_price', 'price', 'status', 'created_by','updated_by','deleted_by' ];
        //         // sale orer item ==>  ['product_id', 'item_id', 'sale_key', 'order_id', 'customer_id', 'qty', 'offer_price', 'discount', 'price', 'status']; 

        //         $itemData['product_id']     =    $tItem->product_id;
        //         // $itemData['item_id']        =    $stockItem->id;
        //         $itemData['order_id']       =    $saleOrder->id;
        //         $itemData['customer_id']    =    $saleOrder->customer_id;
        //         $itemData['qty']            =    $tItem->qty;
                                
        //         $itemData['offer_price']    =    $tItem->offer_price;
        //         $itemData['discount']       =    $tItem->discount;
        //         $itemData['price']          =    $tItem->final_price;

        //         $totalOfferPrice            =    $totalOfferPrice + ( $tItem->offer_price * $tItem->qty );
        //         $totalPrice                 =    $totalPrice + ( $tItem->final_price * $tItem->qty ) ;
        //         $totalDiscount              =    $totalDiscount + ( $tItem->discount * $tItem->qty );
                

        //         SaleOrderItem::create($itemData);

        //         $proAvail->qty = $proAvail->qty - $tItem->qty;
        //         $proAvail->save();


        //         /*
        //         $stockItem->available_qty = $stockItem->available_qty - $tItem->qty;
        //         $stockItem->save();
        //         */

        //         $itemWillDelete[] = $tItem->id;
        //     endforeach;

        //     $saleOrder->total_offer_price   = $totalOfferPrice;
        //     $saleOrder->total_discount      = $totalDiscount;
        //     $saleOrder->total_price         = $totalPrice;
        //     $saleOrder->save();

        //     DB::commit();
        //  //    dd('testing');

        //  //   SaleCart::whereIn('id',$itemWillDelete)->delete();

        //     if( $notFound > 0 ){
        //         DB::rollBack();
        //     }else{
        //         DB::commit();
        //     }

        //     if( $notFound > 0 ){
        //         return response(['status' => false, 'message' => $notFound.' items not available in quantity']);
        //     }else{
        //         return response(['status' => true, 'message' => 'Order created successfully!', 'data' => $saleOrder]);
        //     }            
        // }catch(\Exception $e){
        //     return response()->json(['status' => false, 'message' => 'Error.Please Contact   Support'. $e->getMessage()], 500);
        // }
    }



    function getRequestCart(Request $request){

        $userID      = auth()->user()->id;
        $deliveryID  = $request->get('stock_delivery_id');
        $exchangeKey = $request->get('exchange_key');
        $groupBy     = $request->get('group_by_for');
        $loginShopID = auth()->user()->shop_id;
        
        $listFor    = $request->get('list_for');
        $whereArr   = [];
        
        /*
        if( $listFor == 'send' ){
            $whereArr['req_send_shop_id']    = $loginShopID;
        }elseif( $listFor == 'receive' ){
            $whereArr['req_receive_shop_id'] = $loginShopID;
        }elseif( $listFor == 'detail' ){
             $whereArr['exchange_key']        = $exchangeKey;
        }
        */

        $whereArr['req_send_shop_id']    = $loginShopID;
        $whereArr['status']    = 'darft';
        $results    = StockExchange::with(['product'])->where(function($query) use ($whereArr){
            $query->where($whereArr);
        }); // ->get();

        // if($groupBy == 'exchange_key'){
        //     $results = $results->select('*',DB::raw('count(*) as total_items'))->groupBy('exchange_key');
        // }

         return Datatables::of($results)
            ->addColumn('product_name', function ($row) {
                return optional($row->product)->name;
            })->addColumn('uom_code', function ($row) {
                 return optional($row->product)->code;
            })->addColumn('req_qty', function ($row) {
                return $row->req_qty; //optional($row->product)->name;
            })->addColumn('shop_name', function ($row) {
                return Shop::find($row->req_receive_shop_id)->first()->name;
            })->addColumn('action', function ($row) {
                $dd =  '<a href="javascript:void(0);" onclick="deleteCartItem('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                return $dd;
            })->rawColumns(['action','product_name'])
            // ->orderColumn('id', 'DESC')
            ->make(true);
    }



    function addItemToExchange(Request $request){
        $validator = Validator::make($request->all(), [
            'product_id'    => 'required',
            'qty'           => 'required',
            'action'        => 'required',
          //  'sale_key'      => 'required',
            'shop_id'       => 'required'
        ]);
 
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()],406);
        }
        $productID  = $request->get('product_id');
        $qty        = (int) $request->get('qty');
        $action     = $request->get('action');
        $saleKey    = $request->get('sale_key');
        $loginShopID     = auth()->user()->shop_id;
        $reqToshopID     = $request->get('shop_id');

        if($qty < 1){
            return response()->json(['status' => false, 'message' => 'Invalid Qty.'],406);
        }
        if(!($loginShopID)){
            return response()->json(['status' => false, 'message' => 'User have no shop assign. Please select shop first'],406);
        }
        $prodcutAvail = ProductAvailable::where(['product_id' => $productID, 'shop_id' => $reqToshopID ])->first();

        if( !($prodcutAvail->product_id) || ($prodcutAvail->qty < 1) ){
            return response()->json(['status' => false, 'message' => 'Product not available'],406);
        }
        
        $product = Product::find($productID);        
        // #todo add status 

        $where['req_receive_shop_id']   = $reqToshopID;
        $where['status']                = 'darft';
        $itemObj = StockExchange::where('product_id', $productID)->where($where)->first();
        $shopID = auth()->user()->shop_id;

        /*
        Please check the is item available in Product_Available
        */

        if($itemObj){
            if($action  == 'add' ){
                $qty = $itemObj->req_qty  + $qty;
            }
            // dd([
            //     'product_id'        => $product->id,
            //     'created_by'        => auth()->user()->id,
            //     'req_qty'           => $qty,
            //     'req_receive_shop_id'   => $reqToshopID,
            //     'req_send_shop_id'      => $loginShopID
            // ]);
            $item = StockExchange::where([
                    'product_id' => $productID, 
                    'req_receive_shop_id'=> $reqToshopID,
                    'status' => 'darft'
                ])->update([
                'product_id'        => $product->id,
                'created_by'        => auth()->user()->id,
                'req_qty'           => $qty,
                'req_receive_shop_id'   => $reqToshopID,
                'req_send_shop_id'      => $loginShopID,
                'status' => 'darft'
            ]);
        }else{
            // dd([
            //     'product_id'        => $product->id,
            //     'created_by'        => auth()->user()->id,
            //     'req_qty'           => $qty,
            //     'req_receive_shop_id'   => $reqToshopID,
            //     'req_send_shop_id'      => $loginShopID
            // ]);
            $item = StockExchange::create([
                'product_id'        => $product->id,
                'created_by'        => auth()->user()->id,
                'req_qty'           => $qty,
                'req_receive_shop_id'   => $reqToshopID,
                'req_send_shop_id'      => $loginShopID,
                'status' => 'darft'
            ]);
        }
        return response()->json(['status' => true, 'data' => $item , 'message' => 'Done']);
    }






    function requestPost(Request $request){

        $validator = Validator::make($request->all(), [
            'product_id'    => 'required',
            'qty'           => 'required',
            'shop_id'       => 'required',
            // 'sale_key'      => 'required'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()],406);
        }


        $serialNumbers = $request->get('sn');
        if( !empty($serialNumbers) &&  count($serialNumbers) > 0){
            $results = DB::table('stock_items')->whereIn('serial_number',$serialNumbers)->get();
            if( count($results) > 0 ){
                return response(['message' => 'Serial Number ( '.$results[0]->serial_number.' ) already exist. ' ],422);
            }
            $data['serial_numbers'] = implode(",",$serialNumbers);
        }


        $loginShopID = 1;
        $requestToShopID = 2; //$request->get('shop_id');

        //stock_items
         $subCatID      = $request->get('sub_cat_id');
         $parentID      = SubCategory::find($subCatID)->parentCategoryId;  // optional(SubCategory::find($subCatID))->parentCategoryId; 
         $productCatID  = $request->get('product_cat_id');
         $productID     = $request->get('product_id');
         $uomID         = $request->get('uom_id');
         $uom = Uom::find($uomID);
        // $uomCode       = $request->get('uom_code');
         $qty           = $request->get('qty');
         $warranty_date = $request->get('warranty_date');
         $unit_price    = $request->get('unit_price');        
         $expiry_date   = $request->get('expiry_date');
 
      //   $data['stock_delivery_id'] = '0';
         $data['parent_cat_id'] = $parentID;
         $data['sub_cat_id'] = $subCatID;
         $data['product_cat_id'] = $productCatID;
         $data['product_id'] = $productID;
         $data['uom_id'] = $uomID;
      //    $data['shop_id'] =  $request->get('shop_id');
         $data['uom_code'] =  $uom->code;

         // 'req_send_user_id','req_aprove_user_id','req_send_shop_id','req_receive_shop_id',

         $data['req_send_user_id']      =  auth()->user()->id;
         // $data['req_aprove_user_id']    =  $uom->code;
         $data['req_send_shop_id']      =  $loginShopID;
         $data['req_receive_shop_id'] =  $requestToShopID;

         $data['quantity'] = $qty;
         $data['unit_price'] = $unit_price;
        // $data['warranty_date'] = $warranty_date;
       //  $data['expiry_date'] = $expiry_date;
         $data['created_by'] = auth()->user()->id;
         $data['status'] = 'darft';
         StockExchange::create($data);
         return response()->json(['status' => true, 'message' => 'Product added successfully!']);
    }




    function getRequestDetail(Request $request){
        //  $results = DB::table('stock_deliveries_products')->get();
        $userID     = auth()->user()->id;
        $exchangeKey = $request->get('exchange_key');
        $loginShopID = auth()->user()->shop_id;
        $results    = StockExchange::with(['product'])->where('exchange_key',$exchangeKey)->get();


         return Datatables::of($results)
            ->addColumn('product_name', function ($row) {
                     return optional($row->product)->name;
            })->addColumn('uom_code', function ($row) {
                 return $row->uom_code;
            })->addColumn('sn_tags', function ($row) {
                return "<span class=\"label label-sm label-info arrowed arrowed-right \" >".str_replace(',','</span> &nbsp; <span class="label label-sm label-info arrowed arrowed-right" >',$row->serial_numbers).'</span>';
            })->addColumn('action', function ($row) {
                     $statusIcon = 'fa fa-ban';
                     // if($row->status == 1){
                     // $statusIcon = 'fa fa-check-circle-o';
                     // }
                     $action = '<div class="hidden-sm hidden-xs btn-group">';
                    
                 //     $action .= '<a href="'.route('product.edit', $row->id).'" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Edit" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';
                 
                     $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                     $action .= '</div>';
                     return $action;
             })->rawColumns(['action','product_category_name','product_name','product_attribute','sn_tags'])
             //  ->orderColumn('id', 'DESC')
             ->make(true);
     }


    function finalSubmit(){
        $loginShopID        = 1;
        $requestToShopID    = 2;
        // $results    = StockExchange::with(['product'])->where('req_send_shop_id',$loginShopID)->where('status','darft')->get();

        StockExchange::where('req_send_shop_id',$loginShopID)->where('status','darft')->update(['status' => 'send']);
        return response()->json(['status' => true, 'message' => 'Submit successfully!']);
    }


    function getRecords(Request $request){
        $loginShopID        = auth()->user()->shop_id;
        $listFor            = $request->get('list_for');
        $whereArr           = [];

        if( $listFor == 'send' ){
            $whereArr['req_send_shop_id']    = $loginShopID;
        }elseif( $listFor == 'receive' ){
            $whereArr['req_receive_shop_id'] = $loginShopID;
        }

        $results    = StockExchange::with(['product'])->where(function($query) use ($whereArr){
            $query->where($whereArr);
        });

        return Datatables::of($results)
            ->addColumn('product_name', function ($row) {
                return optional($row->product)->name;
            })->addColumn('date_time', function ($row) {
                return     $row->created_at->format('h:m d-m-y');
            })->addColumn('status', function ($row) {
                 return $row->status;
            })->addColumn('req_send_shop', function ($row) {
                return Shop::find($row->req_send_shop_id)->name;
            })->addColumn('req_receive_shop', function ($row) {
                return Shop::find($row->req_receive_shop_id)->name;
            })->addColumn('item_count', function ($row) {
                return (isset($row->total_items))?$row->total_items:'';
            })->addColumn('action', function ($row) {
                $statusIcon = 'fa fa-ban';
                // if($row->status == 1){
                    // $statusIcon = 'fa fa-check-circle-o';
                // }
                $action = '<div class="hidden-sm hidden-xs btn-group">';    
                //if($row->exchange_key != ''){
                        //$action .= '<a href="'.route('stock_exchange.req_detail', $row->exchange_key).'" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Edit" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';
                //  }
                //  $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                // 
                if( ($row->status == 'approve') || ($row->status == 'darft') ){
                // $action .= '<a href="javascript:void(0);" data-link="'.route('stock_exchange.mark_receive',$row->exchange_key).'" onclick="markReceive(this);" class="btn btn-xs btn-success" data-toggle="tooltip" title="Mark Receive" ><i class="ace-icon fa fa-download bigger-120"></i></a>';
                }
                $action .= '</div>';
                return $action;
            })->rawColumns(['action','product_category_name','product_name','product_attribute','sn_tags'])
             //  ->orderColumn('id', 'DESC')
            ->make(true);
    }




    function requestDetail($key){
        $data['results'] = StockExchange::with(['product','productAvailableShopWise'])->where('exchange_key',$key)->get();
        $data['exchange_key'] = $key;
        return view('stock_exchange.detail',$data); 
    }

    


    function requestAprove(Request $request){
        
        $validator = Validator::make($request->all(), [
            'product_id'    => 'required',
            'req_id'        => 'required',
            'exchange_key'  => 'required',
            'send_qty'      => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()],406);
        }
        $reqIDs     = $request->get('req_id');
        $sendQty    = $request->get('send_qty');
        $index      = 0;
        $approveUserID = auth()->user()->id;

        foreach($reqIDs as $reqId):
            $receiveQty = (isset($sendQty[$index]))?$sendQty[$index]:0;
            $stockEx    = StockExchange::find($reqId); 
            if($stockEx->status == 'send'){
                $stockEx->update(['receive_qty' => $receiveQty, 'req_approve_user_id' => $approveUserID, 'status' => 'approve' ]);
            }
        endforeach;

        return response()->json(['status' => false, 'message' => 'Approve Successfully.' ],200);


    }



    function markReceive($exchangeKey){

        $results = StockExchange::with(['product','productAvailableShopWise'])->where('exchange_key',$exchangeKey)->get();
        foreach($results as $row):
            /// $receiveQty = (isset($sendQty[$index]))?$sendQty[$index]:0;
            $stockEx    = StockExchange::find($row->id); 
            if($stockEx->status == 'approve'){
                $stockEx->update(['status' => 'receive' ]);


                // Minus or Redecude the stock who approve
                $reqReceiverStock       = ProductAvailable::where(['shop_id' => $row->req_receive_shop_id, 'product_id' => $row->product_id ])->first();
                $reqReceiverStock->qty  = $reqReceiverStock->qty - $row->receive_qty;
                $reqReceiverStock->save();

                //Add the stock who receive the stock
                // stockEx
                $reqSenderStock       = ProductAvailable::where(['shop_id' => $row->req_send_shop_id, 'product_id' => $row->product_id ])->first();
                $reqSenderStock->qty  = $reqSenderStock->qty + $row->receive_qty;
                $reqSenderStock->save();
            }
        endforeach;

        return response()->json(['status' => false, 'message' => 'Mark Receive Successfully.' ],200);
        

    }



    


    

    

}
