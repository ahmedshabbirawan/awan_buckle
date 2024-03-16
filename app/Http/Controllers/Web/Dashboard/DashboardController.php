<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SaleCart;
use App\Models\SaleOrder;
use App\Models\SaleReturn;
use App\Models\StockAdjustment;
use App\Models\StockDelivery;
use App\Models\StockExchange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


use App\Models\StockItem;
use App\Models\Supplier;
// use App\Models\
use App\Traits\Definitions;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function view(){
        
        return view('Dashboard.dashboard');
    }


    function dashboardStats(){
        $dayWiseReport      = array();
        
        $dayWiseReport[]    =   [    
            'labels' => ['days' => 1],
            'reports' => $this->getDaysWiseReportByDay(1)
        ];

        $dayWiseReport[]    =   [    
            'labels' => ['days' => 7],
            'reports' => $this->getDaysWiseReportByDay(7)
        ];

        $dayWiseReport[]    =   [    
            'labels' => ['days' => 14],
            'reports' => $this->getDaysWiseReportByDay(14)
        ];

        $dayWiseReport[]    =   [    
            'labels' => ['days' => 30],
            'reports' => $this->getDaysWiseReportByDay(30)
        ];






        $working    = Definitions::$CONDITION_WORKING;
        $dead       = Definitions::$CONDITION_DEAD;
        $damaged    = Definitions::$CONDITION_DAMAGED;
        $faulty     = Definitions::$CONDITION_FAULTY;
       

        // $physical = StockItem::with(['subCategory' => function($query){
        //   //  $query->groupBy('condition_status');
        //   $query->where('parentCategoryId',2);
        //   // ->where('condition_status');
        // }])->selectRaw( 
        //     DB::raw(' SUM(qty) as item_total'),
        // )
        // ->selectRaw( 
        //     DB::raw(' ( select SUM(available_qty) from stock_items where condition_status = '.$working.' AND available_qty = qty ) as item_available ')
        // )
        // ->selectRaw( 
        //     DB::raw(' ( select SUM(qty) from stock_items where condition_status = '.$dead.' ) as dead_items ')
        // )
        // ->selectRaw( 
        //     DB::raw(' ( select SUM(qty) from stock_items where condition_status = '.$dead.' ) as dead_items ')
        // )
        // ->get();

        // $conditions = StockItem::with(['subCategory' => function($query){
        //     //  $query->groupBy('condition_status');
        //     $query->where('parentCategoryId',2);
        //     // ->where('condition_status');
        //   }])
        //   ->selectRaw( 
        //       DB::raw(' ( select SUM(qty) from stock_items where condition_status = '.$working.' ) as dead_items ')
        //   )
        //   ->selectRaw( 
        //       DB::raw(' ( select SUM(qty) from stock_items where condition_status = '.$dead.' ) as dead_items ')
        //   )
        //   ->get();




        // $physical = $physical[0];

        // $data['physical'] = [ 
        //     'item_total' => $physical->item_total, 
        //     'item_available' => $physical->item_available, 
        //     'item_issued' => ($physical->item_total - $physical->item_available),
        //     'dead_items' => $physical->dead_items,

        //     'dayWiseReport' => $dayWiseReport
        // ];



        $data['physical'] = [ 
            'item_total' => '', 
            'item_available' => '', 
            'item_issued' => '',
            'dead_items' => ''
        ];
        $data['dayWiseReport'] = $dayWiseReport;
        $data['property_count'] = $this->getPropertiesCounts();
         

        return $data;
    }

    function getDaysWiseReportByDay($day){
        $currentDateTime = date('Y-m-d').' 23:59:00';

        $lastDay           = Carbon::parse()->subDays($day)->format('Y-m-d');
        $lastDayDateTime   = $lastDay.' 00:00:01';
        $betweenDates      = [$lastDayDateTime,$currentDateTime];

        $whereArr = [];

        if(auth()->user()->hasRole('Shop Manager')){
            $whereArr['shop_id'] = auth()->user()->shop_id;
        }

        $statsData['sale_order_count']      = SaleOrder::whereBetween('created_at',$betweenDates)->where($whereArr)->count();
        $statsData['sale_amount']           = SaleOrder::whereBetween('created_at',$betweenDates)->where($whereArr)->sum('total_price'); //->get();
       //  $statsData['sale_return_amount']    = SaleReturn::where()->count();
      //  $statsData['profit_amount']         = SaleReturn::where()->count();
        $statsData['purchase_count']        = StockDelivery::whereBetween('created_at',$betweenDates)->where([])->count();
        $statsData['purchase_amount']       = StockDelivery::whereBetween('created_at',$betweenDates)->where([])->sum('delivery_amount');
        $statsData['customer_create']       = Customer::whereBetween('created_at',$betweenDates)->count();
        return $statsData;
    }


    function getPropertiesCounts(){
        $statsData['products']           = Product::count();
        $statsData['sale_amount']        = SaleCart::count();
        $statsData['stock_exchange']     = StockExchange::count();
        $statsData['stock_adjustment']   = StockAdjustment::count();
        $statsData['customer']           = Customer::count();
        $statsData['purchases']          = StockDelivery::count();
        return $statsData;
    }


}
