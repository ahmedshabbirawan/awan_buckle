<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ParentCategory;
use App\Models\Product;
use App\Models\StockDelivery;
use App\Models\StockDeliveryProduct;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Uom;
use App\Models\Shop;
use App\Util\Util;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\StockItem;
use App\Http\Requests\StockDeliveryFormRequest;
use App\Models\ProductAvailable;
use App\Models\ProductCategory;
use App\Models\Stock;
use App\Models\StockAdjustment;
use App\Models\StockAudit;
use Illuminate\Support\Str;

use App\Traits\Definitions;
use Carbon\Carbon;

class SimpleStockDeliveryController extends Controller{


    function index(Request $request){
       
        $productID = $request->get('product_id');
        if($request->ajax()){
            $results =  StockDelivery::with(['Supplier'])->select();
            if($productID != ''){
                $results =  $results->whereRelation('deliveryProduct', 'product_id',$productID); 
            }
            return Datatables::of($results)
            ->addColumn('info_no', function ($row) {
                if($row->po_loa_loi != ''){
                    return $row->po_loa_loi;
                }else{
                    return $row->delivery_challan_no;
                }
            })
            ->addColumn('supplier', function ($row) {
                return  optional($row->supplier)->name;
            })
            ->addColumn('rec_info', function ($row) {
                return '<b> Name : '; // .$row->rec_by_name.' <br> Designation : '.$row->rec_by_designation.' <br>   Cell : '.$row->rec_by_phone.'</b>';
            })->addColumn('hand_info', function ($row) {
                return '<b> Name : ';//m.$row->hand_name.' <br> Designation : '.$row->hand_designation.' <br>   Cell : '.$row->hand_phone.'</b>';
            })->addColumn('project_name', function ($row) {
                    return ''; // optional(Project::find($row->project_id))->name;
            })->addColumn('items', function ($row) {
                return StockItem::where('stock_delivery_id', $row->id)->count();
            })->addColumn('status_label', function ($row) {
                return '';
            })->addColumn('purchased_date_new', function ($row) {
                return $row->created_at->format('d-m-Y');
            })
            ->addColumn('action', function ($row) {
                $statusIcon = 'fa fa-ban';
                if($row->status == 1){
                    $statusIcon = 'fa fa-check-circle-o';
                }
                $action = '<div class="hidden-sm hidden-xs btn-group">';
                $action .= '<a href="'.route('stocks.delivery.detail',$row->id).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
                $action .= '</div>';
                return $action;
            })->rawColumns(['status_label','rec_info','hand_info','action','purchased_date_new'])
        //  ->orderColumn('id', 'DESC')
            ->make(true);
        }
        return view('Stock.Delivery.lists');
    }


    public function create(){
        $data['warehouses']     = Warehouse::all()->pluck('name');
        $data['projects']       = [];
        $data['shops']          = Shop::all()->pluck('name','id');
        $data['suppliers']      = Supplier::all()->pluck('name','id');
        $data['designations']   = Util::designations();
        $data['sub_category']   = ParentCategory::with('subCategories')->orderBy('name','DESC')->get();
        $data['uoms']           = Uom::get()->pluck('name','id');
        return view('Stock.Delivery.simple_create',$data);
    }


  


    function save(StockDeliveryFormRequest $request){
    
        $data = $request->only(['rec_by_name', 'rec_by_designation', 'rec_by_cnic', 'rec_by_phone', 
        'hand_name', 'hand_designation','hand_cnic', 'hand_phone',
        'purchased_date', 'project_id', 'project_dg', 
        'po_loa_loi','amount_category','delivery_amount','supplier_id','delivery_challan_no','warehouse_id',
        'stock_ledger_reference'
        ]);

       //  dd($data);

       $data['purchased_date'] = Carbon::parse($data['purchased_date'])->format('Y-m-d');

        $userID             = auth()->user()->id;
        $projectID          = 1; // $data['project_id'];
                
        $whereArr           = array('status' => 0, 'created_by' => $userID); 
        $deliveryProducts   = StockDeliveryProduct::where($whereArr)->get();

        if( count($deliveryProducts)  == 0 ){
            return response(['message' => 'No record found!'],422);
        }

        //$filesData          = $this->uploadDeliveryDocuments($request);
        //$data               = array_merge($data,$filesData);

// dd($data);

        try{
            DB::beginTransaction();
            $row                = StockDelivery::create($data);
            $stockDeliveryID    = $row->id;
            $time               = time();
            foreach($deliveryProducts as $deliverProduct){
                $shopID                 =   $deliverProduct->shop_id;
                $productID              =   $deliverProduct->product_id;
                $productCategoryID      =   0; // $deliverProduct->product_cat_id;
                $subCategoryID          =   0; // $deliverProduct->sub_cat_id;
                $stockDeliveryProductID =   $deliverProduct->id;
                //  $ProductStockItemCount  =   (Int) StockItem::where('product_id',$productID)->where('shop_id',$shopID)->sum('qty');
                //  $project                =   Project::find($projectID);
                //  $productCategory        =   ProductCategory::find($productCategoryID);
                $qty = $deliverProduct->quantity;
                $deliverProduct->status             = 1;
                $deliverProduct->stock_delivery_id  = $stockDeliveryID;
                $deliverProduct->save();
                $snArr                              = ($deliverProduct->serial_numbers != '')? explode(',',$deliverProduct->serial_numbers):[];
                // $itemKey = 'tb/'.Str::slug($project->code,'-').'/'.Str::slug($productCategory->name).'/';
                $itemKey = 'tb/'.$productID;
                /*  MANAGE AVAILABLE ITEMS  */
                $proAvail = [];
                $proAvail['shop_id'] = $shopID;
                $proAvail['product_id'] = $productID;

               //  dd($qty);

                Product::where(['id' => $productID])->update(['cost_price' => $deliverProduct->unit_price]);

                ProductAvailable::manageStockByShopAndProductId($qty, StockAudit::STOCK_ACTION_ADD, $productID, $shopID, StockAudit::STOCK_OBJECT_TYPE_PURCHASE, $stockDeliveryID );

                /*
                $productAvailable = ProductAvailable::where($proAvail)->first();
                $qtyWhenAdjust = 0;
                $newQty = 0; 
                if(isset($productAvailable->product_id)){
                    $newQty = $productAvailable->qty + $qty;
                    $productAvailable->qty      = $newQty;
                    $productAvailable->save();
                    $qtyWhenAdjust = $productAvailable->qty;
                }else{
                    $proAvail['qty'] = $qty;
                    ProductAvailable::create($proAvail);
                }
                */
            }
            DB::commit();
            if($request->ajax()){
                return response(['status' => true, 'data' => $row, 'message' => 'Product Add successfully.' ]);
            }else{
                return redirect(route('product.list'))->with('success','Product added successful!');
            }
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['message' => $e->getLine().' Error : '. $e->getMessage(). '. Please Contact to Developer'], 500);
        }
    }


   


    function uploadDeliveryDocuments(Request $request){
        $data = array();
        if($request->hasFile('ledger_copy_file')){
            //Getting file name with extension
            $fileNameWithExt = $request->file('ledger_copy_file')->getClientOriginalName();
            //Get just file name
            $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            //Get just ext
            $extension = $request->file('ledger_copy_file')->getClientOriginalExtension();
            //Filename to store
            $fileNameToStore = "ledger-".$filename.'_'.time().'.'.$extension;
            //Upload Image
            $path = $request->file('ledger_copy_file')->storeAs('public/documents', $fileNameToStore);
            $data['ledger_copy_file'] =  $fileNameToStore;
        }
        if($request->hasFile('purschase_copy_file')){
            //Getting file name with extension
            $fileNameWithExt = $request->file('purschase_copy_file')->getClientOriginalName();
            //Get just file name
            $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            //Get just ext
            $extension = $request->file('purschase_copy_file')->getClientOriginalExtension();
            //Filename to store
            $fileNameToStore = "purchase_order-".$filename.'_'.time().'.'.$extension;
            //Upload Image
            $path = $request->file('purschase_copy_file')->storeAs('public/documents', $fileNameToStore);
            $data['purschase_copy_file'] =  $fileNameToStore;
        }
       // 'delivery_challan_copy_file' , 'ledger_copy_file', 'purschase_copy_file', 'inspection_report_copy_file'
       if($request->hasFile('delivery_challan_copy_file')){
        //Getting file name with extension
        $fileNameWithExt = $request->file('delivery_challan_copy_file')->getClientOriginalName();
        //Get just file name
        $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
        //Get just ext
        $extension = $request->file('delivery_challan_copy_file')->getClientOriginalExtension();
        //Filename to store
        $fileNameToStore = "delivery_challan-".$filename.'_'.time().'.'.$extension;
        //Upload Image
        $path = $request->file('delivery_challan_copy_file')->storeAs('public/documents', $fileNameToStore);
        $data['delivery_challan_copy_file'] =  $fileNameToStore;
        }
        //inspection_report_copy_file
        if($request->hasFile('inspection_report_copy_file')){
            //Getting file name with extension
            $fileNameWithExt = $request->file('inspection_report_copy_file')->getClientOriginalName();
            //Get just file name
            $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            //Get just ext
            $extension = $request->file('inspection_report_copy_file')->getClientOriginalExtension();
            //Filename to store
            $fileNameToStore = "inspection_report-".$filename.'_'.time().'.'.$extension;
            //Upload Image
            $path = $request->file('inspection_report_copy_file')->storeAs('public/documents', $fileNameToStore);
            $data['inspection_report_copy_file'] =  $fileNameToStore;
        }



        return $data;

    }



    function upload_document(Request $request){
        Validator::make($request->all(), [
            'document_key'          => 'required',
            'stock_delivery_id'     => 'required',
            'document_file'         => 'required'
        ]);

        if($request->hasFile('document_file')){

            $key    = $request->get('document_key');
            $id     = $request->get('stock_delivery_id');

            //Getting file name with extension
            $fileNameWithExt = $request->file('document_file')->getClientOriginalName();
            //Get just file name
            $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            //Get just ext
            $extension = $request->file('document_file')->getClientOriginalExtension();
            //Filename to store
            $fileNameToStore = $key.'-'.time().'-'.rand(99,9999).'.'.$extension;
            //Upload Image
            $path = $request->file('document_file')->storeAs('public/documents', $fileNameToStore);

            if($key == 'purchase_order'){
                $data['purschase_copy_file'] =  $fileNameToStore;
            }else if($key == 'delivery_challan'){
                $data['delivery_challan_copy_file'] =  $fileNameToStore;
            }else if($key == 'inspection_report'){
                $data['inspection_report_copy_file'] =  $fileNameToStore;
            }
            $supplier = StockDelivery::find($id)->forceFill($data)->update();

            return response(['status' => true, 'message' => 'Upload File successfully.' ]);

        }else{
            return response(['message' => 'No file found!'],422);
        }

    }



    public function detail($deliveryID){
        return '';
        $data['row']     = StockDelivery::find($deliveryID);
        $data['project']        =  []; //Project::find($data['row']->project_id); //Project::with('manager')->whereNotNull('manager_id')->pluck('name','id', 'manager.name');
        $data['suppliers']      = Supplier::all()->pluck('name','id');
        $data['designations']   = Util::designations();

        return view('Stock.Delivery.detail',$data);
    }


    
}
