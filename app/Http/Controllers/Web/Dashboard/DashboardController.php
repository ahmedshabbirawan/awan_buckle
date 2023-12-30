<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


use App\Models\StockItem;
// use App\Models\
use App\Traits\Definitions;

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

        $working    = Definitions::$CONDITION_WORKING;
        $dead       = Definitions::$CONDITION_DEAD;
        $damaged    = Definitions::$CONDITION_DAMAGED;
        $faulty     = Definitions::$CONDITION_FAULTY;
       

        $physical = StockItem::with(['subCategory' => function($query){
          //  $query->groupBy('condition_status');
          $query->where('parentCategoryId',2);
          // ->where('condition_status');
        }])->selectRaw( 
            DB::raw(' SUM(qty) as item_total'),
        )
        ->selectRaw( 
            DB::raw(' ( select SUM(available_qty) from stock_items where condition_status = '.$working.' AND available_qty = qty ) as item_available ')
        )
        ->selectRaw( 
            DB::raw(' ( select SUM(qty) from stock_items where condition_status = '.$dead.' ) as dead_items ')
        )
        ->selectRaw( 
            DB::raw(' ( select SUM(qty) from stock_items where condition_status = '.$dead.' ) as dead_items ')
        )
        ->get();

        $conditions = StockItem::with(['subCategory' => function($query){
            //  $query->groupBy('condition_status');
            $query->where('parentCategoryId',2);
            // ->where('condition_status');
          }])
          ->selectRaw( 
              DB::raw(' ( select SUM(qty) from stock_items where condition_status = '.$working.' ) as dead_items ')
          )
          ->selectRaw( 
              DB::raw(' ( select SUM(qty) from stock_items where condition_status = '.$dead.' ) as dead_items ')
          )
          ->get();




        $physical = $physical[0];

        $data['physical'] = [ 
            'item_total' => $physical->item_total, 
            'item_available' => $physical->item_available, 
            'item_issued' => ($physical->item_total - $physical->item_available),
            'dead_items' => $physical->dead_items
        ];
        return $data;
    }


}
