<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

use App\Models\ProjectLedger;
use App\Models\StockDelivery;
use Yajra\DataTables\DataTables;

class ProjectLedgerController extends Controller{
    

    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($projectID,Request $request){

        
        if($request->ajax()){
            $results = ProjectLedger::select()->where('project_id',$projectID)->orderBy('id','ASC');
            return Datatables::of($results)->addColumn('action', function ($row) {
                    $statusIcon = 'fa fa-ban';
                    if($row->status == 1){
                    $statusIcon = 'fa fa-check-circle-o';
                    }
                    $action = '<div class="hidden-sm hidden-xs btn-group">';
                  //  $action .= '<a href="'.route('Settings.manager.view', $row->id).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
                   // $action .= '<a href="'.route('Settings.manager.edit', $row->id).'" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Edit" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';
                   // $action .= '<a href="javascript:void(0);" onclick="changeStatus('.$row->id.');" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Change Status" ><i class="ace-icon '.$statusIcon.' bigger-120"></i></a>';
                   if(auth()->user()->can('project_ledger.read')){ 
                        $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                   } 
                   $action .= '</div>';
                    return $action;
            })->rawColumns(['status_label','action'])
            //  ->orderColumn('id', 'DESC')
            ->make(true);
    }
    $data['projectID'] = $projectID; 
    return view('Project_Ledger.lists',$data);

    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $array = array(
            'name'      => $request->get('name'),
            'project_id'    => $request->get('project_id'),
            'status'    => 1
        );

       return  ProjectLedger::create($array);
    }

    



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Manager  $manager
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProjectLedger $projctLedger,$id){
        $stock = StockDelivery::where('stock_ledger_reference',$id)->get();
        ProjectLedger::where('id', $id)->update(['deleted_by' => auth()->user()->id]);
        $projectLed = ProjectLedger::findorFail($id);
        $projectID  = $projectLed->project_id;
        if( count($stock) > 0 ){
            return redirect(route('Settings.project-ledger.list', $projectID))->with('error','Cannot delete Ledger. Because of already use in delivery.');
        }
        $name       = $projectLed->name;
        $projectLed->delete();
        return redirect(route('Settings.project-ledger.list', $projectID))->with('success',$name.' ledger delete successful!'); 
    }


   


    function ledger_by_project($projectID,Request $request){
        $data =  ProjectLedger::select()->where('project_id',$projectID)->orderBy('id','ASC')->get();
        return response()->json($data);
    }


}
