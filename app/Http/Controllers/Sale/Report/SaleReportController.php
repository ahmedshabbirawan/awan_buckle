<?php

namespace App\Http\Controllers\Sale\Report;

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
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Validator;

class SaleReportController extends Controller{

        function all(Request $request){


            $data['products'] = Product::all();
            $data['shops'] = Shop::all()->pluck('name','id');
            $data['shops'] = Shop::all()->pluck('name','id');



            if($request->ajax()){
                $groupBy    = $request->get('group_by');
                $shopIds = $request->get('shop_ids');
                $dateRange = $request->get('date_range');

                $dateString = explode('-',$dateRange);

              //  dd($dateString);

                $startDate = Carbon::createFromFormat('d/m/Y H:i:s',trim($dateString[0]).' 00:00:01' );
                $endDate = Carbon::createFromFormat('d/m/Y H:i:s',trim($dateString[1]).' 23:59:59' );
        // dd($startDate, $endDate);

                $items      = SaleOrder::with('shop')->where(function($query) use ($shopIds, $startDate, $endDate) {
                    $query->WhereIn('shop_id',$shopIds)->whereDate('created_at','>=',$startDate)->whereDate('created_at','<=',$endDate);
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
                // ->with('totalPrice', function() use ($totalBill) {
                //     return round($totalBill);
                // })->with('billAmount', function() use ($billAmount) {
                //     return round($billAmount);
                // })->with('subTotal', function() use ($subTotal) {
                //     return round($subTotal);
                // })->with('totalDiscount', function() use ($totalDiscount) {
                //     return round($totalDiscount);
                // })
                ->make(true);
            }else{
                return view('sale.report.sale_report_all',$data);
            }     
        }

      


    }
