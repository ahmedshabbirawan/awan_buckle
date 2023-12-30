<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\IssuanceItem;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

use App\Models\StockItem;

class StockItemController extends Controller
{
    //

    function searchStockItem(Request $request){
        $item = StockItem::with(['product','project'])->where('serial_number',$request->get('sn'))->first();
        if( isset($item->id) ){
            $item->attribute    = $item->product->attributeHTML();
            $item->category     = $item->product->categoryHTML();
            return $item;   
        }else{
            return response(['message' => 'No record found!'],422);
        }
    }


    function available_item_by_product_ajax(Request $request){
        $productID      = $request->get('product_id');
        $data['items']  = StockItem::with(['itemProduct'])->where('product_id',$productID)->where(function($query) use ($request){
        $projectID      = $request->get('project_id');
            if($projectID != ''){
                $query->where('project_id',$projectID);
            }
        })->where('available_qty','>',0)->get(); 
        return view('Stock.Delivery.ajax_view.item_select',$data);
    }


    function all_item_by_product_datatable(Request $request){
        
        $results = StockItem::with(['project','itemProduct', 'stockDelivery', 'stockDelivery.Supplier:id,name'])->with('holderInfo.employee')->select()->where(function($query) use ($request){

            $productID      = $request->get('product_id');    
            if($productID != ''){
                $query->where('product_id',$productID);
            }
        })->orderBy('id','ASC');

        return Datatables::of($results)
        ->addColumn('project_name', function ($row) {
            return optional($row->project)->name;
        })
        ->addColumn('employee_name', function ($row) {
            $employee =  optional($row->holderInfo)->employee;
            return optional($employee)->full_name;
        })
        ->addColumn('employee_name', function ($row) {
            $employee =  optional($row->holderInfo)->employee;
            return optional($employee)->full_name;
        })
        ->addColumn('supplier_name', function ($row) {
            $supplier =  optional($row->stockDelivery)->supplier;
            return optional($supplier)->name;
        })
        ->addColumn('expire_date', function ($row) {
            return  optional($row->itemProduct)->warranty_date;
        })
        ->addColumn('delivery_info', function ($row) {
            $delivery   =  $row->stockDelivery;
            $html       = '';
            if(isset($delivery)){
                $html .= 'Purchase Date : <b>'.$delivery->purchased_date.'</b><br>';
                $html .= 'Challan # : <b>'.$delivery->delivery_challan_no.'</b><br>';
            }
            return $html;
        })
        ->addColumn('action', function ($row) {
                $statusIcon = 'fa fa-ban';
                if($row->status == 1){
                    $statusIcon = 'fa fa-check-circle-o';
                }
                $action = '<div class="hidden-sm hidden-xs btn-group">';
                 $action .= '<a href="'.route('stocks.item.history', $row->id).'" target="_blank" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
                // $action .= '<a href="'.route('supplier.edit', $row->id).'" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Edit" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';
                // $action .= '<a href="javascript:void(0);" onclick="changeStatus('.$row->id.');" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Change Status" ><i class="ace-icon '.$statusIcon.' bigger-120"></i></a>';
                // $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                $action .= '</div>';
                return $action;
        })->rawColumns(['condition_status_label','action','delivery_info'])
        //  ->orderColumn('id', 'DESC')
        ->make(true);
    }




    function itemIssuanceHistory(Request $request){
        // issuance_item

        if($request->ajax()){
            $groupBy    = $request->get('group_by');
            $items      = IssuanceItem::with(['product','project','subCategory','returnDetail'])
          //   ->whereRelation('subCategory', 'parentCategoryId',2)
            ->where(function($query) use ($request) {
                $empID              = $request->get('emp_id');
                $return_detail_id   = $request->get('return_detail_id');
                //  $itemStatus = $request->get('item_status');
                $itemID             = $request->get('item_id');
                $returnable         = $request->get('returnable');

                if( $empID != '' ){
                    $query->where('emp_id',$empID);
                }
                if( $returnable == '1' ){
                    $query->whereNull('return_date');
                }
                if($return_detail_id != ''){
                    $query->where('return_detail_id',$return_detail_id);
                }

                if( $itemID != '' ){
                    $query->where('item_id',$itemID);
                }
            })->orderBy('id','DESC'); 
            
            
            
            $items = $items->get();
            return Datatables::of($items)
            ->addColumn('product_detail', function ($row) {
                $item =  "Product : ".$row->product->name.'<br>';
                $item .= "Serial  : ".$row->serial_number.'<br>';
                $item .= "Project : ".optional($row->project)->name;
                return $item;
            })
            ->addColumn('product_attribute', function ($row) {
                return str_replace(' / ','</br>',strip_tags($row->product->attributeHTML()));
            })
            ->addColumn('item_condition', function ($row) {
                return $row->return_condition_status;
            })
            ->addColumn('employee_detail', function ($row) {
                return '<a  href="'.route('Settings.employee.view',$row->emp_id).'">'.$row->employee->full_name.'</a>';
            })
            ->addColumn('project_detail', function ($row) {
                return optional($row->project)->name;
            })
            ->addColumn('status_label', function ($row) {
                if($row->status == 1){
                    return '<span class="label label-sm label-success">'.$row->status_label.'</spna>';
                }else{
                    return '<span class="label label-sm label-danger">'.$row->status_label.'</spna>';
                }
            })
            ->addColumn('action', function ($row) {
                $statusIcon = 'fa fa-ban';
                if($row->status == 1){
                    $statusIcon = 'fa fa-check-circle-o';
                }
    
                $action = '<div class="hidden-sm hidden-xs btn-group">';
                $action .= '<a href="'.route('issuance.detail', ($row->issue_key)?$row->issue_key:'NA' ).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
                $action .= '</div>';
                return $action;
            })->rawColumns(['product_detail','product_attribute','return_receipt_file','status_label','action','item_condition','employee_detail'])
            ->make(true);
            }else{
                // return view('Issuance.lists');
                
            }

    }


    function itemHistory($itemID, Request $request){
        $item = StockItem::find($itemID);
        
        $history      = IssuanceItem::with(['product','employee','project','subCategory','returnDetail','issuedBy'])
          ->where(function($query) use ($request, $itemID) {

              // $itemID             = $request->get('item_id');
              if( $itemID != '' ){
                  $query->where('item_id',$itemID);
              }

          })->orderBy('id','DESC')->get(); 


         //  print_r($history); exit;
        

        $data['itemID']     = $itemID;
        $data['item']       = $item;
        $data['row']        = $item->product;
        $data['history']    = $history;

        $data['categories']   = $item->product->categories_tree();
       //  dd($item->product());

        return view('Product.item_history',$data);
    }

    

}
