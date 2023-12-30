<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class WarehouseController extends Controller
{

    public $resource;
    public $name;
    public $adminURL;
    public $viewData;
    public $table;
    public $view;


    public function __construct(){
        $this->resource     = new Warehouse();
        $this->name         = $this->viewData['name']         =   'Warehouse';
        $this->adminURL     = $this->viewData['adminURL']     =   url('/warehouse');
        $this->table        = 'Warehouse';
        $this->view         = 'warehouse.';
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        if($request->ajax()){
            $results = Warehouse::select()->orderBy('id','ASC');
        return Datatables::of($results)
        ->addColumn('action', function ($row) {
            $statusIcon = 'fa fa-ban';
            if($row->status == 1){
                $statusIcon = 'fa fa-check-circle-o';
            }
            $action = '<div class="hidden-sm hidden-xs btn-group">';
            $action .= '<a href="'.route('Settings.warehouse.show', $row->id).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
            $action .= '<a href="'.route('Settings.warehouse.edit', $row->id).'" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Edit" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';
            $action .= '<a href="javascript:void(0);" onclick="changeStatus('.$row->id.');" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Change Status" ><i class="ace-icon '.$statusIcon.' bigger-120"></i></a>';
            $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
            $action .= '</div>';
            return $action;
        })->rawColumns(['status_label','action'])
         //  ->orderColumn('id', 'DESC')
          ->make(true);
        }

        
        return view($this->view.'lists');
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
            'name' => 'required'
        ]);
        $array = array(
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'address' => $request->get('address'),
            'status' => $request->get('status'),

        );
        $rec = $this->resource::create($array);
        return redirect(route('Settings.warehouse.index'))->with('success',$this->name.' added successful!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function show(Warehouse $warehouse)
    {
        $this->viewData['row'] = $warehouse;
        return view($this->view.'detail',$this->viewData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function edit(Warehouse $warehouse){
        $this->viewData['row'] = $warehouse;
        return view($this->view.'edit',$this->viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Warehouse $warehouse){
        Validator::make($request->all(), [
            'name' => [ 'required'], // $organization
         //   'user_id'  => 'required'
        ]);

        $array = array(
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'address' => $request->get('address'),
           
            'status' => $request->get('status'),
        );
        $this->resource::where('id', $warehouse->id)->update($array);
        return redirect(route('Settings.warehouse.index'))->with('success',$this->name.' updated successful!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function destroy(Warehouse $warehouse){
        $warehouse->delete();
        return redirect($this->adminURL)->with('success',$this->name.' delete successful!'); 
    }
}
