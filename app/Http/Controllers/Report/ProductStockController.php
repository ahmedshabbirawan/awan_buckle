<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ParentCategory;
use Illuminate\Http\Request;

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



}
