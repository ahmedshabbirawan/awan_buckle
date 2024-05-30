<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ParentCategory;
use App\Models\ProductAvailable;
use App\Models\SaleOrderItem;
use App\Models\StockDeliveryItem;
use App\Models\StockDeliveryProduct;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;


class ProductStockController extends Controller{
    


    function productStock(Request $request){

        $shopID = $request->get('shop_id');


        if($request->ajax()){
            // withSum('comments', 'votes'); // ->with(['productQty'])
            $results    = Product::withSum('productAvailable','qty')->withSum('StockDeliveryProduct','quantity')->where(function($query) use ($shopID) {

                
            })->get();
                return Datatables::of($results)->addColumn('product_name', function ($row) {
                    return optional($row->product)->name;
                })->addColumn('product_attribute', function ($row) {
                    return ''; //str_replace(' / ','</br>',strip_tags($row->product->attributeHTML()));
                })->addColumn('uom_code', function ($row) {
                    return $row->uom_code;
                })->addColumn('description', function ($row) {
                    return $row->description; // optional($row->product)->name;
                })->addColumn('created', function ($row) {
                    return $row->created_at->format('h:i d-m-Y');
                })->rawColumns(['action','product_category_name','product_name','product_attribute','sn_tags'])
                    //  ->orderColumn('id', 'DESC')
                ->make(true);
        } 
        $data['sub_category']   = ParentCategory::with(['subCategories'])->orderBy('name','DESC')->get();
        return view('reports.product_stock_report',$data);
    }


    function product(Request $request){
        $data['sub_category']   = ParentCategory::with(['subCategories'])->orderBy('name','DESC')->get();
        $data['products'] = Product::all();// ->pluck('name','id');
        if($request->ajax()){
            $productId      = $request->get('product_id');
            $productDetail  = Product::findoRFail($productId); // Product::where('id',$productId)->first();
            $dateRange  = ($request->has('date_range')) ? $request->get('date_range'): optional($productDetail)->created_at->format('d/m/Y').'-'.date('d/m/Y');
            $dateString = explode('-',$dateRange);
            $startDate  = Carbon::createFromFormat('d/m/Y H:i:s',trim($dateString[0]).' 00:00:01' );
            $endDate    = Carbon::createFromFormat('d/m/Y H:i:s',trim($dateString[1]).' 23:59:59' );


            $dates = function($query)use($startDate,$endDate){
                if($startDate && $endDate){
                    $query->whereDate('created_at','>=',$startDate)->whereDate('created_at','<=',$endDate);
                }
            };


            $response['product_detail'] = $productDetail;

            $productStat['sale_count']      = SaleOrderItem::where('product_id',$productId)->where($dates)->sum('qty');
            $productStat['purchase_count']  = StockDeliveryProduct::where('product_id',$productId)->where($dates)->sum('quantity');
            $productStat['available_count'] = ProductAvailable::where('product_id',$productId)->sum('qty');
            $shops          = Shop::all();


            $shops->map(function($shop, $val)use($productId, $dates){
                $shop->available_qty = ProductAvailable::where('product_id',$productId)->where('shop_id', $shop->id)->sum('qty');
                // $shop->available_qty = ProductAvailable::where('product_id',$productId)->where('shop_id', $shop->id)->sum('qty');
                $shop->sale_count      = SaleOrderItem::where('product_id',$productId)->where($dates)->sum('qty');
                $shop->purchase_count  = StockDeliveryProduct::where('product_id',$productId)->where($dates)->sum('quantity');
            });
            $response['product_stats_shops_wise'] = $shops;
            $response['product_stats'] = $productStat;
            return response()->json($response);
        }else{
           //  $data['shops'] = Shop::all()->pluck('name','id');
            return view('reports.product_report',$data);
        }
    }





    function productWise(){
        $data['sub_category']   = ParentCategory::with(['subCategories'])->orderBy('name','DESC')->get();
        return view('reports.product_wise_stock_report',$data);
    }





}
