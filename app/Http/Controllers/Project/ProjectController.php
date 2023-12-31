<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Organization;
use App\Models\Manager;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use App\Http\Requests\ProjectFromRequest;

use App\Models\EmployeeHttpReq;


class ProjectController extends Controller
{

    public $resource;
    public $name;
    public $adminURL;
    public $viewData;
    public $table;
    public $view;


    public function __construct(){
        $this->resource     = new Project();
        $this->name         = $this->viewData['name']         =   'Project';
        $this->adminURL     = $this->viewData['adminURL']     =   url('/project');
        $this->table        = 'Project';
        $this->view         = 'project.';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        if($request->ajax()){
            $results = Project::with(['organization'])->select()->orderBy('code','ASC');
        return Datatables::of($results)
        ->addColumn('org_name', function ($row) {
            return optional($row->organization)->name;
        }) ->addColumn('dg_name', function ($row) {
            $name = optional($row->manager)->name;
            return ($name != '')?$name:'N/A';
        })
        ->addColumn('action', function ($row) {
            $statusIcon = 'fa fa-ban';
            if($row->status == 1){
                $statusIcon = 'fa fa-check-circle-o';
            }
            $action = '<div class="hidden-sm hidden-xs btn-group">';
            if(auth()->user()->can('project.read')){
                $action .= '<a href="'.route('Settings.project.view', $row->id).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
            }
            if(auth()->user()->can('project.update')){
                $action .= '<a href="'.route('Settings.project.edit', $row->id).'" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Edit" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';
            }
            if(auth()->user()->can('project.status')){
                $action .= '<a href="javascript:void(0);" onclick="changeStatus('.$row->id.');" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Change Status" ><i class="ace-icon '.$statusIcon.' bigger-120"></i></a>';
            }
            if(auth()->user()->can('project.delete')){
                $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
            }
            if(auth()->user()->can('project_ledger.read')){
                $action .= '<a href="'.route('Settings.project-ledger.list', $row->id).'" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Add Ledger" ><i class="ace-icon fa fa-book bigger-120"></i></a>';
            }
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
        
        $data['managers'] = Manager::where('status', 1)->orderBy('name','ASC')->get()->pluck('name','id');
        $data['org'] = Organization::where('status', 1)->get()->pluck('name','id');
        return view($this->view.'create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProjectFromRequest $request){

        
        
        $validated = $request->validated();
        $array = array(
            'name'      => $request->get('name'),
            'org_id'    => $request->get('org_id'),
            'dg'        => $request->get('dg'),
            'manager_id' => $request->get('manager_id'),
            'code' => $request->get('code'),
            'created_by' => auth()->user()->id,
            'status'    => $request->get('status')
        );

        Project::create($array);
        return redirect(route('Settings.project.list'))->with('success',$array['name'].' added successful!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show($id){

        return view('project.detail',['project' => Project::find($id)]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit($id,Project $project){
        $this->viewData['project'] = Project::find($id);
        $this->viewData['managers'] = Manager::where('status', 1)->orderBy('name','ASC')->get()->pluck('name','id');
        $this->viewData['org'] = Organization::where('status', 1)->get()->pluck('name','id');
        return view($this->view.'edit',$this->viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update($id, ProjectFromRequest $request){

        // echo $id; exit;

        $array = array(
            'name' => $request->get('name'),
            'dg'        => $request->get('dg'),
            'org_id' => $request->get('org_id'),
            'manager_id' => $request->get('manager_id'),
            'status' => $request->get('status'),
            'updated_by' => auth()->user()->id
        );
        $this->resource::where('id', $id)->update($array);
        return redirect(route('Settings.project.list'))->with('success',$this->name.' updated successful!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        Project::where('id', $id)->update(['deleted_by' => auth()->user()->id]);
        $project    = Project::find($id);
        $name       = $project->name;
        $project->delete();
        return redirect(route('Settings.project.list'))->with('success',$name.' delete successful!'); 
    }


    function status($eid){
        $emp = Project::find($eid);
        if($emp->status == 0){
            $emp->status = 1;
        }else{
            $emp->status = 0;
        }
        $emp->save();
        return array('status' => true);
    }


    function sync_project_data(){
        EmployeeHttpReq::syncProjectData();        
    }


    function getProjectByAvailableStockItemAndProductCat(Request $request){
        // product_category_id
        return  Project::whereHas('stockItems', function($query) use ($request) {
            $cat = $request->get('product_category_id');
            $query->where('product_cat_id',$cat);
        })->orderBy('name','DESC')->get();    
    }


}
