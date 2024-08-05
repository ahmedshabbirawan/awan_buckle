<?php
namespace App\Http\Controllers\Attribute;

use App\Http\Controllers\Controller;
use App\Models\AttributeValue;
use App\Models\AttributeType;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class AttributeTypeController extends Controller{

    public function index(Request $request){
        if($request->ajax()){
            $results = AttributeType::select()->orderBy('id','ASC');
        return Datatables::of($results)
        ->addColumn('action', function ($row) {
            $statusIcon = 'fa fa-ban';
            if($row->status == 1){
                $statusIcon = 'fa fa-check-circle-o';
            }
            $action = '<div class="hidden-sm hidden-xs btn-group">';
            $action .= '<a href="'.route('attribute_type.edit',$row->id).'" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Edit" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';
            $action .= '<a href="javascript:void(0);" onclick="changeStatus('.$row->id.');" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Change Status" ><i class="ace-icon '.$statusIcon.' bigger-120"></i></a>';
            $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';

            $action .= '</div>';
            return $action;
        })->rawColumns(['status_label','action'])
          ->make(true);
        }
        return view('product_variant.lists');
    }

    
    public function create(Request $request){
      
        return view('product_variant.create');
    }

    public function store(Request $request){
     
        // dd($request->all());
        /*
        $type_id    = $request->get('type_id');
        $rules = ['type_id' => 'required',  'value' => [
            'required', Rule::unique('attribute_values')->where(function($query) use ($type_id) {
                    $query->where('type_id', '=', $type_id);
            })
        ]];
        $validated  = $request->validate($rules);
        $value      = $request->get('value');
        */
        try{
            DB::beginTransaction();
            $ram            = new AttributeType();
            $ram->name      = $request->get('name');
            $ram->status    = $request->get('status');
            $ram->save();
            DB::commit();
            return redirect(route('attribute_type.list'))->with('success','Added successful!');
        }catch(\Exception $e){
            dd($e->getMessage());
            DB::rollBack();
            return redirect()->back()->with('error','Error.Please Contact Support');
        }
    }



    public function edit($id, Request $request){
        $attribute = AttributeType::find($id);
        return view('product_variant.create',['attribute' => $attribute]);
    }

    public function show($id){
        $attribute = AttributeType::find($id);
        return response()->json($attribute);
    }

  
    public function update($id, Request $request){
        // $typeObj    = Attribute::find($id);
        // $type_id    = $typeObj->id;
        // $rules = ['type_id' => 'required',  'value' => [
        //     'required', Rule::unique('attribute_values')->where(function($query) use ($type_id, $id) {
        //             $query->where('type_id', '=', $type_id)->where('id', '<>', $id);
        //     })
        // ]];
        // $validated = $request->validate($rules);
        try{
            DB::beginTransaction();

            $ram = AttributeType::find($id);
            $ram->name = $request->get('name');
            $ram->status  = $request->get('status');
            $ram->save();
            DB::commit();
            return redirect(route('attribute_type.list'))->with('success','Updated successful!');

        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->back()->with('error','Error.Please Contact   Support');

        }


    }

    



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
