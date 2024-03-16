<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\Validator;

use App\Http\Requests\SupplierFromRequest;
use App\Models\Location;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{

    public $resource;
    public $name;
    public $adminURL;
    public $viewData;
    public $table;
    public $view;


    public function __construct(){
        $this->resource     = new Supplier();
        $this->name         = $this->viewData['name']         =   'Supplier';
        $this->adminURL     = $this->viewData['adminURL']     =   url('/supplier');
        $this->table        = 'supplier';
        $this->view         = 'supplier.';
    }




    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        // echo Supplier::find(1)->getAttri(); exit;


        $productID = $request->get('product_id');

        if($request->ajax()){
                $results = Supplier::select()->orderBy('id','ASC');
// ->whereRelation('deliveries.deliveryProduct', 'product_id',3)
               if($productID != ''){
                   $results =  $results->whereRelation('deliveries.deliveryProduct', 'product_id',$productID);    
               }

                



                return Datatables::of($results)
                ->addColumn('city_name', function ($row) {
                        return optional($row->city)->name;
                })
                ->addColumn('action', function ($row) {
                        $statusIcon = 'fa fa-ban';
                        if($row->status == 1){
                            $statusIcon = 'fa fa-check-circle-o';
                        }
                        $action = '<div class="btn-group">';
                        if(auth()->user()->can('supplier.read')){
                            $action .= '<a href="'.route('supplier.view', $row->id).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
                        }
                        if(auth()->user()->can('supplier.update')){
                            $action .= '<a href="'.route('supplier.edit', $row->id).'" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Edit" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';
                        }
                        if(auth()->user()->can('supplier.status')){
                            $action .= '<a href="javascript:void(0);" onclick="changeStatus('.$row->id.');" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Change Status" ><i class="ace-icon '.$statusIcon.' bigger-120"></i></a>';
                        }
                        if(auth()->user()->can('supplier.delete')){
                            $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
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
        $data['countries'] = Location::where('type','CO')->where('name','Pakistan')->pluck('name','id');
        return view($this->view.'create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SupplierFromRequest $request){

        $array = array(
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'email' => $request->get('email'),
            'phone' => $request->get('phone'),
            'ntn' => $request->get('ntn'),
            'fax' => $request->get('fax'),
            'address' => $request->get('address'),
            'city_id' => $request->get('city_id'),
            'province_id' => $request->get('province_id'),
            'country_id' => $request->get('country_id'),
           //  'created_by' => auth()->user()->id,
            'status' => $request->get('status'),

        );

            try{
                DB::beginTransaction();
                $rec = Supplier::create($array);
                DB::commit();
                return redirect(route('supplier.list'))->with('success',$this->name.' added successful!');
            }catch(\Exception $e){
                DB::rollBack();
                return redirect()->back()->withInput($request->all())->with('error','Error.Please Contact   Support');
            }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function show($id, Supplier $supplier){
        $this->viewData['row'] = Supplier::findOrFail($id);
        return view($this->view.'detail',$this->viewData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Supplier $supplier){
        $data['countries'] = Location::where('type','CO')->where('name','Pakistan')->pluck('name','id');
        $data['supplier']  = Supplier::find($id);
        return view('supplier.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update($id, SupplierFromRequest $request){


        $array = array(
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'email' => $request->get('email'),
            'phone' => $request->get('phone'),
            'ntn' => $request->get('ntn'),
            'fax' => $request->get('fax'),
            'address' => $request->get('address'),
            'city_id' => $request->get('city_id'),
            'province_id' => $request->get('province_id'),
            'country_id' => $request->get('country_id'),
            'status' => $request->get('status'),
           //  'updated_by' => auth()->user()->id
        );
        try{
            DB::beginTransaction();
            // Supplier::where('id', $id)->update($array);
            $supplier = Supplier::find($id);
            $supplier->forceFill($array)->update();
            DB::commit();
            return redirect(route('supplier.list'))->with('success','Supplier updated successful!');
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->back()->withInput($request->all())->with('error','Error.Please Contact   Support');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Supplier $supplier){

        Supplier::where('id', $id)->update(['deleted_by' => auth()->user()->id]);
        $supplier = Supplier::find($id);
        $supplier->delete();
        return redirect(route('supplier.list'))->with('success','Supplier delete successful!');
    }




    //-------------------------------------------------------------------------------------------


    public function getDatatable(){

        $supplier = Supplier::select();
        return Datatables::of($supplier)
        ->addColumn('action', function ($row) {
            $action = '<div class="hidden-sm hidden-xs btn-group">';
            $action .= '<a href="'.$this->adminURL.'/'.$row->id.'" class="btn btn-xs btn-success" ><i class="ace-icon fa fa-search-plus bigger-120"></i></a>';
            $action .= '<a href="'.$this->adminURL.'/'.$row->id.'/edit" class="btn btn-xs btn-info" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';
            $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
            $action .= '<a href="javascript:void(0);" onclick="changeStatus('.$row->id.');" class="btn btn-xs btn-warning"><i class="ace-icon fa fa-flag bigger-120"></i></a>';
            $action .= '</div>';
            return $action;
        })->rawColumns(['status_label','action'])
        ->make(true);
    }

    function status($eid){
        $emp = Supplier::find($eid);
        if($emp->status == 0){
            $emp->status = 1;
        }else{
            $emp->status = 0;
        }
        $emp->save();
        return array('status' => true);
    }


}
