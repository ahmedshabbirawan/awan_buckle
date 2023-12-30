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

class StockAdjustmentController extends Controller{

    public function index(Request $request){


        if($request->ajax()){
        $userID             = auth()->user()->id;
        $deliveryID         = $request->get('stock_delivery_id');
        $loginShopID        = 1;
        $requestToShopID    = 2; //$request->get('shop_id');
        // if($deliveryID != ''){
        //     $results    = StockExchange::with(['product'])->where('stock_delivery_id',$deliveryID)->get();
        // }else{
        //     $results    = StockExchange::with(['product'])->where('created_by',$userID)->where('status',0)->get();
        // }
        $results    = StockAdjustment::with(['product'])->where('shop_id',$loginShopID)->where('status','darft')->get();





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

        // dd($data['products']);
        return view('stock_adjustment.form',$data);
    }


    function save(Request $request){
        // echo auth()->user()->shop_id; 
        // exit;
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
        $loginShopID    = auth()->user()->shop_id; 

        $index = 0;


        try{
            DB::beginTransaction();

        foreach($productIDzArr as $id){
            $productID  = $productIDzArr[$index];
            $stock      = $stockArr[$index];
            $qty        = (int) $qtyArr[$index];
            if( $qty < 1 ){
                continue;
            }
            StockAdjustment::create([
                'product_id' => $productID,
                'stock_type' => $stockArr[$index],
                'qty_when_adjust'   => $currentQtyArr[$index],
                'qty'               => $qtyArr[$index],
                'description'       => $descArr[$index],
                'shop_id'       => $loginShopID
            ]);
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
        }
        return response()->json(['status' => true, 'message' => 'Successful']);
    }catch(\Exception $e){
        DB::rollBack();
        return response()->json(['message' => 'Error.Please Contact with Support'], 500);
    }


    }
    
    
}
