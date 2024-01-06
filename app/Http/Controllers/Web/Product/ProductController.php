<?php

namespace App\Http\Controllers\Web\Product;


use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ParentCategory;

use App\Models\Uom;
use App\Models\SubCategory;
use Yajra\DataTables\DataTables;
use App\Http\Requests\ProductFromRequest;
use App\Models\ProductCategory;
use App\Models\ProductCategoryAttribute;

use App\Models\ProductAttribute;
use App\Models\ProductAvailable;
use Illuminate\Support\Str;

use App\Models\Attribute;
use App\Models\AttributeType;
use App\Models\StockItem;

use App\Events\ProductCreate;
use App\Events\ProductEvent;
use App\Models\Shop;
use Exception;
use Illuminate\Support\Facades\DB;
use Nette\Utils\Image;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
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
        $this->view         = 'product.';
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        $shops = Shop::where('status',1)->get();
        if($request->ajax()){

            $order      = $request->get('order_by','name-asc');
            $column     = explode('-',$order)[0];
            $sort       = explode('-',$order)[1];

            $results = Product::with(['product_Category' => function($query) use ($request){}]
            )->select()->where(function ($query) use ($request) {

            })->orderByRaw($column.' '.$sort)->get();
            
            
            $datatable =  Datatables::of($results)
            ->addColumn('product_name', function ($row) {
                    $attributes = array();
                    foreach($row->product_attribute_value as $attr):
                        $attributes[] = $attr->attribute_type->name.' : '.$attr->value;
                    endforeach;
                    if( count($attributes) > 0 ){
                        return '<b>'.$row->name.'</b> <br> <span class="label label-sm label-warning"> '. implode(' / ', $attributes).' </span>';
                    }else{
                        return '<b>'.$row->name.'</b>';
                    }
            
            })->addColumn('stock_available', function ($row) {
                $qty = ProductAvailable::select(DB::raw('SUM(qty) as total_qty'))->where('product_id', $row->id)->first()->total_qty;
                return ($qty)?$qty:0;
            })->addColumn('product_category_name', function ($row) {
                     $tree = $row->categories_tree();
                    return  $tree['category']['name'].' / '.$tree['sub_category']['name'].' / '.$tree['product_category']['name']; 
            })->addColumn('attributes_tags', function ($row) {
                return '';
            })->addColumn('action', function ($row) {
               //  return $row;
                    $statusIcon = 'fa fa-ban';
                    if($row->status == 1){
                    $statusIcon = 'fa fa-check-circle-o';
                    }
                     $action = '<div class="hidden-sm hidden-xs btn-group">';
                     $action .= '<a href="'.route('product.view', $row->id).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
                     $action .= '<a href="'.route('simple_product.edit', $row->id).'" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Edit" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';
                     $action .= '<a href="javascript:void(0);" onclick="changeStatus('.$row->id.');" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Change Status" ><i class="ace-icon '.$statusIcon.' bigger-120"></i></a>';
                    //  $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                     $action .= '</div>';
                    return $action;
            })->rawColumns(['status_label','action','product_category_name','product_name']);

            foreach($shops as $shop):
                $datatable->addColumn('shop_'.$shop->id, function($row) use ($shop) {
                    $qty = ProductAvailable::select(DB::raw('SUM(qty) as total_qty'))
                            ->where('product_id', $row->id)
                            ->where('shop_id', $shop->id)
                            ->first()->total_qty;
                    return ($qty)?$qty:0;
                });
                // $dt->addColumn($request->field, 'content here');
            endforeach;

            //  ->orderColumn('id', 'DESC')
            return $datatable->make(true);
    }


        $this->viewData['shops']        = $shops;
        $this->viewData['uoms']         = Uom::orderBy('code','ASC')->where('status',1)->get()->pluck('code','id');
        $this->viewData['parentCat']    = ParentCategory::all()->pluck('name','id');
        return view($this->view.'lists',$this->viewData);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        dd('stop');

        // $product = Product::findOrFail(2);
        // ProductCreate::dispatch($product);

        $this->viewData['sub_category']     = ParentCategory::with('subCategories')->orderBy('name','DESC')->get();
        $this->viewData['uoms']             = Uom::orderBy('code','ASC')->get()->pluck('code','id');
        return view($this->view.'create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductFromRequest $request){
        dd('stop');
        $name           = $request->get('name');
        $description    = $request->get('description');
        $uom_id         = $request->get('uom_id');
        $status         = $request->get('status');
        $price          = $request->get('price');
        $imageName      = null;
        
        if($request->hasFile('product_image')){ 
            $imageObj = $this->uploadProductImage($request);
            if(  !($imageObj[0]) ){
                return response()->json(['message' => $imageObj[1]], 500);
            }else{
                $imageName = $imageObj[1];
            }
        }

        $array = array(
            'name'           => $name,
            'description'    => $description,
            'uom_id'         => $uom_id,
            'code'           => Uom::find($uom_id)->code,
            'image'          => $imageName,
            'price'          => $price,
            'product_cat_id' => $request->get('product_category_id'),
            'status'         => $status,
        );


        

        try{
            DB::beginTransaction();
            $attributes = $request->get('attribute_value_ids');
            $product    = Product::create($array);
            $insertArr = [];

            if(!empty($attributes ) && (count($attributes) > 0) ){
                foreach($attributes as $valueID):
                    ProductAttribute::create(array('product_id' => $product->id, 'value_id' => $valueID));
                endforeach;
            }   

            

            DB::commit();

            if($request->ajax()){
                return response(['status' => true, 'data' => ['product' => $product], 'message' => 'Product Add successfully.' ]);
            }else{
                return redirect(route('product.list'))->with('success',$this->name.' added successful!');
            }
            
            
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['message' => 'Error.Please Contact   Support'. $e->getMessage()], 500);
            // return redirect()->route('category.sub.list')->with('error','Error.Please Contact   Support');
        }

        


       // return redirect(route('product.list'))->with('success',$this->name.' added successful!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product, $id){
        $product = Product::find($id);
        $this->viewData['row'] = $product;
        $this->viewData['categories']   = $product->categories_tree();
        return view($this->view.'detail_new',$this->viewData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product,$id){
        dd('stop');
        $product = Product::find($id);



        $this->viewData['product']      = Product::find($id);
        $this->viewData['categories']   = $product->categories_tree();
        $this->viewData['sub_category']     = ParentCategory::with('subCategories')->orderBy('name','DESC')->get();
        // $this->viewData['parentCat']    = ParentCategory::all()->pluck('name','id');
        $this->viewData['attributeIDs']    = ProductAttribute::where('product_id',$id)->pluck('value_id')->toArray();
        $this->viewData['uoms']         = Uom::orderBy('code','ASC')->get()->pluck('code','id');
        return view($this->view.'edit',$this->viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product){
        dd('stop');
        $productID = $request->get('id');
        $name           = $request->get('name');
        $description    = $request->get('description');
        $uom_id         = $request->get('uom_id');
        $qty            = $request->get('threshold_qty');
        $status         = $request->get('status');

        $imageName = null;
        $price          = $request->get('price');

        if($request->hasFile('product_image')){ 
            $imageObj = $this->uploadProductImage($request);
            if(  !($imageObj[0]) ){
                return response()->json(['message' => $imageObj[1]], 500);
            }else{
                $imageName = $imageObj[1];
            }
        }

        $array = array(
            'name'          => $name,
            'description'   => $description,
            'uom_id'        => $uom_id,
            'code'        => Uom::find($uom_id)->code,
            'threshold_qty' => $qty,
            'image'          => $imageName,
            'price'          => $price,
            'product_cat_id' => $request->get('product_category_id'),
            'status'        => $status,
        );


   //     print_r($array); exit;

       try{
            DB::beginTransaction();
            $attributes         = $request->get('attribute_value_ids');
            Product::where('id',$productID)->update($array);
            ProductAttribute::where('product_id',$productID)->delete();
            $insertArr          = [];
            if(is_array($attributes)){
                foreach($attributes as $valueID):
                    ProductAttribute::create(array('product_id' => $productID, 'value_id' => $valueID));
                endforeach;
            }
            DB::commit();
            if($request->ajax()){
                return response(['status' => true, 'data' => ['product' => $product], 'message' => 'Product Update successfully.' ]);
            }else{
                return redirect(route('product.list'))->with('success',$this->name.' added successful!');
            }
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['message' => 'Error.Please Contact   Support'], 500);
            // return redirect()->route('category.sub.list')->with('error','Error.Please Contact   Support');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,Product $product){
        dd('destory stop');
        $res = StockItem::where('product_id', $id)->get();
        if( count($res) > 0){
            return redirect(route('product.list'))->with('error','This product have stock item. First delete them');
        }else{
            Product::find($id)->delete();
            return redirect(route('product.list'))->with('success','Product updated successful!');
        }
    }



    function getSubCategories(Request $request){
        
        $parentID = $request->get('parent_id');

        $data['selected_id']    = $request->get('selected_id');
        $data['subCategories']  = SubCategory::where('parentCategoryId',$parentID)->pluck('name','id');
        

        return view('product.ajax_view.sub_categories',$data);
    }


    function getProductCategories(Request $request){
        $subCatID = $request->get('sub_cat_id');

        $data['selected_id']    = $request->get('selected_id');
        $data['subCategories']  = ProductCategory::where('subCategoryId', $subCatID)->get()->pluck('name','id');

        return view('product.ajax_view.sub_categories',$data);
    }

    function getProductAttribute(Request $request){
        $productCatID           = $request->get('product_cat_id');
        $data['selected_id']    = explode(',',$request->get('selected_id'));

      //   print_r($data['selected_id']); exit;

        // $data['subCategories']  = ProductCategory::where('subCategoryId', $subCatID)->get()->pluck('name','id');
        // $productCategory =  ProductCategory::where('id',$id)->first();
        // $productCategoryAttributesArray = ProductCategoryAttribute::where('productCategoryId',$productCatID->id)->pluck('attributeName')->toArray();
        // $productCategoryAttributes = AttributeType::all();


        $productCategoryAttributesArray     = ProductCategoryAttribute::with(['ProductCategoryAttributeName'])->where('productCategoryId',$productCatID)->pluck('attributeName')->toArray();
        $data['attributes']                 = AttributeType::with(['attributeValue'])->whereIn('id',$productCategoryAttributesArray)->get(); //->toArray();
        // dd($data['attributes']); exit;
        return view('product.ajax_view.product_cat_attribute',$data);
    }

    function getProductByProductCategory(Request $request){
        $productCatID       = $request->get('product_cat_id');
        if($productCatID == 0){
            $where = [];
        }else{
            $where['product_cat_id']              = $productCatID;
        }
        $data['products']   = Product::with(['product_Attribute_Value', 'product_Attribute_Value.attribute_type'])->where($where)->orderBy('name','ASC')->get();
        return view('product.ajax_view.product_with_attribute_select',$data);
    }

    function getProductCategoryDetail(Request $request){
        return ProductCategory::find($request->get('id'));
    }


    function uploadProductImage(Request $request){
        $productName = Str::slug($request->get('name'));
        if($request->hasFile('product_image')){
            try{
                //Getting file name with extension
                $fileNameWithExt = $request->file('product_image')->getClientOriginalName();
                //Get just file name
                // $filename        = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
                //Get just ext
                $extension       = $request->file('product_image')->getClientOriginalExtension();
                //Filename to store
                $fileNameToStore = "product-".$productName.'-'.time().'.'.$extension;
                //Upload Image
                $path = $request->file('product_image')->storeAs('public/product_images', $fileNameToStore);


                /*******   CREATE TUMBNAIL   *******/
                
               //  echo Storage::get($path); exit;

                $path = Storage::path('public/product_images/'.$fileNameToStore);

                // $publicPath = "public\product_images\"".$fileNameToStore;

                $imgFile = Image::fromFile($path);
                $imgFile->resize(150, 150, Image::EXACT );
                $imgFile->cropAuto()->save($path);
                // $imgFile->resize(150, 150, function ($constraint) {
                //     // $constraint->aspectRatio();
                //     $constraint->cropAuto();
                // })->save($path);
                // $destinationPath = public_path('/uploads');
                //$image->move($destinationPath, $input['file']);
                /**************/

                return [true,$fileNameToStore];
            }catch(Exception $e){
                return [false,$e->getMessage()];
            }
        }
        return [false,'Image not found!'];
    }


    function status($eid){
        $emp = Product::find($eid);
        if($emp->status == 0){
            $emp->status = 1;
        }else{
            $emp->status = 0;
        }
        $emp->save();
        return array('status' => true);
    }


    /*
    SET @runtot:=0;
    SELECT 
    q1.pro,
    q1.avail_qty,
    (@runtot := @runtot + q1.avail_qty) AS rt
 FROM 
    (SELECT product_id AS pro,
     available_qty AS avail_qty
     FROM  stock_items 
     WHERE available_qty > 0
     ORDER  BY available_qty
     ) AS q1
WHERE @runtot + q1.avail_qty <= 200;
    */

    /*
        SET @runtot:=0;
    SELECT 
    q1.pro,
    q1.avail_qty,
    (@runtot := @runtot + q1.avail_qty) AS rt
 FROM 
    (SELECT product_id AS pro,
     available_qty AS avail_qty
     FROM  stock_items 
     WHERE available_qty > 0
     ORDER  BY available_qty
     ) AS q1
WHERE @runtot + q1.avail_qty < 130 ORDER BY rt DESC;
    */


    /*
    
     //->whereHas('product_Attribute_Value', function ($query) use ($request){
                // $query->select(DB::raw("GROUP_CONCAT(value_id SEPARATOR ',') as attributes, product_id"))
                // ->groupBy('product_id')->havingRaw(" 1= 1 AND attributes like '%16%' AND attributes like '%13%'");

                /*
                $attr = $request->get('attribute_idz');
                $filterArr = [];
                $str = ' 1 = 1 ';
                if( is_array($attr) ){
                //  $idFound = 0;
                
                    foreach($attr as $attrID){
                        if($attrID == ''){ continue; }
                        //  $filterArr['value_id'] = $attrID;
                        //  $str .= ' AND value_id = '.$attrID;

                        //  echo 'as'; exit;
                        $filterArr[] = $attrID; 
                    }

                    if( count($filterArr) > 0 ){
                        $query->whereIn('product_attributes.value_id',$filterArr);
                        $query->groupBy('value_id3');
                        $idz = implode(",",$filterArr);
                    }
                    
                }
                

            // })
    
    */

    /*
     // productAvailable_sum_qty
                // $productCat = $request->get('product_category_id');
                // if($productCat != ''){
                //     $query->where('product_cat_id', '=', $request->product_category_id);
                // }
    */

}
