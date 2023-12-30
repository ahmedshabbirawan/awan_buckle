<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Manager;
use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Requests\ManagerFormRequest;
use App\Models\Location;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;

class ManagerController extends Controller{

    public $resource;
    public $name;
    public $adminURL;
    public $viewData;
    public $table;
    public $view;


    public function __construct(){

        $this->name         = $this->viewData['name']         =   'DG / ADG';
        $this->adminURL     = $this->viewData['adminURL']     =   url('/manager');
        $this->table        = 'managers';
        $this->view         = 'manager.';
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        if($request->ajax()){
            $results = Manager::select()->orderBy('id','ASC');
            return Datatables::of($results)

            // organization
            ->addColumn('organization', function ($row) {
                return optional($row->organization)->name;
            })->addColumn('action', function ($row) {
                    $statusIcon = 'fa fa-ban';
                    if($row->status == 1){
                    $statusIcon = 'fa fa-check-circle-o';
                    }
                    $action = '<div class="hidden-sm hidden-xs btn-group">';
                    if(auth()->user()->can('project_manager.read')){
                        $action .= '<a href="'.route('Settings.manager.view', $row->id).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
                    }
                    if(auth()->user()->can('project_manager.edit')){
                        $action .= '<a href="'.route('Settings.manager.edit', $row->id).'" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Edit" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';
                    }
                    if(auth()->user()->can('project_manager.status')){
                        $action .= '<a href="javascript:void(0);" onclick="changeStatus('.$row->id.');" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Change Status" ><i class="ace-icon '.$statusIcon.' bigger-120"></i></a>';
                    }
                    if(auth()->user()->can('project_manager.delete')){
                        $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                    }
                    $action .= '</div>';
                    return $action;
            })->rawColumns(['status_label','action'])
            //  ->orderColumn('id', 'DESC')
            ->make(true);
    }
    return view('manager.lists');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        $data['org'] = Organization::where('status', 1)->get()->pluck('name','id');
        return view($this->view.'create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ManagerFormRequest $request){
       
        $array = array(
            'name'     => $request->get('name'),
            'org_id'   => $request->get('org_id'),
            'mobile'   => $request->get('mobile'),
            'email'    => $request->get('email'),
            'status'   => $request->get('status'),
            'created_by' => auth()->user()->id
        );
        Manager::create($array);
        return redirect(route('Settings.manager.list'))->with('success',$array['name'].' added successful!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Manager  $manager
     * @return \Illuminate\Http\Response
     */
    public function show($id, Manager $manager){
        $data['row'] = Manager::findOrFail($id);
        return view($this->view.'detail',$data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Manager  $manager
     * @return \Illuminate\Http\Response
     */
    public function edit(Manager $manager,$id){
        $this->viewData['manager']  = Manager::find($id);
        $this->viewData['org']      = Organization::where('status', 1)->get()->pluck('name','id');
        return view($this->view.'edit',$this->viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Manager  $manager
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request, Manager $manager){
        $array = array(
            'name' => $request->get('name'),
            'org_id' => $request->get('org_id'),
            'mobile'   => $request->get('mobile'),
            'email'    => $request->get('email'),
            'status' => $request->get('status'),
            'updated_by' => auth()->user()->id
        );
        Manager::where('id', $id)->update($array);
        return redirect(route('Settings.manager.list'))->with('success',$this->name.' updated successful!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Manager  $manager
     * @return \Illuminate\Http\Response
     */
    public function destroy(Manager $manager,$id){
        Manager::where('id', $id)->update(['deleted_by' => auth()->user()->id]);
        $project    = Manager::find($id);
        $name       = $project->name;
        $project->delete();
        return redirect(route('Settings.manager.list'))->with('success',$name.' delete successful!'); 
    }


    /////----------------------------------------------------

    function getManagerByOrganization(Request $request){
        $orgID = $request->get('org_id');
        $data['selected_id'] = $request->get('selected_id');
        $data['managers']      = Manager::where('status', 1)->where('org_id',$orgID)->get()->pluck('name','id');
        return view('manager.ajax_view.select_manager',$data);
    }


    function getMangerByProject($projectID,Request $request){
        // $request->get('project_id')

        return Manager::with('project')->select('name','id')->whereHas('project', function ($query) use ($request, $projectID){
            $query->where('id',$projectID);
        })->get();


    }
}
