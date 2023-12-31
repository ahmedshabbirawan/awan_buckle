<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Rules\Lowercase;
use App\Http\Requests\OrganizationFromRequest;
use Yajra\DataTables\DataTables;

class OrganizationController extends Controller
{
    public $resource;
    public $name;
    public $adminURL;
    public $viewData;
    public $table;
    public $view;


    public function __construct(){
        $this->resource     = new Organization();
        $this->name         = $this->viewData['name']         =   'Organization';
        $this->adminURL     = $this->viewData['adminURL']     =   url('/organization');
        $this->table        = 'Organizations';
        $this->view         = 'organization.';
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->ajax()){
            $users = Organization::withTrashed()->select()->orderBy('id','ASC');
        return Datatables::of($users)
        ->addColumn('action', function ($row) {
            $statusIcon = 'fa fa-ban';
            if($row->status == 1){
                $statusIcon = 'fa fa-check-circle-o';
            }
            $action = '<div class="hidden-sm hidden-xs btn-group">';
          //  $action .= '<a href="'.route('Settings.org.view', $row->id).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
            $action .= '<a href="'.route('Settings.org.edit', $row->id).'" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Edit" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';
            $action .= '<a href="javascript:void(0);" onclick="changeStatus('.$row->id.');" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Change Status" ><i class="ace-icon '.$statusIcon.' bigger-120"></i></a>';
         //   $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
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
    public function store(OrganizationFromRequest $request){
        $request->validated();
        $array = array(
            'name' => $request->get('name'),
            'status' => $request->get('status'),
            'created_by' => auth()->user()->id
        );
        $rec = $this->resource::create($array);
        return redirect(route('Settings.org.list'))->with('success',$this->name.' added successful!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $org = new Organization();
        return view($this->view.'detail',$org->getOrganizationById($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request){

        $org = new Organization();
        return view($this->view.'edit',$org->getOrganizationById($id));


        // $this->viewData['row'] = $organization;
        // return view($this->view.'edit',$this->viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function update($id,OrganizationFromRequest $request){

        $request->validated();
        $array = array(
            'name' => $request->get('name'),
            'status' => $request->get('status'),
            'updated_by' => auth()->user()->id
        );
        Organization::where('id', $id)->update($array);
        return redirect(route('Settings.org.list'))->with('success',$this->name.' updated successful!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){

        Organization::where('id', $id)->update(['deleted_by' => auth()->user()->id]);

        $org    = Organization::find($id);
        // $name   = $org->name;
        $org->delete();
        return redirect(route('Settings.org.list'))->with('success','Organization delete successful!');
    }


    function status($id){
        $org = Organization::find($id);
        $org->status = ($org->status == 1) ? 0 : 1;
        $org->save();
        return redirect(route('Settings.org.list'))->with('success','Organization`s status changed successful!');
    }
}
