<?php

namespace App\Http\Controllers\Uom;

use App\Http\Controllers\Controller;
use App\Models\Uom;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UomController extends Controller{

    public $resource;
    public $name;
    public $adminURL;
    public $viewData;
    public $table;
    public $view;


    public function __construct(){
        $this->resource     = new Uom();
        $this->name         = $this->viewData['name']         =   'Unit of Measure';
        $this->adminURL     = $this->viewData['adminURL']     =   url('Settings/uom');
        $this->table        = 'uoms';
        $this->view         = 'uom.';
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        if($request->ajax()){
            $results = Uom::select()->orderBy('id','ASC');
        return Datatables::of($results)
        ->addColumn('action', function ($row) {
            $statusIcon = 'fa fa-ban';
            if($row->status == 1){
                $statusIcon = 'fa fa-check-circle-o';
            }
            $action = '<div class="hidden-sm hidden-xs btn-group">';
         //    $action .= '<a href="'.route('uom.show', $row->id).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
            $action .= '<a href="'.route('Settings.uom.edit', $row->id).'" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Edit" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';
            
            $action .= '<a href="javascript:void(0);" onclick="changeStatus('.$row->id.');" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Change Status" ><i class="ace-icon '.$statusIcon.' bigger-120"></i></a>';
        //     $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
            
            $action .= '</div>';
            return $action;
        })->rawColumns(['status_label','action'])
         //  ->orderColumn('id', 'DESC')
          ->make(true);
        }

        $data = (Array) $this;
        return view($this->view.'lists',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
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
            'name' => 'required|unique:measure_units',
            'code' => 'required|unique:measure_units'
        ]);
        $array = array(
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'status' => $request->get('status')
        );
        $rec = $this->resource::create($array);
        return redirect($this->adminURL)->with('success',$this->name.' added successful!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Uom  $uom
     * @return \Illuminate\Http\Response
     */
    public function show(Uom $uom){
        $this->viewData['row'] = $uom;
        return view($this->view.'edit',$this->viewData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Uom  $uom
     * @return \Illuminate\Http\Response
     */
    public function edit(Uom $uom){
        $this->viewData['row'] = $uom;
        return view($this->view.'edit',$this->viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Uom  $uom
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Uom $uom){


        $rules['name'] = [ 'required', Rule::unique('measure_units')->ignore($uom->id)];
        $rules['code'] = [ 'required', Rule::unique('measure_units')->ignore($uom->id)];

        // Validator::make($request->all(),$rules);

        $validated = $request->validate($rules);

        $array = array(
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'status' => $request->get('status')
        );
        $this->resource::where('id', $uom->id)->update($array);
        return redirect($this->adminURL)->with('success',$this->name.' updated successful!');
    }


    function changeStatus($eid){
        $obj = Uom::find($eid);
        if($obj->status == 0){
            $obj->status = 1;
        }else{
            $obj->status = 0;
        }
        $obj->save();
        return array('status' => true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Uom  $uom
     * @return \Illuminate\Http\Response
     */
    public function destroy(Uom $uom){
        $uom->delete();
        return redirect($this->adminURL)->with('success',$this->name.' delete successful!');
    }
}
