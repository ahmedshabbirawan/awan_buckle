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
use App\Models\StockExchange;
use App\Models\SubCategory;
use App\Models\Uom;
use Illuminate\Support\Facades\Validator;



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

        $userID      = auth()->user()->id;
        $deliveryID  = $request->get('stock_delivery_id');
        $exchangeKey = $request->get('exchange_key');

        $groupBy = $request->get('group_by_for');


        $loginShopID        = 1;
        $requestToShopID    = 2; //$request->get('shop_id');

        $listFor    = $request->get('list_for');
        $whereArr   = [];
       //  $groupBy    = [];

        if( $listFor == 'send' ){
            $whereArr['req_send_shop_id']    = $loginShopID;
        }elseif( $listFor == 'receive' ){
            $whereArr['req_receive_shop_id'] = $loginShopID;
        }elseif( $listFor == 'detail' ){
             $whereArr['exchange_key']        = $exchangeKey;
        }else{

        }



        



        $results    = StockExchange::with(['product'])->where(function($query) use ($whereArr){
            $query->where($whereArr);
        }); // ->get();


        // echo $groupBy; 
        // exit;


        if($groupBy == 'exchange_key'){
            $results = $results->select('*',DB::raw('count(*) as total_items'))->groupBy('exchange_key');
        }


        
        

        
         return Datatables::of($results)
            ->addColumn('exchange_key', function ($row) {
                return ($row->exchange_key != '')?$row->exchange_key:'N/A';
            })->addColumn('product_name', function ($row) {
                return optional($row->product)->name;
            })->addColumn('product_attribute', function ($row) {
                return ''; //str_replace(' / ','</br>',strip_tags($row->product->attributeHTML()));
            })->addColumn('uom_code', function ($row) {
                 return ''; // $row->uom_code;
            })->addColumn('product_description', function ($row) {
                return optional($row->product)->name;
            })->addColumn('item_count', function ($row) {
                return (isset($row->total_items))?$row->total_items:'';
            })->addColumn('action', function ($row) {

                // return $row->exchange_key;

                     $statusIcon = 'fa fa-ban';
                     // if($row->status == 1){
                     // $statusIcon = 'fa fa-check-circle-o';
                     // }
                     $action = '<div class="hidden-sm hidden-xs btn-group">';    
                     //if($row->exchange_key != ''){
                        $action .= '<a href="'.route('stock_exchange.req_detail', $row->exchange_key).'" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Edit" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';
                    //  }
                     
                    //  $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';


                    // 
                    if( ($row->status == 'approve') || ($row->status == 'darft') ){
                        $action .= '<a href="javascript:void(0);" data-link="'.route('stock_exchange.mark_receive',$row->exchange_key).'" onclick="markReceive(this);" class="btn btn-xs btn-success" data-toggle="tooltip" title="Mark Receive" ><i class="ace-icon fa fa-download bigger-120"></i></a>';
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



    function requestDeny(){
        // return view('stock_exchange.create'); 
        // 021111117359

        // 6


    }


    




    
/*
    function searchStockItem(Request $request){
        $item = StockItem::with(['product','project'])->where('serial_number',$request->get('sn'))->first();
        if( isset($item->id) ){
            $item->attribute    = $item->product->attributeHTML();
            $item->category     = $item->product->categoryHTML();
            return $item;   
        }else{
            return response(['message' => 'No record found!'],422);
        }
    }


    function available_item_by_product_ajax(Request $request){
        $productID      = $request->get('product_id');
        $data['items']  = StockItem::with(['itemProduct'])->where('product_id',$productID)->where(function($query) use ($request){
        $projectID      = $request->get('project_id');
            if($projectID != ''){
                $query->where('project_id',$projectID);
            }
        })->where('available_qty','>',0)->get(); 
        return view('Stock.Delivery.ajax_view.item_select',$data);
    }


    function all_item_by_product_datatable(Request $request){
        
        $results = StockItem::with(['project','itemProduct', 'stockDelivery', 'stockDelivery.Supplier:id,name'])->with('holderInfo.employee')->select()->where(function($query) use ($request){

            $productID      = $request->get('product_id');    
            if($productID != ''){
                $query->where('product_id',$productID);
            }
        })->orderBy('id','ASC');

        return Datatables::of($results)
        ->addColumn('project_name', function ($row) {
            return optional($row->project)->name;
        })
        ->addColumn('employee_name', function ($row) {
            $employee =  optional($row->holderInfo)->employee;
            return optional($employee)->full_name;
        })
        ->addColumn('employee_name', function ($row) {
            $employee =  optional($row->holderInfo)->employee;
            return optional($employee)->full_name;
        })
        ->addColumn('supplier_name', function ($row) {
            $supplier =  optional($row->stockDelivery)->supplier;
            return optional($supplier)->name;
        })
        ->addColumn('expire_date', function ($row) {
            return  optional($row->itemProduct)->warranty_date;
        })
        ->addColumn('delivery_info', function ($row) {
            $delivery   =  $row->stockDelivery;
            $html       = '';
            if(isset($delivery)){
                $html .= 'Purchase Date : <b>'.$delivery->purchased_date.'</b><br>';
                $html .= 'Challan # : <b>'.$delivery->delivery_challan_no.'</b><br>';
            }
            return $html;
        })
        ->addColumn('action', function ($row) {
                $statusIcon = 'fa fa-ban';
                if($row->status == 1){
                    $statusIcon = 'fa fa-check-circle-o';
                }
                $action = '<div class="hidden-sm hidden-xs btn-group">';
                 $action .= '<a href="'.route('stocks.item.history', $row->id).'" target="_blank" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
                // $action .= '<a href="'.route('supplier.edit', $row->id).'" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Edit" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';
                // $action .= '<a href="javascript:void(0);" onclick="changeStatus('.$row->id.');" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Change Status" ><i class="ace-icon '.$statusIcon.' bigger-120"></i></a>';
                // $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                $action .= '</div>';
                return $action;
        })->rawColumns(['condition_status_label','action','delivery_info'])
        //  ->orderColumn('id', 'DESC')
        ->make(true);
    }




    function itemIssuanceHistory(Request $request){
        // issuance_item

        if($request->ajax()){
            $groupBy    = $request->get('group_by');
            $items      = IssuanceItem::with(['product','project','subCategory','returnDetail'])
          //   ->whereRelation('subCategory', 'parentCategoryId',2)
            ->where(function($query) use ($request) {
                $empID              = $request->get('emp_id');
                $return_detail_id   = $request->get('return_detail_id');
                //  $itemStatus = $request->get('item_status');
                $itemID             = $request->get('item_id');
                $returnable         = $request->get('returnable');

                if( $empID != '' ){
                    $query->where('emp_id',$empID);
                }
                if( $returnable == '1' ){
                    $query->whereNull('return_date');
                }
                if($return_detail_id != ''){
                    $query->where('return_detail_id',$return_detail_id);
                }

                if( $itemID != '' ){
                    $query->where('item_id',$itemID);
                }
            })->orderBy('id','DESC'); 
            
            
            
            $items = $items->get();
            return Datatables::of($items)
            ->addColumn('product_detail', function ($row) {
                $item =  "Product : ".$row->product->name.'<br>';
                $item .= "Serial  : ".$row->serial_number.'<br>';
                $item .= "Project : ".optional($row->project)->name;
                return $item;
            })
            ->addColumn('product_attribute', function ($row) {
                return str_replace(' / ','</br>',strip_tags($row->product->attributeHTML()));
            })
            ->addColumn('item_condition', function ($row) {
                return $row->return_condition_status;
            })
            ->addColumn('employee_detail', function ($row) {
                return '<a  href="'.route('Settings.employee.view',$row->emp_id).'">'.$row->employee->full_name.'</a>';
            })
            ->addColumn('project_detail', function ($row) {
                return optional($row->project)->name;
            })
            ->addColumn('status_label', function ($row) {
                if($row->status == 1){
                    return '<span class="label label-sm label-success">'.$row->status_label.'</spna>';
                }else{
                    return '<span class="label label-sm label-danger">'.$row->status_label.'</spna>';
                }
            })
            ->addColumn('action', function ($row) {
                $statusIcon = 'fa fa-ban';
                if($row->status == 1){
                    $statusIcon = 'fa fa-check-circle-o';
                }
    
                $action = '<div class="hidden-sm hidden-xs btn-group">';
                $action .= '<a href="'.route('issuance.detail', ($row->issue_key)?$row->issue_key:'NA' ).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
                $action .= '</div>';
                return $action;
            })->rawColumns(['product_detail','product_attribute','return_receipt_file','status_label','action','item_condition','employee_detail'])
            ->make(true);
            }else{
                // return view('Issuance.lists');
                
            }

    }


    function itemHistory($itemID, Request $request){
        $item = StockItem::find($itemID);
        
        $history      = IssuanceItem::with(['product','employee','project','subCategory','returnDetail','issuedBy'])
          ->where(function($query) use ($request, $itemID) {

              // $itemID             = $request->get('item_id');
              if( $itemID != '' ){
                  $query->where('item_id',$itemID);
              }

          })->orderBy('id','DESC')->get(); 


         //  print_r($history); exit;
        

        $data['itemID']     = $itemID;
        $data['item']       = $item;
        $data['row']        = $item->product;
        $data['history']    = $history;

        $data['categories']   = $item->product->categories_tree();
       //  dd($item->product());

        return view('Product.item_history',$data);
    }


    */

    

}
