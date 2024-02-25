<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;
use App\Models\Customer;
use App\Models\User;
use App\Models\SaleReturn;
use App\Models\Shop;
use App\Models\SaleReturnItem;

use Illuminate\Support\Facades\Validator;

class SaleReturnController extends Controller{

        function index(Request $request){
            if($request->ajax()){
                $items      = SaleReturn::where(function($query) use ($request) {});
                
                $items = $items->get();
                return Datatables::of($items)
                ->addColumn('title', function ($row) {
                    return '<a href="'.route('sale.order.detail',$row->id).'" target="_blank" >'.$row->sale_key.'</a>';
                })->addColumn('order_time', function ($row) {
                    return $row->created_at->format('h:i / d-m-Y');
                })->addColumn('description', function ($row) {
                    return $row->description;
                })->addColumn('customer_name', function ($row) {
                    if($row->customer_id == 0){
                        return 'Walking Customer';
                    }else{
                        return Customer::find($row->customer_id)->name;
                    }
                })
                ->addColumn('action', function ($row) {
                    $action = '<div class="hidden-sm hidden-xs btn-group">';
                    $action .= '<a href="'.route('sale.sale_return.detail',$row->id).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
                    $action .= '</div>';
                    return $action;
                })->rawColumns(['title','product_attribute','status_label','action'])
                ->make(true);
            }else{
                return view('order.sale_return_lists');
            }     
        }

        function detail($orderID,Request $request){

            $viewType   = $request->get('view');
            $saleReturn      = SaleReturn::findOrFail($orderID);
            $orderItem  = SaleReturnItem::with('product')->where('sale_return_id',$saleReturn->id)->get();
            $customer   = Customer::find($saleReturn->customer_id);
            $shop       = Shop::find($saleReturn->shop_id);
            $manager    = User::find($saleReturn->created_by);
            $view       = 'sale.sale_return_detail';

            $data = [
                'viewType'          => $viewType,
                'order'        => $saleReturn,
                'orderItem'    => $orderItem,
                'shop'              => $shop,
                'customer'          => $customer,
                'manager'           => $manager
            ];
            // if($viewType == 'print'){
            //     $data['hideInfo'] = false; 
            //     $view = 'sale.sale_return_detail';
            // }
            return view($view,$data);
        }



    }
