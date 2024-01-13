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
use App\Models\Shop;
use App\Models\StockItem;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Validator;

class SaleOrderController extends Controller{

        function index(Request $request){
            if($request->ajax()){
                $groupBy    = $request->get('group_by');
                $items      = SaleOrder::where(function($query) use ($request) {
                    //     $empID      = $request->get('emp_id');
                    //     if( $empID != '' ){
                    //         $query->where('emp_id',$empID);
                    //     }
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
                })->addColumn('customer_name', function ($row) {
                    if($row->customer_id == 0){
                        return 'Walking Customer';
                    }else{
                        return Customer::find($row->customer_id)->name;
                    }
                })
                ->addColumn('action', function ($row) {
                    $action = '<div class="hidden-sm hidden-xs btn-group">';
                    $action .= '<a href="'.route('sale.order.detail',$row->id).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
                    $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->sale_key.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                    $action .= '</div>';
                    return $action;
                })->rawColumns(['title','product_attribute','status_label','action'])
                ->make(true);
            }else{
                return view('order.lists');
            }     
        }

        function detail($orderID,Request $request){

            $viewType   = $request->get('view');
            $order      = SaleOrder::findOrFail($orderID);
            $orderItem  = SaleOrderItem::with('product')->where('order_id',$order->id)->get();
            $customer   = Customer::find($order->customer_id);
            $shop       = Shop::find($order->shop_id);
            $manager    = User::find($order->created_by);
            $view       = 'order.detail';
            if($viewType == 'print'){
                $view = 'order.order_detail_table';
            }
            return view($view, [
                'order' => $order,
                'orderItem' => $orderItem,
                'shop' => $shop,
                'customer' => $customer,
                'manager' => $manager
            ]);
        }


        function OrderBackSearchModal(){
        return response()->json([
            'view' => View('order.order_back_search_modal',[])->render(),
            'data' =>   [],
            'status' => true,
            'message' => 'OK'
        ]);
        }
    }
