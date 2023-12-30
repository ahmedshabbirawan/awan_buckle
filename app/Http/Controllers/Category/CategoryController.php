<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class CategoryController extends Controller{

    public $resource;
    public $name;
    public $adminURL;
    public $viewData;
    public $table;
    public $view;


    public function __construct(){
        $this->resource     = new Category();
        $this->name         = $this->viewData['name']         =     'Category';
        $this->adminURL     = $this->viewData['adminURL']     =     url('/category');
        $this->typeArr      = $this->viewData['typeArr']      =     ['cat' => 'Category', 'sub' => 'Sub Category', 'product' => 'Product Category'];
        $this->table        = 'categories';
        $this->view         = 'category.';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $this->viewData['listing_data']   = $this->resource->get();
        return view($this->view.'lists',$this->viewData);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){


        $this->viewData['title']        = 'Add Category';
        $this->viewData['type']        = 'product';
       
        $this->viewData['parentCat']   = Category::where('type','cat')->get()->pluck('name','id');
        return view($this->view.'create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){

        $validated = $request->validate([
            'name' => 'required',
            'cat_id' => 'required'
        ]);

        $catID      = $request->get('cat_id');
        $subCatID   = $request->get('sub_cat_id');
        $catName    = $request->get('name');
        $code       = Str::slug($catName, '-').'-'.rand(9,99);

        $parentID   = 0;
        $type       = 'cat';
        if($subCatID != ''){
            $parentID   = $subCatID;
            $type       = 'sub';
        }elseif($catID != ''){
            $parentID   = $catID;
            $type       = 'product';
        }

        $array = array(
            'name' => $catName,
            'code' => $code,
            'parent_id' => $parentID,
            'type' => $type,
            'status' => $request->get('status'),
        );
        $rec = $this->resource::create($array);
        return redirect($this->adminURL)->with('success',$this->name.' added successful!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

   

    public function editName($id, Category $category){
        $this->viewData['row']     = Category::find($id);
        return view($this->view.'edit_name',$this->viewData);
    }

    public function updateName(Request $request, $id){
        Validator::make($request->all(), [
            'name' => [ 'required'],
            'code' => [ 'required']
        ]);
        $array = array(
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'status' => $request->get('status'),
        );
        $this->resource::where('id', $id)->update($array);
        return redirect($this->adminURL)->with('success',$this->name.' updated successful!');
    }


     /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category){

//        dd($category->parentCategory()); exit;

       //  dd(Category::with(['parentCategory'])->where('id',$category->id)->get());

        // exit;
        

        // getAllParents($row->parent_id)
        $this->viewData['row'] = $category;
       //  $this->viewData['parents'] = $category->getAllParents($category->parent_id);
        $this->viewData['parents'] = $category->getAllParents($category->parent_id);
        $this->viewData['parentCat']   = Category::where('parent_id',0)->pluck('name','id');

       //  print_r($this->viewData['parentCat']); exit;
        return view($this->view.'edit',$this->viewData);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category){
        

        $validated = $request->validate([
            'name' => 'required',
            'cat_id' => 'required'
        ]);

        $catID      = $request->get('cat_id');
        $subCatID   = $request->get('sub_cat_id');
        $catName    = $request->get('name');
        $code       = Str::slug($catName, '-').'-'.rand(9,99);

        $parentID   = 0;
        $type       = 'cat';
        if($subCatID != ''){
            $parentID   = $subCatID;
            $type       = 'sub';
        }elseif($catID != ''){
            $parentID   = $catID;
            $type       = 'product';
        }

        $array = array(
            'name' => $catName,
            'code' => $code,
            'parent_id' => $parentID,
            'type' => $type,
            'status' => $request->get('status'),
        );

        print_r($array); exit;
        
        

        $this->resource::where('id', $category->id)->update($array);
        return redirect($this->adminURL)->with('success',$this->name.' updated successful!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category){
        $category->delete();
        return redirect($this->adminURL)->with('success',$this->name.' delete successful!'); 
    }



    /*** CUSTUM WORK ***/

    function get_child_category(Request $request){
        $parent_id  = $request->get('parent_id');
        $seleted_id = $request->get('selected_id');

        $this->viewData['categories']   =   Category::where('parent_id',$request->get('parent_id'))->get()->pluck('name','id');
        $this->viewData['parents']      =   [$seleted_id];  
        // Category::where('parent_id',$parent_id)->get()->getAllParents($parent_id);
        return view($this->view.'ajax_view.select_child_category',$this->viewData);
    }




}
