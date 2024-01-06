<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->ajax()){
            $results = Designation::select()->orderBy('id','ASC');
        return Datatables::of($results)
        ->addColumn('action', function ($row) {
            $statusIcon = 'fa fa-ban';
            if($row->status == 1){
                $statusIcon = 'fa fa-check-circle-o';
            }
            $action = '<div class="hidden-sm hidden-xs btn-group">';
            if(auth()->user()->can('designation.read')){
                $action .= '<a href="'.route('Settings.designations.view', $row->id).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
            }
            if(auth()->user()->can('designation.update')){
                $action .= '<a href="'.route('Settings.designations.edit', $row->id).'" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Edit" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';
            }
            if(auth()->user()->can('designation.status')){
                $action .= '<a href="javascript:void(0);" onclick="changeStatus('.$row->id.');" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Change Status" ><i class="ace-icon '.$statusIcon.' bigger-120"></i></a>';
            }
            if(auth()->user()->can('designation.delete')){
                $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
            }
            $action .= '</div>';
            return $action;
        })->rawColumns(['status_label','action'])
         //  ->orderColumn('id', 'DESC')
          ->make(true);
        }
        return view('Employee.Designation.lists');
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        return view('Employee.Designation.create');
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

         try{
            DB::beginTransaction();
            $array = array(
                'name' => $request->get('name'),
                'status' => $request->get('status'),

            );
            
            Designation::create($array);
            DB::commit();
            return redirect(route('Settings.designations.list'))->with('success','Designation added successful!');

        }catch(\Exception $e){
            DB::rollBack();
            return redirect(route('Settings.designations.create'))->with('error','Error.Please Contact   Support');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Designation  $designation
     * @return \Illuminate\Http\Response
     */
    public function show($id,Designation $designation){
        $data['row'] = Designation::findOrFail($id);
        return view('Employee.Designation.detail',$data );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Designation  $designation
     * @return \Illuminate\Http\Response
     */
    public function edit($id,Designation $designation){
        $data['designation'] = Designation::findOrFail($id);
        return view('Employee.Designation.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Designation  $designation
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request, Designation $designation){

        $validated = $request->validate([
            'name' => 'required'
        ]);

        try{
            DB::beginTransaction();
            $array = array(
                'name' => $request->get('name'),
                'status' => $request->get('status'),
            );
            Designation::where('id', $id)->update($array);
            DB::commit();
            return redirect(route('Settings.designations.list'))->with('success','Designation added successful!');

        }catch(\Exception $e){
            DB::rollBack();
            return redirect(route('Settings.designations.edit',$id))->with('error','Error.Please Contact   Support');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Designation  $designation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Designation $designation)
    {
        //
    }
}
