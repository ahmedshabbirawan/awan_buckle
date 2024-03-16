<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ReturnDetail;
use Yajra\DataTables\DataTables;

use App\Models\Products;
use App\Models\Employee;
use App\Models\ProductAvailable;
use App\Models\SaleCart;
use App\Models\SaleItemTemp;
use App\Models\StockItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SaleBoardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->ajax()){
        $groupBy    = $request->get('group_by');
        $items      = SaleCart::select(DB::raw('count(id) as item_count, sale_key, MAX(updated_at) as last_update'))->where(function($query) use ($request) {
            //     $empID      = $request->get('emp_id');
            //     if( $empID != '' ){
            //         $query->where('emp_id',$empID);
            //     }
        })->groupBy('sale_key');
        
        $items = $items->get();
        return Datatables::of($items)
        ->addColumn('title', function ($row) {
            return '<a href="'.route('sale.board.create',$row->sale_key).'" target="_blank" >'.$row->sale_key.'</a>';
        })
        ->addColumn('item_count', function ($row) {
            return $row->item_count;
            // return count($row->returnItem); 
            // (isset($row->issuanceItem))?$row->item_count:'';
        })
        ->addColumn('last_update', function ($row) {
            // return $row->last_update->format('h:i d-m-Y');
            return Carbon::parse($row->last_update)->format('h:i d-m-Y');
            // return count($row->returnItem); // (isset($row->issuanceItem))?$row->item_count:'';
        })
        ->addColumn('action', function ($row) {
            $action = '<div class="btn-group">';
            $action .= '<a href="'.route('sale.board.create',$row->sale_key).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
            $action .= '<a href="javascript:void(0);" onclick="pos_app.deleteOnHoldItem('.$row->sale_key.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
            $action .= '</div>';
            return $action;
        })->rawColumns(['title','product_attribute','status_label','action'])
        ->make(true);
        }else{
            return view('sale.lists');
        }
    }


    function getProductItems(Request $request){
        if($request->ajax()){
            $loginShopID = auth()->user()->shop_id;
            $groupBy = $request->get('group_by');
            $shopID = $request->get('shop_id',$loginShopID);


            /*
            $items = StockItem::select('*',DB::raw('sum(available_qty) as total_qty'))
            ->with(['product:id,name','shop:id,name'])
            ->where('shop_id',$shopID)
            ->groupBy(DB::raw('product_id'))->get();
            */


            $items = ProductAvailable::select('*',DB::raw('sum(qty) as total_qty'))
            ->with(['product:id,name,price,image','shop:id,name'])
            ->where('shop_id',$shopID)
            ->groupBy(DB::raw('product_id'))->get();

            return Datatables::of($items)
            ->addColumn('image_url', function ($row) {
                return optional($row->product)->image_url;
            })->addColumn('product_detail', function ($row) {
                return optional($row->product)->name;
                // return count($row->returnItem); // (isset($row->issuanceItem))?$row->item_count:'';
            })->addColumn('shop_name', function ($row) {
                return ''; //$row->shop->name;
            })->addColumn('action', function ($row) {
                $action = '<a href="javascript:void(0);" onclick="addOneToCart('.$row->product_id.');" class="btn btn-xs btn-success" data-toggle="tooltip" title="Add to Cart" ><i class="ace-icon fa fa-plus bigger-120"></i></a>';
               //  $action .= '<a href="'.route('').'" target="_blank" class="btn btn-xs btn-primary ml-2" data-toggle="tooltip" title="Request Stock" ><i class="ace-icon fa fa-hand-pointer-o bigger-120"></i></a>';

                return $action;

            })->rawColumns(['product_detail','action'])
            ->make(true);
        }else{
            return view('sale.lists');
        }
    }

    function detail($id){
        $returnDetail      = ReturnDetail::find($id);
        $employee          = Employee::findOrFail($returnDetail->emp_id);
        return view('ReturnItem.detail',['detail_id' => $id, 'row' => $employee]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($salekey=''){
        if($salekey == ''){
            $saleKey = time();
            return redirect(route('sale.board.create',$saleKey));
        }
        $saleCartObj = SaleCart::where('sale_key', $salekey)->get()->first(); 
        return view('sale.create_order',['salekey' => $salekey, 'saleCartObj' => $saleCartObj]);
    }



    function deleteKeyItems($saleKey){
        SaleCart::where('sale_key',$saleKey)->delete();
        return response()->json(['status' => true, 'message' => 'Delete success.']);
    }


    function onHoldModalView(){
        return response()->json([
            'view' => View('sale.on_hold_modal')->render(),
            'data' =>   [],
            'status' => true,
            'message' => 'OK'
        ]);
    }


    /*
    
    $term = $request->get('term');
        $data['results'] = Customer::
        select('id','name', 'cnic', DB::raw("concat(name,' - ', IF(mobile IS NOT NULL, mobile,'')) as text"))
        //select(DB::raw("concat(first_name,' ',last_name,' (',cnic,')') as text, id"))
           // ->where('first_name', 'like', "%".$term."%")
           // ->orWhere('last_name', 'like', "%".$term."%")
            ->where('name','like','%'.$term.'%')
            ->orWhere('mobile','like','%'.$term.'%')
            ->limit(15)
            ->get();
           // ->pluck('first_name');
        return $data;
    
    */

    


    
}
