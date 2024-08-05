<?php
namespace App\Http\Controllers\ProductVariant;

use App\Http\Controllers\Controller;
use App\Models\AttributeValue;
use App\Models\AttributeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class VariantController extends Controller{





    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        // echo $request->route('type'); exit;
        //  dd($request->route()); exit;
        $slug       = strtolower($request->route('slug'));

        $typeObj    = AttributeType::where('slug',$slug)->firstOrFail();
        $name       = $typeObj->name;
        $typeID     = $typeObj->id;



        if($request->ajax()){
            $results = AttributeValue::where('type_id',$typeID)->select()->orderBy('id','ASC');
        return Datatables::of($results)
        ->addColumn('action', function ($row) use ($slug) {
            $statusIcon = 'fa fa-ban';
            if($row->status == 1){
                $statusIcon = 'fa fa-check-circle-o';
            }
            $action = '<div class="hidden-sm hidden-xs btn-group">';
           // $action .= '<a href="'.route('project.view', $row->id).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
            $action .= '<a href="'.url('attribute/'.$slug.'/edit', $row->id).'" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Edit" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';
            $action .= '<a href="javascript:void(0);" onclick="changeStatus('.$row->id.');" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Change Status" ><i class="ace-icon '.$statusIcon.' bigger-120"></i></a>';
            $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';

            $action .= '</div>';
            return $action;
        })->rawColumns(['status_label','action'])
         //  ->orderColumn('id', 'DESC')
          ->make(true);
        }
        return view('DynamicAttribute.lists',['typeID' => $typeID, 'name' => $name, 'slug' => $slug]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request){

        $slug       = strtolower($request->route('slug'));

        $typeObj    = AttributeType::where('slug',$slug)->firstOrFail();
        $name       = $typeObj->name;
        $typeID     = $typeObj->id;


        return view('DynamicAttribute.create',['typeID' => $typeID, 'name' => $name, 'slug' => $slug]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request){
        $slug       = strtolower($request->route('slug'));

        $type_id    = $request->get('type_id');


        $rules = ['type_id' => 'required',  'value' => [
            'required', Rule::unique('attribute_values')->where(function($query) use ($type_id) {
                    $query->where('type_id', '=', $type_id);
            })
        ]];

        $validated  = $request->validate($rules);
        $value      = $request->get('value');
        try{
            DB::beginTransaction();
            $ram            = new AttributeValue();
            $ram->value     = $value;
            $ram->type_id   = $type_id;
            $ram->status    = $request->get('status');
            $ram->save();
            DB::commit();
            return redirect('attribute/'.$slug.'/list')->with('success','Added successful!');


        }catch(\Exception $e){
            DB::rollBack();
            return redirect('attribute/'.$slug.'/list')->with('error','Added successful!');


        }




    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AttributeValues  $attributeValues
     * @return \Illuminate\Http\Response
     */
    public function show(AttributeValue $attributeValues)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AttributeValues  $attributeValues
     * @return \Illuminate\Http\Response
     */
    public function edit($slug, $id, Request $request){


        $typeObj = AttributeType::where('slug',$slug)->firstOrFail();


        $name = $typeObj->name;
        $type = $typeObj->name;
        $typeID = $typeObj->id;
        $org = AttributeValue::find($id);
        return view('DynamicAttribute.create',['typeID' => $typeID, 'name' => $name, 'slug' => $slug, 'org' => $org]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AttributeValues  $attributeValues
     * @return \Illuminate\Http\Response
     */
    public function update($slug, $id, Request $request){

        // ->where('id', '!=', $id);

        $typeObj    = AttributeType::where('slug',$slug)->firstOrFail();
        $type_id    = $typeObj->id;



        $rules = ['type_id' => 'required',  'value' => [
            'required', Rule::unique('attribute_values')->where(function($query) use ($type_id, $id) {
                    $query->where('type_id', '=', $type_id)->where('id', '<>', $id);
            })
        ]];


        $validated = $request->validate($rules);

        try{
            DB::beginTransaction();

            $ram = AttributeValue::find($id);
            $ram->value = $request->get('value');
            $ram->status  = $request->get('status');
            $ram->save();
            DB::commit();
            return redirect('attribute/'.$slug.'/list')->with('success','Updated successful!');

        }catch(\Exception $e){
            DB::rollBack();
            return redirect('attribute/'.$slug.'/list')->with('Error','Error.Please Contact   Support');

        }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AttributeValues  $attributeValues
     * @return \Illuminate\Http\Response
     */



    function status($slug,$eid){


        try{
            DB::beginTransaction();

            $emp = AttributeValue::find($eid);
            if($emp->status == 0){
                $emp->status = 1;
            }else{
                $emp->status = 0;
            }
            $emp->save();
            DB::commit();
            return array('status' => true);
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->back()->with('error','Error.Please Contact   Support');
        }

    }


}
