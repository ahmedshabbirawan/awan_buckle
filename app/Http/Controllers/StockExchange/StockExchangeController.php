<?php

namespace App\Http\Controllers\StockExchange;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Models\ProductAvailable;
use App\Models\Shop;
use App\Models\StockAudit;
use App\Models\Product;
use App\Models\StockExchange;
use Illuminate\Support\Facades\Validator;
use Exception;



class StockExchangeController extends Controller
{

    function getRecords(Request $request)
    {
        $loginShopID        = auth()->user()->shop_id;
        $listFor            = $request->get('list_for');
        $whereArr           = [];

        if ($listFor == 'send') {
            $whereArr['req_send_shop_id']    = $loginShopID;
        } elseif ($listFor == 'receive') {
            $whereArr['req_receive_shop_id'] = $loginShopID;
        }

        $results    = StockExchange::with(['product'])->where(function ($query) use ($whereArr) {
            $query->where($whereArr);
        });

        return Datatables::of($results)
            ->addColumn('product_name', function ($row) {
                return optional($row->product)->name;
            })->addColumn('date_time', function ($row) {
                return     $row->created_at->format('h:i d-m-y');
            })->addColumn('status', function ($row) {
                return $row->status;
            })->addColumn('req_send_shop', function ($row) {
                return Shop::find($row->req_send_shop_id)->name;
            })->addColumn('req_receive_shop', function ($row) {
                return Shop::find($row->req_receive_shop_id)->name;
            })->addColumn('item_count', function ($row) {
                return (isset($row->total_items)) ? $row->total_items : '';
            })->addColumn('action', function ($row) use ($listFor) {

                $action = '<div class="btn-group">';

                if ($listFor == 'send') {
                    if ($row->status == 'darft') {
                        $action .= '<a href="javascript:void(0);" onclick="deleteCartItem(' . $row->id . ');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                    }
                }

                if ($listFor == 'receive') {
                    if ($row->status == 'post') {
                        $action .= '<a href="javascript:void(0);" onclick="markApprove(' . $row->id . ');" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Click For Approve and Send" ><i class="ace-icon fa fa-check bigger-120"></i></a>';
                    }
                }

                $action .= '</div>';
                return $action;
            })->rawColumns(['action', 'product_category_name', 'product_name', 'product_attribute', 'sn_tags'])
            //  ->orderColumn('id', 'DESC')
            ->make(true);
    }

    function requestForm($exchangeKey = '')
    {
        if ($exchangeKey == '') {
            $exchangeKey = time();
        }
        $data['shops']  = Shop::all()->pluck('name', 'id');
        $data['exchangeKey'] = $exchangeKey;
        return view('stock_exchange.create_request', $data);
    }

    function indexList()
    {
        return view('stock_exchange.lists');
    }

    function deleteCartItem(Request $request)
    {
        $itemID = $request->get('item_id');
        StockExchange::find($itemID)->delete();
        return response()->json(['status' => true, 'message' => 'Deleted Successfully']);
    }



    function placeRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_ids'   => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 406);
        }

        $itemsIDs           = $request->item_ids;
        $exchangeItems      = StockExchange::whereIn('id', $itemsIDs)->get();
        $itemFound          = count($exchangeItems);

        if ($itemFound == 0) {
            return response(['message' => 'No items available'], 422);
        }

        try {
            DB::beginTransaction();
            foreach ($exchangeItems as $item) :
                $reqQty         = $item->req_qty;
                $reqToShopID    = $item->req_receive_shop_id;
                $reqFromShopID  = $item->req_send_shop_id;
                $productID      = $item->product_id;
                $productAvail = ProductAvailable::where(['shop_id' => $reqToShopID, 'product_id' => $productID])->first();
                if ($productAvail && ($productAvail->qty > 0)) {
                    $item->status = 'post';
                    $item->save();
                } else {
                    throw new Exception("Product Qty not available");
                }
            endforeach;
            DB::commit();

            return response()->json(['status' => true, 'message' => 'Successfully Posted.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Error.Please Contact   Support' . $e->getMessage()], 500);
        }
    }


    function markApproveReq(Request $request)
    {
        $itemID         = $request->get('item_id');
        $item           = StockExchange::find($itemID);

        $reqQty         = $item->req_qty;
        $reqToShopID    = $item->req_receive_shop_id;
        $reqFromShopID  = $item->req_send_shop_id;
        $productID      = $item->product_id;

        $productAvail = ProductAvailable::where(['shop_id' => $reqToShopID, 'product_id' => $productID])->first();

        if ($productAvail && ($productAvail->qty > 0)) {

            // $productAvail->qty = $productAvail->qty - $reqQty;
            // $productAvail->save();

            $item->status = 'approve';
            $item->save();

            ProductAvailable::manageStockByShopAndProductId($reqQty, StockAudit::STOCK_ACTION_MINUS, $productID, $reqToShopID, StockAudit::STOCK_OBJECT_TYPE_EXCHANGE, $itemID );


            /* UPDATE REQUESTED SHOP STOCK */
            ProductAvailable::manageStockByShopAndProductId($reqQty, StockAudit::STOCK_ACTION_ADD, $productID, $reqFromShopID, StockAudit::STOCK_OBJECT_TYPE_EXCHANGE, $itemID );

            /*
            $reqProductAvailable = ProductAvailable::where([
                'product_id' => $productID,
                'shop_id' => $reqFromShopID
            ])->first();

            if ($reqProductAvailable) {
                $reqProductAvailable->qty = $reqProductAvailable->qty + $reqQty;
                $reqProductAvailable->save();
            } else {
                ProductAvailable::create([
                    'product_id' => $productID,
                    'shop_id' => $reqFromShopID,
                    'qty' => $reqQty
                ]);
            }
            */
        } else {
            throw new Exception("Product Qty not available");
        }
    }

    function getRequestCart(Request $request)
    {

        $userID      = auth()->user()->id;
        $deliveryID  = $request->get('stock_delivery_id');
        $exchangeKey = $request->get('exchange_key');
        $groupBy     = $request->get('group_by_for');
        $loginShopID = auth()->user()->shop_id;

        $listFor    = $request->get('list_for');
        $whereArr   = [];


        $whereArr['req_send_shop_id']    = $loginShopID;
        $whereArr['status']    = 'darft';
        $results    = StockExchange::with(['product'])->where(function ($query) use ($whereArr) {
            $query->where($whereArr);
        }); 

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
                $dd =  '<a href="javascript:void(0);" onclick="deleteCartItem(' . $row->id . ');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                return $dd;
            })->rawColumns(['action', 'product_name'])
            // ->orderColumn('id', 'DESC')
            ->make(true);
    }

    function addItemToExchange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id'    => 'required',
            'qty'           => 'required',
            'action'        => 'required',
            //  'sale_key'      => 'required',
            'shop_id'       => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 406);
        }
        $productID  = $request->get('product_id');
        $qty        = (int) $request->get('qty');
        $action     = $request->get('action');
        $saleKey    = $request->get('sale_key');
        $loginShopID     = auth()->user()->shop_id;
        $reqToshopID     = $request->get('shop_id');

        if ($qty < 1) {
            return response()->json(['status' => false, 'message' => 'Invalid Qty.'], 406);
        }
        if (!($loginShopID)) {
            return response()->json(['status' => false, 'message' => 'User have no shop assign. Please select shop first'], 406);
        }
        $prodcutAvail = ProductAvailable::where(['product_id' => $productID, 'shop_id' => $reqToshopID])->first();

        if (!($prodcutAvail->product_id) || ($prodcutAvail->qty < 1)) {
            return response()->json(['status' => false, 'message' => 'Product not available'], 406);
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

        if ($itemObj) {
            if ($action  == 'add') {
                $qty = $itemObj->req_qty  + $qty;
            }
            $item = StockExchange::where([
                'product_id' => $productID,
                'req_receive_shop_id' => $reqToshopID,
                'status' => 'darft'
            ])->update([
                'product_id'        => $product->id,
                'created_by'        => auth()->user()->id,
                'req_qty'           => $qty,
                'req_receive_shop_id'   => $reqToshopID,
                'req_send_shop_id'      => $loginShopID,
                'status' => 'darft'
            ]);
        } else {
            $item = StockExchange::create([
                'product_id'        => $product->id,
                'created_by'        => auth()->user()->id,
                'req_qty'           => $qty,
                'req_receive_shop_id'   => $reqToshopID,
                'req_send_shop_id'      => $loginShopID,
                'status' => 'darft'
            ]);
        }
        return response()->json(['status' => true, 'data' => $item, 'message' => 'Done']);
    }

    function requestDetail($key)
    {
        $data['results'] = StockExchange::with(['product', 'productAvailableShopWise'])->where('exchange_key', $key)->get();
        $data['exchange_key'] = $key;
        return view('stock_exchange.detail', $data);
    }
}
