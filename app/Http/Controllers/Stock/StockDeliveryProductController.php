<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ParentCategory;
use App\Models\Project;
use App\Models\StockDelivery;
use App\Models\StockDeliveryProduct;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Uom;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Category;
use App\Models\Stock;
use App\Models\StockDeliveryItem;
use App\Models\ProductAvailable;

use Illuminate\Support\Facades\Validator;

use App\Util\Util;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables;

use App\Http\Requests\StockDeliveryProductFormRequest;
use App\Models\StockItem;
use App\Models\SubCategory;
use League\Csv\Reader;


class StockDeliveryProductController extends Controller{




    function list(Request $request){
        //  $results = DB::table('stock_deliveries_products')->get();
        $userID     = auth()->user()->id;
        $deliveryID = $request->get('stock_delivery_id');

        

        if($deliveryID != ''){
            $results    = StockDeliveryProduct::with(['product'])->where('stock_delivery_id',$deliveryID)->get();
        }else{
            $results    = StockDeliveryProduct::with(['product'])->where('created_by',$userID)->where('status',0)->get();
        }

         return Datatables::of($results)
             ->addColumn('product_name', function ($row) {
                     return optional($row->product)->name;
             })->addColumn('product_attribute', function ($row) {
                return str_replace(' / ','</br>',strip_tags($row->product->attributeHTML()));
            })->addColumn('uom_code', function ($row) {
                 return $row->uom_code;
             })->addColumn('product_description', function ($row) {
                return optional($row->product)->description;
             })->addColumn('product_category_name', function ($row) {
                    return ''; 
                    $cat =  optional($row->product)->categories_tree();
                     return  $cat['category']['name'].' / '.$cat['sub_category']['name'].' / '.$cat['product_category']['name']; 
             })->addColumn('attributes_tags', function ($row) {
                 return '';
             })->addColumn('shop_name', function ($row) {
                return optional(Shop::find($row->shop_id))->name;
            })
             ->addColumn('total_price', function ($row) {
                $qty    = (Int) $row->quantity;
                $price  = (Int) $row->unit_price;
                return $qty * $price;
            })
            ->addColumn('sn_tags', function ($row) {
                return "<span class=\"label label-sm label-info arrowed arrowed-right \" >".str_replace(',','</span> &nbsp; <span class="label label-sm label-info arrowed arrowed-right" >',$row->serial_numbers).'</span>';
            })
             ->addColumn('action', function ($row) {
                     $statusIcon = 'fa fa-ban';
                     // if($row->status == 1){
                     // $statusIcon = 'fa fa-check-circle-o';
                     // }
                     $action = '<div class="hidden-sm hidden-xs btn-group">';
                    
                 //     $action .= '<a href="'.route('product.edit', $row->id).'" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Edit" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';
                  
                     $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                     
                     $action .= '</div>';
                     return $action;
             })->rawColumns(['action','product_category_name','product_name','product_attribute','sn_tags'])
             //  ->orderColumn('id', 'DESC')
             ->make(true);
     }


    function create(){
        $data['parentCat']    = []; // ParentCategory::all()->pluck('name','id');
        $data['sub_category']    = ParentCategory::with('subCategories')->get();
        $data['uoms']        = Uom::get()->pluck('name','id');
        return view('Stock.Delivery.stock_item_form',$data);
    }

    
   
 
 
    /* SAVE TEMP PRODUCT */
     function save(StockDeliveryProductFormRequest $request){
        $serialNumbers = $request->get('sn');
        if( !empty($serialNumbers) &&  count($serialNumbers) > 0){
            $results = DB::table('stock_items')->whereIn('serial_number',$serialNumbers)->get();
            if( count($results) > 0 ){
                return response(['message' => 'Serial Number ( '.$results[0]->serial_number.' ) already exist. ' ],422);
            }
            $data['serial_numbers'] = implode(",",$serialNumbers);
        }

        //stock_items
         $subCatID      = $request->get('sub_cat_id');
         $parentID      = optional(SubCategory::find($subCatID))->parentCategoryId;  // SubCategory::find($subCatID)->parentCategoryId;  // optional(SubCategory::find($subCatID))->parentCategoryId; 
         $productCatID  = $request->get('product_cat_id');
         $productID     = $request->get('product_id');
         
         $product = Product::find($productID);

         $uomID         = $product->uom_id;
         $uom = Uom::find($uomID);
        // $uomCode       = $request->get('uom_code');
         $qty           = $request->get('qty');
         $warranty_date = $request->get('warranty_date');
         $unit_price    = $request->get('unit_price');        
         $expiry_date   = $request->get('expiry_date');
 
      //   $data['stock_delivery_id'] = '0';
         $data['parent_cat_id'] = $parentID;
         $data['sub_cat_id'] = $subCatID;
         $data['product_cat_id'] = $productCatID;
         $data['product_id'] = $productID;
         $data['uom_id'] = $uomID;
         $data['shop_id'] =  $request->get('shop_id');
         $data['uom_code'] =  optional($uom)->code;

         $data['quantity'] = $qty;
         $data['unit_price'] = $unit_price;
         $data['warranty_date'] = $warranty_date;
         $data['expiry_date'] = $expiry_date;
         $data['created_by'] = auth()->user()->id;
         $data['status'] = 0;

         StockDeliveryProduct::create($data);
         
         return response()->json(['status' => true, 'message' => 'Product added successfully!']);
     }



     function destroy($eid){
        $emp = StockDeliveryProduct::find($eid);
        if($emp->status == 0){
            $emp->status = 1;
        }else{
            $emp->status = 0;
        }
        $emp->save();
        return array('status' => true);
     }


     function downloadSample(){
        $file= public_path(). "/download/serial_number_sample.csv";
        return response()->download($file, " _serial_number_sample.csv");
     }

     function readSerialNumber(Request $request){
        // $file= public_path(). "/download/serial_number_sample.csv";
       //  return response()->download($file, " _serial_number_sample.csv");
         $file = $request->file('serial_number_file');
        // // Create a CSV reader instance
        // $reader = Reader::createFromFileObject($file->openFile());
        //load the CSV document from a file path
        $csv = Reader::createFromPath($file, 'r');
        $csv->setHeaderOffset(0);

        $header = $csv->getHeader(); //returns the CSV header record
        $records = $csv->getRecords(); 
        //         //load the CSV document from a file path
        // $csv = Reader::createFromPath($file, 'r');
        // $csv->setHeaderOffset(0);

        // $header = $csv->getHeader(); //returns the CSV header record
        // $records = $csv->getRecords(); 

        $serialNumber = [];
        foreach($records as $rec){
            $serialNumber[] = $rec['serial-number']; //print_r($rec);
        }
         return $serialNumber;
     }


     function getStockItemByProductID($id){
        return StockItem::where('stock_delivery_product_id',$id)->get();
     }




     function purchaseReturnView($purchaseID){

        $purchase      = StockDelivery::findOrFail($purchaseID);
        $purchaseItem  = StockDeliveryProduct::with('product')->where('stock_delivery_id',$purchaseID)->get();
        $supplier      = Supplier::find($purchase->supplier_id);
        $view          = 'Stock.Delivery.purchase_return';
        $data = [
            'purchase' => $purchase,
            'purchaseItem' => $purchaseItem,
            'supplier' => $supplier,
        ];
        return view($view,$data);
    }


    function purchaseReturnSave(Request $request){
        // purchase_id
        $validator = Validator::make($request->all(), [
            'purchase_id'          => 'required',
            'order_item_id'     => 'required',
            'product_id'        => 'required',
            // 'order_item_price'  => 'required',
            'qty'               => 'required',
            'return_qty'        => 'required',
            'order_item_price'  => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()],406);
        }
       // dd($request->all());

        $purchaseID    = $request->get('purchase_id');
        $items      = $request->get('order_item_id');
        $returnsQty = $request->get('return_qty');
        $purchase      = StockDelivery::findOrFail($purchaseID);
      //  $shopID     = $purchase->shop_id; 

        $index = 0;
        foreach($items as $item){
            $returnQty = $returnsQty[$index];
                if( ($returnQty == 0)){
                    $index++;
                    continue;
                }
           //  dd($item, $returnQty);
        //    echo $item.'----'.$returnQty;
        //    echo '<br>';
          
            $saleOrder = StockDeliveryProduct::find($item);
            $saleOrder->quantity = $saleOrder->quantity - $returnQty;
            $saleOrder->save();
            $shopID = $saleOrder->shop_id;

            ProductAvailable::where(['shop_id' => $shopID, 'product_id' => $saleOrder->product_id])->decrement('qty', $returnQty);
            $index++;
        }

       // dd('-----');


        // $priceOffer = SaleOrderItem::where('order_id', $orderID)->sum('offer_price');
        // $priceOffer = SaleOrderItem::where('order_id', $orderID)->sum('offer_price');

        $purchaseItems          = StockDeliveryProduct::where('stock_delivery_id', $purchaseID)->get();
        $priceOfferTotal    = 0;
        $discountTotal      = 0;
        $priceTotal         = 0; 
        foreach($purchaseItems as $purchaseItem){
            $priceOfferTotal = $priceOfferTotal + ( $purchaseItem->quantity *  $purchaseItem->offer_price );
            $discountTotal   = $discountTotal + ( $purchaseItem->quantity *  $purchaseItem->discount );
            $priceTotal      = $priceTotal + ( $purchaseItem->quantity *  $purchaseItem->price );
        }
        $purchase->delivery_amount   = $purchase->delivery_amount -  $priceTotal;
        $purchase->save();

        return response()->json(['status' => true, 'message' => 'Done', 'url' => route('stocks.delivery.detail',$purchaseID) ],200);

    }



    
}
