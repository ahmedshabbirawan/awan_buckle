<?php

namespace App\Http\Controllers\Web\Categories;

use App\Http\Controllers\Controller;
use App\Models\AttributeType;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\ParentCategory;
use App\Models\ProductCategoryAttribute;
use Illuminate\Support\Facades\DB;

class ProductCategoryController extends Controller
{
    public function list(Request $request)
    {
        // $data = ProductCategory::orderBy('id','desc')->with(['parentCategoryName','parentSubCategoryName','ProductCategoryAttributes','ProductCategoryAttributes.ProductCategoryAttributeName'])->get();
        // dd($data);
        if ($request->ajax()) {
            // $data = User::with(['roles'])->whereHas("roles", function($q){ $q->whereNotIn("name", ["Super Admin"]);});
            $data = ProductCategory::orderBy('id','desc')->with(['parentCategoryName','parentSubCategoryName','ProductCategoryAttributes','ProductCategoryAttributes.ProductCategoryAttributeName']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $button = "";
                    $button .= '<td class="center">';
                    $button .= '<div class="hidden-sm hidden-xs btn-group">';
                        if($data->status == 1){
                            $button .= '<a href="'. route('category.product.status', ['id' => $data->id]) .'">
                            <button class="btn btn-xs btn-warning">
                                <i class="ace-icon fa fa-ban bigger-120"></i>
                            </button>
                            </a>';
                        }else{

                            $button .= '<a href="'. route('category.product.status', ['id' => $data->id]) .'">
                            <button class="btn btn-xs btn-success">
                                <i class="ace-icon fa fa-check bigger-120"></i>
                            </button>
                            </a>';
                        }

                        $button .=      '<a href="'. route('category.product.edit', ['id' => $data->id]) .'">
                                        <button class="btn btn-xs btn-info">
                                            <i class="ace-icon fa fa-pencil bigger-120"></i>
                                        </button>
                                        </a>

                                        <a href="'. route('category.product.destroy', ['id' => $data->id]) .'">
                                        <button class="btn btn-xs btn-danger">
                                            <i class="ace-icon fa fa-trash-o bigger-120"></i>
                                        </button>
                                        </a>
                                    </div>
                                </td>';
                            return $button;
                })
                ->addColumn('status', function ($data) {

                    if ($data->status == 1) {
                        $status = "<div style='color:green;'>Active</div>";
                    } elseif ($data->status == 0) {
                        $status = "<div style='color:red;'>InActive</div>";
                    } elseif ($data->status == 2) {
                        $status = "<div style='color:pink;'>Disabled</div>";
                    }
                    return $status;
                })
                ->addColumn('parentCategoryName', function ($data) {

                   $parentName = $data->parentCategoryName->name;
                    return $parentName;
                })
                ->addColumn('SubCategoryName', function ($data) {

                    $SubName = $data->parentSubCategoryName->name;
                     return $SubName;
                 })
                 ->addColumn('CategoryAttributes', function ($data) {

                    $attributes="";
                    foreach($data->ProductCategoryAttributes as $singleAttributes){

                         $attributes.= ' * '.$singleAttributes->ProductCategoryAttributeName->name.'<br>';

                    }
                            return $attributes;

                 })
                ->rawColumns(['action', 'status','parentCategoryName','SubCategoryName','CategoryAttributes'])
                ->make(true);
        }

        return view('Categories.ProductCategory.list');


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $ParentCategories = ParentCategory::orderBy('name','asc')->get();
        $attributeTypes = AttributeType::where('status',1)->get();
        return view('Categories.ProductCategory.create',compact('ParentCategories','attributeTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        try{
            DB::beginTransaction();
            $productCategory = new ProductCategory();
            $productCategory->parentCategoryId = $request->parentCategoryName;
            $productCategory->subCategoryId = $request->SubCategoryName;
            $productCategory->name = $request->name;
            $productCategory->enable_alert = $request->enable_alert;
            $productCategory->sn_require = $request->sn_require;
            $productCategory->save();



            if($productCategory->id && ($request->duallistbox_demo1) && ( count($request->duallistbox_demo1) > 0) ){
                for($i=0; $i<count($request->duallistbox_demo1);$i++){
                    $categoryAttribute = new ProductCategoryAttribute();
                    $categoryAttribute->productCategoryId = $productCategory->id;
                    $categoryAttribute->attributeName =$request->duallistbox_demo1[$i];
                    $categoryAttribute->save();
                }
            }
            DB::commit();
            return redirect()->route('category.product.list')->with('success','Product Category has been added');
        }catch(\Exception $e){
            DB::rollBack();
            
            return redirect()->route('category.product.create')->with('error','Error. '.$e->getMessage().' And Please Contact   Support');
        }

    }


    public function edit($id)
    {
        $productCategory =  ProductCategory::where('id',$id)->first();
        $productCategoryAttributesArray = ProductCategoryAttribute::where('productCategoryId',$productCategory->id)->pluck('attributeName')->toArray();
        $productCategoryAttributes = AttributeType::all();

        // dd($productCategoryAttributes);
        $ParentCategories = ParentCategory::orderBy('name','asc')->get();
        // dd($productCategory);
        return view('Categories.ProductCategory.edit',compact('ParentCategories','productCategory','productCategoryAttributes','productCategoryAttributesArray'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            DB::beginTransaction();
            $productCategory = ProductCategory::where('id',$id)->first();
            $productCategory->parentCategoryId = $request->parentCategoryName;
            $productCategory->subCategoryId = $request->SubCategoryName;
            $productCategory->name = $request->name;

            $productCategory->enable_alert = $request->enable_alert;
            $productCategory->sn_require = $request->sn_require;


            $productCategory->save();
            if($productCategory->id){
                $categoryAttributeDelete = ProductCategoryAttribute::where('productCategoryId',$productCategory->id)->get();
                // $categoryAttributeDelete->delete();
                $categoryAttributeDelete->map->delete();
                if( !empty($request->duallistbox_demo1) ){
                for($i=0; $i<count($request->duallistbox_demo1);$i++){
                    $categoryAttribute = new ProductCategoryAttribute();
                    $categoryAttribute->productCategoryId = $productCategory->id;
                    $categoryAttribute->attributeName =$request->duallistbox_demo1[$i];
                    $categoryAttribute->save();
                }
                }
            }
            DB::commit();
            return redirect()->route('category.product.list')->with('success','Product Category has been Updated');
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->route('category.product.list')->with('error','Error.Please Contact   Support '.$e->getMessage());
        }


    }

    public function status( $id)
    {
        try{
            DB::beginTransaction();
            $obj = ProductCategory::where('id',$id)->first();

            if($obj->status==1){
                $status_string = "InActived";
                $obj->status = 0;
                $obj->save();

            }else{
                $obj->status = 1;
                $status_string = "Actived";
                $obj->save();
            }
            DB::commit();
            return redirect()->route('category.product.list')->with('success','Status has been '.$status_string.' Successfully');


        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->route('category.product.list')->with('error','Error.Please Contact   Support');
        }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {


       return redirect()->back();

       //  $res = StockItem::where('product_id', $id)->get();


        //  try{
        //     DB::beginTransaction();

        //     $productCategory = ProductCategory::where('id',$id)->first()->delete();
        //     $categoryAttributeDelete = ProductCategoryAttribute::where('productCategoryId',$productCategory->id)->get();
        //     $categoryAttributeDelete->map->delete;
        //     DB::commit();

        // }catch(\Exception $e){
        //     DB::rollBack();


        // }

    }
}
