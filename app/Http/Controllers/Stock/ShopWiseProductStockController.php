<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ParentCategory;
use App\Models\Project;
use App\Models\StockDelivery;
use App\Models\StockDeliveryProduct;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Uom;
use App\Models\Shop;
use App\Util\Util;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\StockItem;
use App\Http\Requests\StockDeliveryFormRequest;
use App\Models\ProductAvailable;
use App\Models\ProductCategory;
use App\Models\StockAdjustment;
use Illuminate\Support\Str;

use App\Traits\Definitions;



class ShopWiseProductStockController extends Controller{


    function list(Request $request){
        $productID = $request->get('product_id');

        if($request->ajax()){
            $results    = StockDeliveryProduct::with(['product'])->where('product_id',$productID)->get();

            
            return Datatables::of($results)
             ->addColumn('product_name', function ($row) {
                     return optional($row->product)->name;
             })->addColumn('product_attribute', function ($row) {
                return str_replace(' / ','</br>',strip_tags($row->product->attributeHTML()));
            })->addColumn('uom_code', function ($row) {
                 return $row->uom_code;
             })->addColumn('product_description', function ($row) {
                return optional($row->product)->name;
             })->addColumn('product_category_name', function ($row) {
                     $cat =  optional($row->product)->categories_tree();
                     return  $cat['category']['name'].' / '.$cat['sub_category']['name'].' / '.$cat['product_category']['name']; 
             })->addColumn('attributes_tags', function ($row) {
                 return '';
             })
            //  ->addColumn('total_price', function ($row) {
            //     $qty    = (Int) $row->quantity;
            //     $price  = (Int) $row->unit_price;
            //     return $qty * $price;
            // })
            ->addColumn('sn_tags', function ($row) {
                return "<span class=\"label label-sm label-info arrowed arrowed-right \" >".str_replace(',','</span> &nbsp; <span class="label label-sm label-info arrowed arrowed-right" >',$row->serial_numbers).'</span>';
            })
             ->addColumn('action', function ($row) {
                     $statusIcon = 'fa fa-ban';
                     $action = '<div class="hidden-sm hidden-xs btn-group">';  
                     $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                     $action .= '</div>';
                     return $action;
             })->rawColumns(['action','product_category_name','product_name','product_attribute','sn_tags'])
             //  ->orderColumn('id', 'DESC')
             ->make(true);
        }
       // return view('Stock.Delivery.lists');
    }


    function shopWiseStock(Request $request){
        $productID  = $request->get('product_id');
        $results    = ProductAvailable::with(['product','shop'])->where('product_id',$productID)->get();


        return Datatables::of($results)
        ->addColumn('product_name', function ($row) {
                return optional($row->product)->name;
        })->addColumn('shop_name', function ($row) {
            return optional($row->shop)->name;
        })->addColumn('uom_code', function ($row) {
            return $row->uom_code;
        })->addColumn('product_description', function ($row) {
           return optional($row->product)->name;
        })->addColumn('product_category_name', function ($row) {
                $cat =  optional($row->product)->categories_tree();
                return  $cat['category']['name'].' / '.$cat['sub_category']['name'].' / '.$cat['product_category']['name']; 
        })->addColumn('attributes_tags', function ($row) {
            return '';
        })->addColumn('sn_tags', function ($row) {
           return "<span class=\"label label-sm label-info arrowed arrowed-right \" >".str_replace(',','</span> &nbsp; <span class="label label-sm label-info arrowed arrowed-right" >',$row->serial_numbers).'</span>';
        })->addColumn('action', function ($row) {
                $statusIcon = 'fa fa-ban';
                $action = '<div class="hidden-sm hidden-xs btn-group">';  
                $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                $action .= '</div>';
                return $action;
        })->rawColumns(['action','product_category_name','product_name','product_attribute','sn_tags'])
        //  ->orderColumn('id', 'DESC')
        ->make(true);


    }




    
}
