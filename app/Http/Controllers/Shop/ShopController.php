<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\Validator;

use App\Http\Requests\ShopFromRequest;
use App\Models\Location;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{

    public $resource;
    public $name;
    public $adminURL;
    public $viewData;
    public $table;
    public $view;


    public function __construct(){
        $this->resource     = new Shop();
        $this->name         = $this->viewData['name']         =   'Shop';
        $this->adminURL     = $this->viewData['adminURL']     =   url('/shop');
        $this->table        = 'shops';
        $this->view         = 'shop.';
    }




    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        $productID = $request->get('product_id');
        if($request->ajax()){
            $results = Shop::select()->orderBy('id','ASC');
            if($productID != ''){
                $results =  $results->whereRelation('deliveries.deliveryProduct', 'product_id',$productID);    
            }

            
            return Datatables::of($results)
            ->addColumn('city_name', function ($row) {
                    return optional($row->city)->name;
            })->addColumn('action', function ($row) {
                $statusIcon = 'fa fa-ban';
                if($row->status == 1){
                    $statusIcon = 'fa fa-check-circle-o';
                }
                $action = '<div class="hidden-sm hidden-xs btn-group">';
                // if(auth()->user()->can('shop.read')){
                    $action .= '<a href="'.route('shop.view', $row->id).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
                // }
                // if(auth()->user()->can('shop.update')){
                    $action .= '<a href="'.route('shop.edit', $row->id).'" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Edit" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';
                // }
                // if(auth()->user()->can('shop.status')){
                    $action .= '<a href="javascript:void(0);" onclick="changeStatus('.$row->id.');" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Change Status" ><i class="ace-icon '.$statusIcon.' bigger-120"></i></a>';
                // }
                // if(auth()->user()->can('shop.delete')){
                    $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                // }
                $action .= '</div>';
                return $action;
            })->addColumn('shop_manager', function ($row) {
                $users = User::where('shop_id',$row->id)->get();
                $userHtml = [];
                foreach($users as $user){
                    $userHtml[] = '<a href="'.route('usermanagement.user.edit',$user->id).'">'.$user->name.'</a>';
                }
                return implode(', ',$userHtml);

            })->rawColumns(['shop_manager','status_label','action'])
            //  ->orderColumn('id', 'DESC')
            ->make(true);
        }
        return view('shop.lists');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        $data['users']      = User::all();
        $data['countries']  = Location::where('type','CO')->where('name','Pakistan')->pluck('name','id');
        return view($this->view.'create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ShopFromRequest $request){

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
        );

        try{
            DB::beginTransaction();
            $rec = Shop::create($array);
            if($request->get('user_id')){
                $user = User::find($request->user_id);
                $user->shop_id = $user->id;
                $user->save();
            }
            DB::commit();
            return redirect(route('shop.list'))->with('success',$this->name.' added successful!');
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->back()->withInput($request->all())->with('error','Error.Please Contact Admin Support');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function show($id, Shop $shop){
        $this->viewData['row'] = Shop::findOrFail($id);
        return view($this->view.'detail',$this->viewData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Shop $shop){
        $data['users']      = User::all();
        $data['countries'] = Location::where('type','CO')->where('name','Pakistan')->pluck('name','id');
        $data['shop']  = Shop::find($id);
        return view($this->view.'edit',$data); // view('shop.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function update($id, ShopFromRequest $request){

        // dd($request->all());


        $array = array(
            'name' => $request->get('name'),
            'code' => $request->get('code'),
           //  'email' => $request->get('email'),
           //  'phone' => $request->get('phone'),
            // 'ntn' => $request->get('ntn'),
           // 'fax' => $request->get('fax'),
            'address' => $request->get('address'),
            'city_id' => $request->get('city_id'),
            'province_id' => $request->get('province_id'),
            'country_id' => $request->get('country_id'),
            'status' => $request->get('status'),
           //  'updated_by' => auth()->user()->id
        );

        try{
            DB::beginTransaction();
            $shop = Shop::find($id);
            $shop->update($array);

            if($request->get('user_id')){
               //  dd($request->user_id);
                $user = User::find($request->user_id);
                $user->shop_id = $user->id;
                $user->save();
            }

            DB::commit();
            return redirect(route('shop.list'))->with('success','Shop updated successful!');
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->back()->withInput($request->all())->with('error','Error.Please Contact Admin Support');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Shop $shop){

        Shop::where('id', $id)->update(['deleted_by' => auth()->user()->id]);
        $shop = Shop::find($id);
        $shop->delete();
        return redirect(route('shop.list'))->with('success','Shop delete successful!');
    }




    //-------------------------------------------------------------------------------------------


    public function getDatatable(){

        $shop = Shop::select();
        return Datatables::of($shop)
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
        $emp = Shop::find($eid);
        if($emp->status == 0){
            $emp->status = 1;
        }else{
            $emp->status = 0;
        }
        $emp->save();
        return array('status' => true);
    }


}
