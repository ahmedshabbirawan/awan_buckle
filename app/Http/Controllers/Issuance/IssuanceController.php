<?php

namespace App\Http\Controllers\Issuance;

use App\Http\Controllers\Controller;
use App\Models\Issuance;
use Illuminate\Http\Request;



use App\Models\ParentCategory;
use App\Models\Project;
use App\Models\StockDelivery;
use App\Models\StockDeliveryProduct;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Uom;
use App\Models\IssuanceItemTemp;
use App\Util\Util;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

use App\Models\StockItem;
use App\Http\Requests\StockDeliveryFormRequest;
use App\Models\Employee;
use App\Models\IssuanceItem;
use App\Models\ProductCategory;
use App\Models\SubCategory;
use App\Models\ReturnDetail;

use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Traits\Definitions;

class IssuanceController extends Controller
{

    function __construct(){
        define('ISSUE_KEY','issue_temp_item');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->ajax()){


        $groupBy = $request->get('group_by');


        $items = IssuanceItem::with(['product','project','subCategory','returnDetail', 'issuedBy','employee:id,first_name,last_name,designation'])
        ->whereRelation('subCategory', 'parentCategoryId',2)
        ->where(function($query) use ($request) {
            $empID              = $request->get('emp_id');
            $return_detail_id   = $request->get('return_detail_id');
            $issue_key          = $request->get('issue_key');

            
           //  $itemStatus = $request->get('item_status');
            $returnable = $request->get('returnable');
            if( $empID != '' ){
                $query->where('emp_id',$empID);
            }
            if( $returnable == '1' ){
                $query->whereNull('return_date');
            }

            if($return_detail_id != ''){
                $query->where('return_detail_id',$return_detail_id);
            }

            if($issue_key != ''){
                $query->where('issue_key',$issue_key);
            }




        }); 
        
        
        if($groupBy != ''){
            $items = $items->selectRaw('*,count(id) as item_count')->groupBy('issue_key');
        }
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
            $condition = $row->return_condition_status;
            $select  = '<select name="return_condition_status_'.$row->id.'">';
            $select .= '<option value="1" '.( ($condition == Definitions::$CONDITION_WORKING) ? 'selected="selected"' : '' ).' >Working Condition</option>';
            $select .= '<option value="2" '.( ($condition == Definitions::$CONDITION_DEAD) ? 'selected="selected"' : '' ).' >Dead</option>';
            $select .= '<option value="3" '.( ($condition == Definitions::$CONDITION_DAMAGED) ? 'selected="selected"' : '' ).' >Damaged</option>';
            $select .= '<option value="4" '.( ($condition == Definitions::$CONDITION_FAULTY) ? 'selected="selected"' : '' ).' >Faulty</option>';
            $select .= '</select>';
            return $select;
        })
        ->addColumn('item_count', function ($row) {
            return (isset($row->item_count))?$row->item_count:'';
        })
        ->addColumn('employee_detail', function ($row) {
            return '<a  href="'.route('Settings.employee.view',$row->emp_id).'">'.$row->employee->full_name.'</a>';
        })
        ->addColumn('issueBy', function ($row) {
            return $row->issuedBy->name;
            // return '<a  href="'.route('Settings.employee.view',$row->emp_id).'">'.$row->employee->full_name.'</a>';
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
        ->addColumn('return_receipt_file', function ($row) {
            $file = optional($row->returnDetail)->return_report_file;
            if($file != ''){
                $fileLink = '<div class="hidden-sm hidden-xs btn-group" >';
                $fileLink .= '<a href="'.asset('storage/documents/'.$file).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail"><i class="ace-icon fa fa-eye bigger-120"></i></a>';
                $fileLink .= '</div>';
                return $fileLink;
            }else{
                return 'N/A';
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
        })->rawColumns(['product_detail','product_attribute','return_receipt_file','status_label','action','item_condition','employee_detail','return_condition_status_label'])
        ->make(true);
        }else{
            return view('Issuance.lists');
        }
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
       //  $data['warehouses']     = Warehouse::all()->pluck('name','id');
        // $data['projects']       = Project::with('manager')->whereNotNull('manager_id')->pluck('name','id', 'manager.name');
        $data['projects']       = [];  //Project::orderBy('name','ASC')->get();
        $data['suppliers']      = Supplier::all()->pluck('name','id');
        $data['designations']   = Util::designations();


        /*
        $data['sub_category']   = ParentCategory::with(['subCategories' => function($query){
         return $query->whereHas('stockItems');
        }])->orderBy('name','DESC')->get();
        */

        $data['sub_category']   = ParentCategory::with(['subCategories'])->orderBy('name','DESC')->get();


        $data['uoms']           = Uom::get()->pluck('name','id');
        return view('Issuance.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request){

        $rules['issue_date']        = 'required';
        $rules['employee_cnic']     = 'required';
        $rules['emp_id']            = 'required';
        $validator                  = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()->first()],422);
        }


        $issueData = $request->get('issue_date');

        $empID            = $request->get('emp_id');
        $tempItems        = IssuanceItemTemp::where('emp_id',$empID)->get();
        $itemFound        = count($tempItems);

        if($itemFound == 0){
            return response(['message' => 'No items available'],422);
        }

        $notFound         = 0;
        $itemWillDelete   = [];

        

        try{
            DB::beginTransaction();


            
            $key = $this->genrateIssuanceKey();
            foreach($tempItems as $tItem):
                $stockItem = StockItem::findOrFail($tItem->item_id);


               // dd($stockItem);

                if( ($tItem->qty < 1) && ($stockItem->available_qty <  $tItem->qty) ){
                    $notFound++;
                    continue;
                }

                $itemData = $tItem->only('item_id','project_id','emp_id','product_id','product_cat_id','qty','sub_cat_id','serial_number','is_loan','is_data_center','remarks','issue_date', 'status','created_at','updated_at');

                $itemData['serial_number']    = $stockItem->serial_number;
                $itemData['product_cat_id']   = $stockItem->product_cat_id;
                $itemData['sub_cat_id']       = $stockItem->sub_category_id;
                $itemData['issue_date']       = $issueData;
                $itemData['issue_key']        = $key;


                IssuanceItem::create($itemData);

                $stockItem->available_qty = $stockItem->available_qty - $tItem->qty;
                $stockItem->save();
                $itemWillDelete[] = $tItem->item_id;
            endforeach;
            IssuanceItemTemp::whereIn('item_id',$itemWillDelete)->delete();

            if( $notFound > 0 ){
                DB::rollBack();
            }else{
                DB::commit();
            }

            if( $notFound > 0 ){
                return response(['message' => $notFound.' items not available in quantity']);
            }else{
                return response(['message' => 'Item added successfully!']);
            }            
        }catch(\Exception $e){
            return response()->json(['message' => 'Error.Please Contact   Support'. $e->getMessage()], 500);
        }
    }


    function itemReturn(Request $request){
        $rules['emp_id']        = 'required';
        $rules['emp_cnic']      = 'required';
        $rules['item_ids']      = 'required';
        $validator                  = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()->first()],422);
        }



        $return_report_file = '';

        if($request->hasFile('return_report_file')){
            //Getting file name with extension
            $fileNameWithExt = $request->file('return_report_file')->getClientOriginalName();
            //Get just file name
            $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            //Get just ext
            $extension = $request->file('return_report_file')->getClientOriginalExtension();
            //Filename to store
            $fileNameToStore = 'return-report-'.time().'-'.rand(99,9999).'.'.$extension;
            //Upload Image
            $path = $request->file('return_report_file')->storeAs('public/documents', $fileNameToStore);
            $return_report_file =  $fileNameToStore;
        }


        try{
            DB::beginTransaction();
            $empID = $request->get('emp_id');
            $items = $request->get('item_ids');

            $returnDetail = ReturnDetail::create([
                'emp_id'                => $empID,
                'item_return_count'     => count($items),
                'item_return_ids'       => implode(',',$items),
                'return_report_file'    => $return_report_file
            ]);


          //   dd($returnDetail); exit;

            foreach($items as $itemID){


                $remarks = $request->get('remarks_'.$itemID);
                $condition = $request->get('return_condition_status_'.$itemID);

                $issueItemObj = IssuanceItem::find($itemID);
                $issueItemObj->return_detail_id = $returnDetail->id;
                $issueItemObj->return_date      = Carbon::now()->format('Y-m-d');
                $issueItemObj->remarks                  = $remarks;
                $issueItemObj->return_condition_status  = $condition;
                $issueItemObj->save();

                $stockItem = StockItem::find($issueItemObj->item_id);
                $stockItem->available_qty = $stockItem->available_qty + $issueItemObj->qty;
                $stockItem->condition_status = $condition;
                $stockItem->save();
            }
            DB::commit();
            return response()->json(['message' => 'Items return successfully'], 200);
        }catch(\Exception $e){
            return response()->json(['message' => 'Error.Please Contact   Support'. $e->getMessage()], 500);
        }
    }


    function submitRemarks(Request $request){
        $rules['remarks']      = 'required';
        $rules['issuance_item_id']      = 'required';
        $rules['condition_status']      = 'required';
        $validator                  = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return response(['message' => $validator->errors()->first()],422);
        }

        $id         = $request->get('issuance_item_id');
        $remarks    = $request->get('remarks');
        $condition_status = $request->get('condition_status');

        $issueObj = IssuanceItem::find($id);
        $issueObj->remarks = $remarks;
        $issueObj->return_condition_status = $condition_status;
        $issueObj->save();

        return response(['message' => 'updated remarks successfully!']); 
    }


    function upload_hard_copy(Request $request){
        

        if($request->hasFile('hard_copy_file')){
            //Getting file name with extension
            $fileNameWithExt = $request->file('hard_copy_file')->getClientOriginalName();
            //Get just file name
            $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            //Get just ext
            $extension = $request->file('hard_copy_file')->getClientOriginalExtension();
            //Filename to store
            $fileNameToStore = "Issuance-".$filename.'_'.time().'.'.$extension;
            //Upload Image
            $path = $request->file('hard_copy_file')->storeAs('public/documents', $fileNameToStore);
            $data['hard_copy_file'] =  $fileNameToStore;


            $issue_key = $request->get('issue_key');

            IssuanceItem::where('issue_key',$issue_key)->update($data);

            return response(['message' => 'Upload successfully!']); 

        }

        return response()->json(['message' => 'Error.File not found'], 422);

    }


    function detail($key){
        $data['item'] = IssuanceItem::where('issue_key',$key)->first();
        if( empty($data['item']->id) ){
            abort(404);
        }
        $data['row'] = Employee::find($data['item']->emp_id);
        $data['emp'] = $data['row'];
        return view('issuance.detail',$data);
    }



    function genrateIssuanceKey(){
        $key = time().str_pad(mt_rand(99,99999),6,0,STR_PAD_LEFT);
        $res = IssuanceItem::where('issue_key',$key)->get();
        if( count($res) > 0 ){
            return $this->genrateIssuanceKey();
        }else{
            return $key;
        }
    }




    function getItemFromSession(Request $request){
        $userID     = auth()->user()->id;
        $deliveryID = $request->get('stock_delivery_id');
        $empID      = $request->get('emp_id');

        if($deliveryID != ''){
            $results    = IssuanceItemTemp::with(['product'])->where('stock_delivery_id',$deliveryID)->get();
        }else{
            $results    = IssuanceItemTemp::with(['product'])->where('emp_id',$empID)->where('status',1)->get();
        }

         return Datatables::of($results)
             ->addColumn('product_name', function ($row) {
                     return optional($row->product)->name.' '.$row->product->attributeHTML();
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
             ->addColumn('total_price', function ($row) {
                $qty    = (Int) $row->quantity;
                $price  = (Int) $row->unit_price;

                return $qty * $price;
            })
             ->addColumn('action', function ($row) {
                     $action = '<div class="hidden-sm hidden-xs btn-group">';
                     $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                     $action .= '</div>';
                     return $action;
             })->rawColumns(['action','product_category_name','product_name'])
             //  ->orderColumn('id', 'DESC')
             ->make(true);

    }

    // item_by_employee
    function itemByEmployee(){

    }


    function addItemToSession(Request $request){
        $rules['product_id']        = 'required';
        $rules['item_id']           = 'required|integer';
        $rules['qty']               = 'required|integer';

        $validator                  = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response(['message' => $validator->errors()->first()],422);
        }

        $qty = $request->get('qty');

        if($qty == 0){
            return response(['message' => 'Please give valid Quantity'],422);
        }

        try{
        
            $itemID = $request->get('item_id');
            $stockItem = StockItem::find($itemID);
            
            // $data                       = $request->only('item_id','project_id','emp_id','product_id','product_cat_id','sub_cat_id','serial_number','issue_date','qty');

            $data                       = $request->only('item_id','project_id','emp_id','product_id','qty','loan_return_date','is_loan','is_data_center','remarks');
            $data['product_cat_id']     = $stockItem->product_cat_id;
            $data['sub_cat_id']         = $stockItem->sub_category_id;
            $data['serial_number']      = $stockItem->serial_number;
        //  $data['product_cat_id']     = $request->get('product_category_id');
        //  $data['sub_cat_id']         = $request->get('sub_category_id');
        
            
            IssuanceItemTemp::create($data);
            return response()->json(['status' => true]);

        }catch(\Exception $e){
            return response()->json(['message' => 'Error.Please Contact   Support'. $e->getMessage()], 500);
        }
    }


    // 

    function addItemBySN(Request $request){
        $rules['serial_number_search']  = 'required';
        $rules['emp_id']                = 'required';
        $validator                  = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response(['message' => $validator->errors()->first()],422);
        }

        try{
        
        $sn             = $request->get('serial_number_search');
        $empID          = $request->get('emp_id');
        $stockItem      = StockItem::where('serial_number',$sn)->first();
        $isLoan         = $request->get('is_loan');
        $isDataCenter   = $request->get('is_data_center');
        $remarks        = $request->get('remarks');

        if( (!($stockItem->id)) || ($stockItem->available_qty < 1) ){
            return response(['message' => 'Quantity not available'],422);
        }

        // $data                       = $request->only('item_id','project_id','emp_id','product_id','product_cat_id','sub_cat_id','serial_number','issue_date','qty');
        // $data                       = $request->only('item_id','project_id','emp_id','product_id','qty');

        $data['item_id']            = $stockItem->id;
        $data['product_id']         = $stockItem->product_id;
        $data['product_cat_id']     = $stockItem->product_cat_id;
        $data['sub_cat_id']         = $stockItem->sub_category_id;
        $data['serial_number']      = $stockItem->serial_number; 
        $data['project_id']         = $stockItem->project_id;
        $data['emp_id']             = $empID;
        $data['qty']                = 1;

        $data['is_loan']            = ($isLoan == 1)?1:0;
        $data['is_data_center']     = ($isDataCenter == 1)?1:0;
        $data['remarks']            = $remarks;

        IssuanceItemTemp::create($data);
        return response(['message' => 'Add item successfully!'],200);
    }catch(\Exception $e){
        return response()->json(['message' => 'Error.Please Contact   Support'. $e->getMessage()], 500);
    }
    }

    function removeItemFromSession(Request $request){
        $id = $request->get('id');
        IssuanceItemTemp::find($id)->delete();
        return response(['message' => 'Item delete successfully!']);
    }



}
