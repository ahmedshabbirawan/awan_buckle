<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmployeeHttpReq;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

use App\Http\Requests\EmployeeFormRequest;
use App\Models\IssuanceItem;
use App\Models\Project;
use PhpParser\Node\Expr\Empty_;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller{

    public $resource;
    public $name;
    public $adminURL;
    public $viewData;
    public $table;
    public $view;




    public function __construct(){
        $this->resource     = new Employee();
        $this->name         = $this->viewData['name']         =   'Employee';
        $this->adminURL     = $this->viewData['adminURL']     =   url('Settings/employee');
        $this->table        = 'Employees';
        $this->view         = 'Employee.';
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $this->viewData['listing_data']   = []; // $this->resource->get();
        return view($this->view.'lists',$this->viewData);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        $this->viewData['projects']      = Project::where('status',1)->orderBy('name','ASC')->pluck('name','id');
        $this->viewData['designations']  = Designation::where('status',1)->orderBy('name','ASC')->pluck('name','id');
        return view($this->view.'create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmployeeFormRequest $request){

      //   print_r($request->all()); exit;

        $designationID  = $request->get('designation_id');
        $projectID      = $request->get('project_id');
        $cnic           = str_replace('-','',$request->get('cnic'));
        $array          = array(
            'first_name'        => $request->get('first_name'),
            'last_name'         => $request->get('last_name'),
            'cnic'              => $cnic,
            'mobile'            => $request->get('mobile'),
            'email'             => $request->get('email'),
            'designation'       => optional(Designation::find($designationID))->name,
            'designation_id'    => $designationID,
            'project'           => optional(Project::find($projectID))->name,
            'project_id'        => $projectID,
            'source' => $request->get('source'),
            'status'            => $request->get('status'),
        );
        $rec = Employee::create($array);
        return redirect(route('Settings.employee.index'))->with('success',$this->name.' added successful!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show($id, Employee $employee){
        $this->viewData['row']          = Employee::findOrFail($id);
        $this->viewData['inventories']  = IssuanceItem::where('emp_id',$id)->get();
        $this->viewData['issue']    = IssuanceItem::where('emp_id',$id)->count();
        $this->viewData['in_hand']  = IssuanceItem::where('emp_id',$id)->whereNull('return_date')->count();
        $this->viewData['return']   = IssuanceItem::where('emp_id',$id)->whereNotNull ('return_date')->count();



        return view($this->view.'detail',$this->viewData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Employee $employee){
      //   $this->viewData['row'] = $employee;
        $this->viewData['employee'] = Employee::findOrFail($id);
        $this->viewData['projects']      = Project::where('status',1)->orderBy('name','ASC')->pluck('name','id');
        $this->viewData['designations']  = Designation::where('status',1)->orderBy('name','ASC')->pluck('name','id');
        return view($this->view.'edit',$this->viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update($id, EmployeeFormRequest $request, Employee $employee){

        $employee = Employee::findOrFail($id);


        $designationID  = (Int) $request->get('designation_id');
        $projectID      = (Int) $request->get('project_id');
        $cnic           = str_replace('-','',$request->get('cnic'));
        $array          = array(
            'first_name'        => $request->get('first_name'),
            'last_name'         => $request->get('last_name'),
            'cnic'              => $cnic,
            'mobile'            => $request->get('mobile'),
            'email'             => $request->get('email'),
            'designation'       => optional(Designation::find($designationID))->name,
            'designation_id'    => $designationID,
            'project'           => optional(Project::find($projectID))->name,
            'project_id'        => $projectID,
            'status'            => $request->get('status'),
        );



        
        Employee::where('id', $employee->id)->update($array);
        return redirect(route('Settings.employee.index'))->with('success','Employee updated successful!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee){
        $employee->delete();
        return redirect($this->adminURL)->with('success',$this->name.' delete successful!'); 
    }



    //------------------------------------------------------------------------------------------------------------------------


    public function getDatatable(){
        // <i class="glyphicon glyphicon-edit"></i>
        $employees = Employee::select();
        return Datatables::of($employees)
        ->addColumn('status_label', function ($row) {
           //  return $row->status_label;

            if($row->status == 1){
                return '<span class="label label-sm label-success">'.$row->status_label.'</spna>';
            }else{
                return '<span class="label label-sm label-danger">'.$row->status_label.'</spna>';
            }

        })
        ->addColumn('action', function ($row) {
            $statusIcon = 'fa fa-ban';
            if($row->status == 1){
                $statusIcon = 'fa fa-check-circle-o';
            }

            $action = '<div class="hidden-sm hidden-xs btn-group">';
            if(auth()->user()->can('employee.read')){
                $action .= '<a href="'.route('Settings.employee.view',$row->id).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
            }
            if($row->source != ' '){
                if(auth()->user()->can('employee.update')){
                    $action .= '<a href="'.$this->adminURL.'/edit/'.$row->id.'" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Edit" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';
                }
                if(auth()->user()->can('employee.status')){
                    $action .= '<a href="javascript:void(0);" onclick="changeStatus('.$row->id.');" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Change Status" ><i class="ace-icon '.$statusIcon.' bigger-120"></i></a>';
                }
                    // $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
            }
            
            $action .= '</div>';
            return $action;
            /*
            $action = '<div class="hidden-sm hidden-xs btn-group">';
            $action .= '<a href="'.$this->adminURL.'/'.$row->id.'" class="btn btn-xs btn-success" ><i class="ace-icon fa fa-search-plus bigger-120"></i></a>';
            $action .= '<a href="'.$this->adminURL.'/'.$row->id.'/edit" class="btn btn-xs btn-info" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';
            $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
            $action .= '<a href="javascript:void(0);" onclick="changeStatus('.$row->id.');" class="btn btn-xs btn-warning"><i class="ace-icon fa fa-flag bigger-120"></i></a>';
            $action .= '</div>';
            return $action;
            */
        })->rawColumns(['status_label','action'])
        ->make(true);
    }


    function changeStatus($eid){
        $emp = Employee::find($eid);
        if($emp->status == 0){
            $emp->status = 1;
        }else{
            $emp->status = 0;
        }
        $emp->save();
        return array('status' => true);
    }



    function search(Request $request){
        $text   =   $request->get('cnic');

        try{
            list($empcode, $cnic) = explode(' - ',$text);
        }catch(\Exception $e){
            return response(['message' => 'Please provide valid format!'],422);
        }

        

        $emp    =   Employee::where('cnic',$cnic)->first();
        if( isset($emp->id) ){
            return $emp;
        }else{
            return response(['message' => 'No record found!'],422);
        }
    }


    function auto_complete(Request $request){


       //  dd($request->all());

        $term = $request->get('query');


        try{
            list($empcode, $cnic) = explode(' - ',$term);
            $term = $cnic;
        }catch(\Exception $e){
            $term = $request->get('query');
        }


        $data['results'] = Employee::
        select('id','first_name', 'cnic', DB::raw("concat(emp_code,' - ',cnic) as text"))
        //select(DB::raw("concat(first_name,' ',last_name,' (',cnic,')') as text, id"))
           // ->where('first_name', 'like', "%".$term."%")
           // ->orWhere('last_name', 'like', "%".$term."%")
            ->where('cnic','like','%'.$term.'%')
            ->orWhere('emp_code','like','%'.$term.'%')
            ->limit(15)
            ->get();
           // ->pluck('first_name');
        return $data;
    }



    function sync_remote_data(){
        EmployeeHttpReq::syncRemoteData();
    }


}
