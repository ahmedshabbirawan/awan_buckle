<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SaleCart;
use App\Models\SaleOrder;
use App\Models\SaleOrderItem;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\StockAdjustment;
use App\Models\StockDelivery;
use App\Models\StockExchange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


use App\Models\StockItem;
use App\Models\Supplier;
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
        $currentDateTime = date('Y-m-d') . ' 23:59:59';
        $lastDay         = Carbon::now()->subDays($day)->format('Y-m-d');
        $lastDayDateTime = $lastDay . ' 00:00:01';
        $betweenDates    = [$lastDayDateTime, $currentDateTime];

        $whereShop = [];
        if (auth()->check() && auth()->user()->hasRole('Shop Manager')) {
            $whereShop['shop_id'] = auth()->user()->shop_id;
        }

        // 1. Sales Orders
        $saleOrdersQuery = SaleOrder::whereBetween('created_at', $betweenDates)->where($whereShop);
        $saleOrderCount  = $saleOrdersQuery->count();
        $grossSaleAmount = (float)$saleOrdersQuery->sum('total_price');

        // 2. Sales Returns
        $saleReturnQuery = SaleReturn::whereBetween('created_at', $betweenDates);
        if (auth()->check() && auth()->user()->hasRole('Shop Manager')) {
            $saleReturnQuery->where('shop_id', auth()->user()->shop_id);
        }
        $saleReturnCount  = $saleReturnQuery->count();
        $saleReturnIds    = $saleReturnQuery->pluck('id');

        // Calculate return amount from SaleReturnItem
        $saleReturnAmount = (float)SaleReturnItem::whereIn('sale_return_id', $saleReturnIds)
            ->join('products', 'products.id', '=', 'sale_return_items.product_id')
            ->selectRaw('SUM(sale_return_items.qty * products.price) as total')
            ->value('total') ?: 0;

        $netSaleAmount = max(0, $grossSaleAmount - $saleReturnAmount);

        // 3. Cost of Goods Sold (COGS)
        $orderIds = $saleOrdersQuery->pluck('id');
        $grossCogs = (float)SaleOrderItem::whereIn('order_id', $orderIds)
            ->selectRaw('SUM(qty * COALESCE(cost_price, 0)) as total_cogs')
            ->value('total_cogs') ?: 0;

        $returnedCogs = (float)SaleReturnItem::whereIn('sale_return_id', $saleReturnIds)
            ->join('products', 'products.id', '=', 'sale_return_items.product_id')
            ->selectRaw('SUM(sale_return_items.qty * COALESCE(products.cost_price, 0)) as ret_cogs')
            ->value('ret_cogs') ?: 0;

        $netCogs = max(0, $grossCogs - $returnedCogs);
        $grossProfit = $netSaleAmount - $netCogs;
        $profitMargin = $netSaleAmount > 0 ? round(($grossProfit / $netSaleAmount) * 100, 1) : 0;

        // 4. Purchases
        $deliveryQuery = StockDelivery::whereBetween('created_at', $betweenDates);
        $purchaseCount = $deliveryQuery->count();
        $purchaseAmount = (float)$deliveryQuery->sum('delivery_amount');

        $statsData['sale_order_count']      = $saleOrderCount;
        $statsData['gross_sale_amount']     = round($grossSaleAmount);
        $statsData['sale_amount']           = round($netSaleAmount);
        $statsData['sale_return_amount']    = round($saleReturnAmount);
        $statsData['cogs_amount']           = round($netCogs);
        $statsData['profit_amount']         = round($grossProfit);
        $statsData['profit_margin']         = $profitMargin;
        $statsData['purchase_count']        = $purchaseCount;
        $statsData['purchase_amount']       = round($purchaseAmount);
        $statsData['customer_create']       = Customer::whereBetween('created_at', $betweenDates)->count();
        return $statsData;
    }


    function getPropertiesCounts(){
        $whereShop = [];
        if (auth()->check() && auth()->user()->hasRole('Shop Manager')) {
            $whereShop['shop_id'] = auth()->user()->shop_id;
        }

        $statsData['products']         = Product::count();
        $statsData['pos_count']        = SaleOrder::where($whereShop)->count();
        $statsData['item_total']       = $statsData['pos_count']; // Backwards compatibility for dashboard button
        $statsData['sale_amount']      = SaleOrder::where($whereShop)->sum('total_price');
        $statsData['stock_exchange']   = StockExchange::count();
        $statsData['stock_adjustment'] = StockAdjustment::where($whereShop)->count();
        $statsData['customer']         = Customer::count();
        $statsData['purchases']        = StockDelivery::count();
        return $statsData;
    }


}
