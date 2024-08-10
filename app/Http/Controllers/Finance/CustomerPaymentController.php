<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\CustomerPayment;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Product;
use App\Models\ProductAvailable;
use App\Models\SaleCart;
use App\Models\Customer;

use App\Models\SaleOrder;
use App\Models\SaleOrderItem;
use App\Models\Shop;
use App\Models\StockAudit;
use Illuminate\Support\Facades\DB;
use Exception;

class CustomerPaymentController extends Controller{

    

    

    function index(Request $request){
        $customerId = $request->get('customer_id');

        if($request->ajax()){
            $items = CustomerPayment::where(function($query) use ($customerId) {
                if($customerId != ''){
                    $query->where('customer_id', $customerId);
                }
            })->orderBy('id','ASC');
            return Datatables::of($items)
            ->addColumn('action', function ($row) {
                $dd =  '<a href="javascript:void(0);" onclick="deleteCartItem('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                return $dd;
            })
            ->rawColumns(['action'])
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
        }
        return view('finance.payment.lists');
    }



}
