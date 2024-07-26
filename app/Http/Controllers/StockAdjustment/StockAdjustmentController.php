<?php

namespace App\Http\Controllers\StockAdjustment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Models\StockAdjustment;
use App\Models\Product;
use App\Models\ProductAvailable;
use Illuminate\Support\Facades\Validator;
use App\Models\StockAudit;

class StockAdjustmentController extends Controller{

    public function index(Request $request){


if($request->ajax()){
    $loginShopID        = auth()->user()->shop_id;
    $results            = StockAdjustment::with(['product'])->where('shop_id',$loginShopID)->get();
        return Datatables::of($results)->addColumn('product_name', function ($row) {
            return optional($row->product)->name;
        })->addColumn('product_attribute', function ($row) {
            return str_replace(' / ','</br>',strip_tags($row->product->attributeHTML()));
        })->addColumn('uom_code', function ($row) {
            return $row->uom_code;
        })->addColumn('description', function ($row) {
            return $row->description; // optional($row->product)->name;
        })->addColumn('created', function ($row) {
            return $row->created_at->format('h:i d-m-Y');
        })->rawColumns(['action','product_category_name','product_name','product_attribute','sn_tags'])
            //  ->orderColumn('id', 'DESC')
        ->make(true);
        }else{
            return view('stock_adjustment.lists');
        }
    }


    function form(){
        $data['products'] = Product::with('productQtyShopWise')->get();
        return view('stock_adjustment.form',$data);
    }


    function save(Request $request){
        $validator = Validator::make($request->all(), [
            'product_id'    => 'required',
            'stock'         => 'required',
            'adjust_qty'    => 'required',
            'description'   => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()],406);
        }

        $productIDzArr  = $request->get('product_id');
        $stockArr       = $request->get('stock');
        $qtyArr         = $request->get('adjust_qty');
        $descArr        = $request->get('description');
        $currentQtyArr  = $request->get('current_qty');
        
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
            foreach($productIDzArr as $id){
                $productID  = $productIDzArr[$index];
                $stock      = $stockArr[$index];
                $qty        = (int) $qtyArr[$index];
                $currentQty = $currentQtyArr[$index];
                $desc = $descArr[$index];
                $stockType = $stockArr[$index];

                $index++;

                if( $qty < 1 ){
                    continue;
                }
                
                $sa = StockAdjustment::create([
                    'product_id' => $productID,
                    'stock_type' => $stockType,
                    'qty_when_adjust'   => $currentQty,
                    'qty'               => $qty,
                    'description'       => $desc,
                    'shop_id'       => $loginShopID
                ]);

                $action = '';

                if($stock == 'in'){
                    $action = StockAudit::STOCK_ACTION_ADD;
                }elseif($stock == 'out'){
                    $action = StockAudit::STOCK_ACTION_MINUS;
                }


                ProductAvailable::manageStockByShopAndProductId($qty, $action,$productID, $loginShopID,StockAudit::STOCK_OBJECT_TYPE_ADJUSTMENT, $sa->id);

                /*
                $product = ProductAvailable::where([
                    'shop_id'       => $loginShopID,
                    'product_id'    => $productID
                ])->first();

                if($product){
                    if($stock == 'in'){
                        $product->qty = $product->qty + $qty;
                    }elseif($stock == 'out'){
                        $product->qty = $product->qty - $qty;
                    }
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
        return response()->json(['message' => 'Error.Please Contact with Support'], 500);
    }


    }
    
    
}
