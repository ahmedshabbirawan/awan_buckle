<?php

namespace App\Http\Controllers\Web\Product;


use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

use App\Models\Uom;
use App\Models\ProductAvailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\StockDelivery;
use App\Models\StockDeliveryProduct;



use League\Csv\Reader;
use SebastianBergmann\CodeCoverage\Report\Xml\Unit;

class ProductImportExportController extends Controller
{
    /* THIS CONTROLLER FOR IMPORT AND EXPORT DATA IT WILL BE USE IN FUTURE */
    public $resource;
    public $name;
    public $adminURL;
    public $viewData;
    public $table;
    public $view;

    public function __construct(){
        $this->resource     = new Product();
        $this->name         = $this->viewData['name']         =   'Product';
        $this->adminURL     = $this->viewData['adminURL']     =   url('/product');
        $this->table        = 'products';
        $this->view         = 'product.simple.';
    }


    
    function friday_work(Request $request){
        $validator = Validator::make($request->all(), [
            'product_list_file'   => 'required'
        ]);


        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()],406);
        }

        $fileNameWithExt = $request->file('product_list_file')->getClientOriginalName();
        $extension       = $request->file('product_list_file')->getClientOriginalExtension();
        $fileNameToStore = "product-csv".'-'.time().'.'.$extension;
        $path            = $request->file('product_list_file')->storeAs('public/product_images', $fileNameToStore);
        $path            = Storage::path('public/product_images/'.$fileNameToStore);
        




        //load the CSV document from a file path
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        $header = $csv->getHeader(); //returns the CSV header record

        //returns all the records as
        $records = $csv->getRecords(); // an Iterator object containing arrays


        try{
         DB::beginTransaction();

        $insertCount = 0;
        $updateCount = 0;
        $index = 1; 

         $purchaseData['purchased_date'] = date('Y-m-d');
         $purchaseData['delivery_challan_no'] = 'First Purchase';
         $purchaseData['supplier_id'] = 1;


         $row                = StockDelivery::create($purchaseData);
         $stockDeliveryID    = $row->id;
         


        foreach($records as $product){
            $index++;
            $name   = $product['product_name'];
            $desc   = (isset($product['product_description']))? $product['product_description'] : '';
            $costPrice  = (isset($product['cost_price']))? $product['cost_price'] : 0;
            $salePrice  = (isset($product['sale_price']))? $product['sale_price'] : 0;

            $shop_1    = (isset($product['shop_1']))? $product['shop_1'] : 0;
            $shop_1    = intval(trim($shop_1));

            $shop_2    = (isset($product['shop_2']))? $product['shop_2'] : 0;
            $shop_2    = intval(trim($shop_2));


            $productObj = Product::where('name',$name)->first();
            $unitObj = Uom::find(12);
          

            $productArr = [
                'name' => $name,
                'uom_id' => $unitObj->id,
                'code' => $unitObj->code,
                'description' => $desc,
                'price' => $salePrice,
                'cost_price' => $costPrice
            ];
            

            if($productObj){
                $productObj->update($productArr);
                $updateCount++;
            }else{
                $productObj = Product::create($productArr);
                $insertCount++;
            }


            /////////////////// Purchase //////////////////////
             StockDeliveryProduct::create([
                 'shop_id' => 1,
                 'stock_delivery_id' => $stockDeliveryID,
                 'product_id' => $productObj->id,
                 'quantity' => $shop_1,
                 'uom_id' => 12,
                 'uom_code' => 'pair',
                 'unit_price' => $costPrice
             ]);
             StockDeliveryProduct::create([
                 'shop_id' => 2,
                 'stock_delivery_id' => $stockDeliveryID,
                 'product_id' => $productObj->id,
                 'quantity' => $shop_2,
                 'uom_id' => 12,
                 'uom_code' => 'pair',
                 'unit_price' => $costPrice
             ]);



             ProductAvailable::create([
                 'shop_id' => 1,
                 'product_id' => $productObj->id,
                 'qty' => $shop_1
             ]);
             
             ProductAvailable::create([
                 'shop_id' => 2,
                 'product_id' => $productObj->id,
                 'qty' => $shop_2
             ]); 
        }


        DB::commit();
        return response(['status' => true, 'message' => $insertCount. ' new Product added and '.$updateCount.' products updated' ]);
     }catch(\Exception $e){
         DB::rollBack();
         return response()->json(['message' => 'Error : '.$e->getLine().' -- '. $e->getMessage(). '. Please Contact to Developer'], 500);
     }
    } // FUNC END 


    function importProductsSave_cost(Request $request){

        $validator = Validator::make($request->all(), [
            'product_list_file'   => 'required'
        ]);


       if ($validator->fails()) {
           return response()->json(['status' => false, 'message' => $validator->errors()->first()],406);
       }

       $fileNameWithExt = $request->file('product_list_file')->getClientOriginalName();
       $extension       = $request->file('product_list_file')->getClientOriginalExtension();
       $fileNameToStore = "product-csv".'-'.time().'.'.$extension;
       $path            = $request->file('product_list_file')->storeAs('public/product_images', $fileNameToStore);
       $path            = Storage::path('public/product_images/'.$fileNameToStore);
       
       //load the CSV document from a file path
       $csv = Reader::createFromPath($path, 'r');
       $csv->setHeaderOffset(0);

       $header = $csv->getHeader(); //returns the CSV header record

       //returns all the records as
       $records = $csv->getRecords(); // an Iterator object containing arrays


       $unitArr = array();
       $shopID = $request->get('shop_id');

       $insertCount = 0;
       $updateCount = 0;
       $index = 1; 
       foreach($records as $product){
           $index++;
           $name       = $product['product_name'];
           $costPrice  = (isset($product['cost_price']))? $product['cost_price'] : 0;
           $salePrice  = (isset($product['sale_price']))? $product['sale_price'] : 0;
           $product = Product::where('name',$name)->first();
           if($product){
            $product->price = $salePrice;
            $product->cost_price = $costPrice;
            $product->save();
           }
       }
       return response(['status' => true, 'message' => $insertCount. ' new Product added and '.$updateCount.' products updated' ]);
   }



   function create_first_purchase(){
        
    try{
        DB::beginTransaction();
        $data['purchased_date'] = date('Y-m-d');
        $data['delivery_challan_no'] = 'First Purchase';
        $data['supplier_id'] = 1;


        $row                = StockDelivery::create($data);
        $stockDeliveryID    = $row->id;
        $time               = time();
        $products = Product::get();
        foreach($products as $product){
            $productID      = $product->id;
            $productsAvail  = ProductAvailable::where('product_id',$productID)->get();


            if($productsAvail){
            foreach($productsAvail as $proAvail){
                if(isset($proAvail)){
                StockDeliveryProduct::create([
                    'shop_id' => $proAvail->shop_id,
                    'stock_delivery_id' => $stockDeliveryID,
                    'product_id' => $productID,
                    'quantity' => $proAvail->qty,
                    'uom_id' => 12,
                    'uom_code' => 'pair'
                ]);
                }
            }
            }
        }
        DB::commit();
        
    }catch(\Exception $e){
        DB::rollBack();
        return response()->json(['message' => 'Error : '. $e->getMessage(). '. Please Contact to Developer'], 500);
    }
}


function importProductsSave(Request $request){
       $validator = Validator::make($request->all(), [
          //  'shop_id' => 'required',
           'product_list_file'   => 'required'
       ]);


       if ($validator->fails()) {
           return response()->json(['status' => false, 'message' => $validator->errors()->first()],406);
       }

       $fileNameWithExt = $request->file('product_list_file')->getClientOriginalName();
       $extension       = $request->file('product_list_file')->getClientOriginalExtension();
       $fileNameToStore = "product-csv".'-'.time().'.'.$extension;
       $path            = $request->file('product_list_file')->storeAs('public/product_images', $fileNameToStore);
       $path            = Storage::path('public/product_images/'.$fileNameToStore);
       




       //load the CSV document from a file path
       $csv = Reader::createFromPath($path, 'r');
       $csv->setHeaderOffset(0);

       $header = $csv->getHeader(); //returns the CSV header record

       //returns all the records as
       $records = $csv->getRecords(); // an Iterator object containing arrays


       $unitArr = array();
       $shopID = $request->get('shop_id');

       $insertCount = 0;
       $updateCount = 0;
       $index = 1; 
       foreach($records as $product){
           $index++;
           $name   = $product['product_name'];
           $desc   = $product['product_description'];
           $price  = (isset($product['product_price']))? $product['product_price'] : 0;
           $qty    = (isset($product['product_qty']))? $product['product_qty'] : 0;
           $qty    = intval(trim($qty));
           $unit   = ucfirst($product['product_unit']);

           // if( strtolower(substr($name ,0,2))  == 'aw'){
           //     $name =  substr($name,2);
           // }

           $product = Product::where('name',$name)->first();

           $unitObj = null;
           if( isset($unitArr[$unit]) ){
               $unitObj = $unitArr[$unit];
           }else{
               
               if( trim($unit) == '' ){
                   continue;
                   return response(['status' => false, 'message' => 'Empty Product Unit is given at index'.$index ]);
               }

               $unitObj = Uom::where('name',$unit)->first();

               if( !($unitObj) ){
                   continue;
                   return response(['status' => false, 'message' => 'No Pair found at index'.$index ]);
               }

               if($unitObj){
                   $unitArr[$unitObj->name] = $unitObj; // ['id' => $unitObj->id, 'code' => $unitObj->code];
               }else{
                   $unitObj =    Uom::create([
                       'name' => $unit,
                       'code' => $unit
                   ]);
                   $unitArr[$unitObj->name] = $unitArr;
               }
           }

           $productArr = [
               'name' => $name,
               'uom_id' => $unitObj->id,
               'code' => $unitObj->code,
               'description' => $desc
           ];
           if($price){
               $productArr['price'] = $price;
           }

           if($product){
               $product->update($productArr);
               $updateCount++;
           }else{
               $product = Product::create($productArr);
               $insertCount++;
           }

           if($qty){
               $qty = (int) $qty;
               $productAvail = ProductAvailable::where([
                   'shop_id' => $shopID,
                   'product_id' => $product->id
               ])->first();
   
               if($productAvail){
                   $productAvail->qty = $qty;
                   $productAvail->save();
               }else{
                   // ProductAvailable::create([
                   //     'shop_id' => $shopID,
                   //     'product_id' => $product->id,
                   //     'qty' => $qty
                   // ]);   
               }
           }
           
       }

       return response(['status' => true, 'message' => $insertCount. ' new Product added and '.$updateCount.' products updated' ]);



   }


    




}
