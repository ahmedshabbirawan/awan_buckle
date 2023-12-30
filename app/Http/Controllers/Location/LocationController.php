<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class LocationController extends Controller{

    public $resource;
    public $name;
    public $adminURL;
    public $viewData;
    public $table;
    public $view;
    public $typeArr;


    public function __construct(){
        $this->resource     = new Location();
        $this->name         = $this->viewData['name']         =   'Location';
        $this->adminURL     = $this->viewData['adminURL']     =   url('Settings/location');
        $this->typeArr      = $this->viewData['typeArr'] = ['CO' => 'Country', 'PR' => 'Province', 'CI' => 'City'];
        $this->table        = 'locations';
        $this->view         = 'Location.';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $this->viewData['listing_data']   = []; //$this->resource->get();
        return view($this->view.'lists',$this->viewData);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        // $this->viewData['listing_data']   = $this->resource->get();
        $this->viewData['countries']    = Location::where('loc_id',0)->get()->pluck('name','id');
        return view($this->view.'create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){

        $rules = array('name' => 'required');
        $type = $request->get('type');

        if( $type == "CI" ){
            $rules['country_id'] = 'required';
            // $rules['country_id'] = 'required';
            $rules['province_id'] =  'required';
        }



        $validated = $request->validate($rules);

        $loc_id = 0;
        if( $type == "CI" ){
           $loc_id = $request->get('province_id');
        }elseif( $type == "PR" ){
            $loc_id = $request->get('country_id');
        }

        $array = array(
            'name' => $request->get('name'),
            'loc_id' => $loc_id,
            'type' => $request->get('type'),
            'status' => $request->get('status'),
        );
        $rec = $this->resource::create($array);
        return redirect($this->adminURL)->with('success',$this->name.' added successful!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function show(Location $location){
        $this->viewData['row'] = $location;
        return view($this->view.'detail',$this->viewData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function edit(Location $location){
        $this->viewData['row']          = $location;

        // parentLocation


        $this->viewData['parents_ids'] = [];

        if($location->type == 'PR'){
            $this->viewData['parents_ids'][] = $location->loc_id;
        }elseif($location->type == 'CI'){
            $this->viewData['parents_ids'][] = $location->loc_id;
            $this->viewData['parents_ids'][] = $location->parentLocation->loc_id;
        }

      //   print_r($this->viewData['parents_ids']); exit;

        $this->viewData['countries']    = Location::where('loc_id',0)->get()->pluck('name','id');
        return view($this->view.'edit',$this->viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Location $location){
        // Validator::make($request->all(), [
        //     'name' => [ 'required'], // $organization
        //  //   'user_id'  => 'required'
        // ]);

        $rules = array('name' => 'required');
        $type = $request->get('type');

        if( $type == "CI" ){
            $rules['country_id'] = 'required';
            // $rules['country_id'] = 'required';
            $rules['province_id'] =  'required';
        }



        $validated = $request->validate($rules);

        $loc_id = 0;
        if( $type == "CI" ){
           $loc_id = $request->get('province_id');
        }elseif( $type == "PR" ){
            $loc_id = $request->get('country_id');
        }

        $array = array(
            'name' => $request->get('name'),
            'loc_id' => $loc_id,
            'type' => $request->get('type'),
        //    'status' => $request->get('status'),
        );

        Location::where('id', $location->id)->update($array);
        return redirect($this->adminURL)->with('success',$this->name.' updated successful!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function destroy(Location $location){
        $location->delete();
        return redirect($this->adminURL)->with('success',$this->name.' delete successful!'); 
    }


    /******************************************************************/

    function location_child(Request $request){
        $this->viewData['request'] = $request->all();
        if($request->get('type') == 'PR'){
            $this->viewData['countries']    = Location::where('loc_id',0)->get()->pluck('name','id');
        }elseif($request->get('type') == 'CI'){
            $this->viewData['countries']    = Location::where('loc_id',0)->get()->pluck('name','id');
            $this->viewData['province']     = []; //Location::where('loc_id',0)->get();
        }

        dd($this->viewData);

        return view($this->view.'ajax_view.child_select',$this->viewData);


    }

    // getProvince


    function getProvince(Request $request){
        $data['selected_id']          =       $request->get('selected_id');
        $data['province']             =       Location::where('loc_id',$request->get('country_id'))->get()->pluck('name','id');
        return view($this->view.'ajax_view.select_province',$data);
    }


    function getCity(Request $request){
        $data['selected_id']          =       $request->get('selected_id');
        $data['province']             =       Location::where('loc_id',$request->get('province_id'))->get()->pluck('name','id');
        return view($this->view.'ajax_view.select_province',$data);
    }



    public function getDatatable(){
        
        $locations = Location::with(['parentLocation'])->orderByRaw("FIELD(type , 'CO', 'PR', 'CI') ASC")->select();
        return Datatables::of($locations)
        ->addColumn('parent_name', function ($row) {
            return optional($row->parentLocation)->name; 
        })
        ->addColumn('action', function ($row) {

            $statusIcon = 'fa fa-ban';
            if($row->status == 1){
                $statusIcon = 'fa fa-check-circle-o';
            }

            
            $action = '<div class="hidden-sm hidden-xs btn-group">';
            $action .= '<a href="'.$this->adminURL.'/'.$row->id.'" class="btn btn-xs btn-success" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
            $action .= '<a href="'.$this->adminURL.'/'.$row->id.'/edit" class="btn btn-xs btn-info" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';

            $action .= '<a href="javascript:void(0);" onclick="changeStatus('.$row->id.');" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Change Status" ><i class="ace-icon '.$statusIcon.' bigger-120"></i></a>';
            $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
            
            $action .= '</div>';
            return $action;
        })->rawColumns(['status_label','action'])
        ->make(true);
    }

    function changeStatus($eid){
        $emp = Location::find($eid);
        if($emp->status == 0){
            $emp->status = 1;
        }else{
            $emp->status = 0;
        }
        $emp->save();
        return array('status' => true);
    }





}
