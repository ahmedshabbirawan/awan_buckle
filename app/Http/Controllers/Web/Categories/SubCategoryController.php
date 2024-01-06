<?php

namespace App\Http\Controllers\Web\Categories;

use App\Http\Controllers\Controller;
use App\Models\ParentCategory;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
class SubCategoryController extends Controller
{
    public function list(Request $request)
    {
        if ($request->ajax()) {
            // $data = User::with(['roles'])->whereHas("roles", function($q){ $q->whereNotIn("name", ["Super Admin"]);});
            $data = SubCategory::orderBy('id','desc')->with(['parentCategoryName']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $button = "";
                    $button .= '<td class="center">';
                    $button .= '<div class="hidden-sm hidden-xs btn-group">';
                        if($data->status == 1){
                            $button .= '<a href="'. route('category.sub.status', ['id' => $data->id]) .'">
                            <button class="btn btn-xs btn-warning">
                                <i class="ace-icon fa fa-ban bigger-120"></i>
                            </button>
                            </a>';
                        }else{

                            $button .= '<a href="'. route('category.sub.status', ['id' => $data->id]) .'">
                            <button class="btn btn-xs btn-success">
                                <i class="ace-icon fa fa-check bigger-120"></i>
                            </button>
                            </a>';
                        }

                        $button .=      '<a href="'. route('category.sub.edit', ['id' => $data->id]) .'">
                                        <button class="btn btn-xs btn-info">
                                            <i class="ace-icon fa fa-pencil bigger-120"></i>
                                        </button>
                                        </a>

                                        <a href="'. route('category.sub.destroy', ['id' => $data->id]) .'">
                                        <button class="btn btn-xs btn-danger">
                                            <i class="ace-icon fa fa-trash-o bigger-120"></i>
                                        </button>
                                        </a>



                                    </div>
                                </td>';
                                return $button;
                    // $role = Auth::user()->roles->pluck('name');
                    // if ($data->status == 0) {
                    //     $button = '<a class="fa fa-check" style="color:green;" href="' . route('php.post.status', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="top" title="Make active"></a> &nbsp';
                    // } elseif ($data->status == 1) {
                    //     $button = '<a class="fa fa-close" style="color:red;" href="' . route('php.post.status', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="top" title="Make InActive"></a> &nbsp';
                    // }
                    // $button .= '<a class="fa fa-edit" style="cursor:pointer;" onclick="editfunction(' . $data->id . ')" data-toggle="tooltip" data-placement="top" title="Edit"></a> &nbsp';
                    // if ($role[0] == "Super Admin") {
                    //     $button .= '<a onclick="return confirm(\'Are you sure? You want to delete the PHP Post\')" class="fa fa-trash" href="' . route('php.post.delete', ['id' => $data->id]) . '" data-placement="top" title="Delete"></a> &nbsp';
                    // }
                    // return $button;
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
                ->rawColumns(['action', 'status','parentCategoryName'])
                ->make(true);
        }

        return view('Categories.SubCategory.list');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $ParentCategories = ParentCategory::orderBy('name','asc')->get();
        return view('Categories.SubCategory.create',compact('ParentCategories'));
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
            $obj = new SubCategory();
            $obj->name = $request->name;
            $obj->parentCategoryId = $request->parentCategoryName;
            $obj->save();
            DB::commit();
            return redirect()->route('category.sub.list')->With('success','Sub Category Added Successfully');
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->route('category.sub.list')->With('error','Error.Please Contact   Support');


        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $obj = SubCategory::where('id',$id)->first();
        $ParentCategories = ParentCategory::orderBy('name','asc')->get();
        return view('Categories.SubCategory.edit',compact('obj','ParentCategories'));
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

            $obj = SubCategory::where('id',$id)->first();
            $obj->name = $request->name;
            $obj->parentCategoryId = $request->parentCategoryName;
            $obj->save();
            DB::commit();
            return redirect()->route('category.sub.list')->with('success','Sub Category Edited Successfully');

        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->route('category.sub.list')->with('error','Error.Please Contact   Support');
        }

    }

    public function status( $id)
    {
        try{
            DB::beginTransaction();

            $obj = SubCategory::where('id',$id)->first();

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
            return redirect()->route('category.sub.list')->with('success','Status has been '.$status_string.' Successfully');


        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->route('category.sub.list')->with('error','Error.Please Contact   Support');


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

    }


   public function getSubCategoryByParentId(Request $request){

    $obj = SubCategory::where('parentCategoryId', $request->parentID)->orderBy('id','asc')->get();
    return $obj;

   }
}
