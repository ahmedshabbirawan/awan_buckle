<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
// use App\Models\Customer;
use App\Models\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\Validator;

use App\Http\Requests\CustomerFromRequest;
use App\Models\Location;
use Illuminate\Support\Facades\DB;
use App\Models\SaleOrder;

class CustomerController extends Controller{

    public $resource;
    public $name;
    public $adminURL;
    public $viewData;
    public $table;
    public $view;


    public function __construct(){
        $this->resource     = new Customer();
        $this->name         = $this->viewData['name']         =   'Customer';
        $this->adminURL     = $this->viewData['adminURL']     =   url('/customer');
        $this->table        = 'customer';
        $this->view         = 'Customer.';
    }




    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        // echo Customer::find(1)->getAttri(); exit;


        $productID = $request->get('product_id');

        if($request->ajax()){
                $results = Customer::select(); //->orderBy('id','ASC');
// ->whereRelation('deliveries.deliveryProduct', 'product_id',3)
            //    if($productID != ''){
            //        $results =  $results->whereRelation('deliveries.deliveryProduct', 'product_id',$productID);    
            //    }

                



                return Datatables::of($results)
                // ->addColumn('city_name', function ($row) {
                //         return optional($row->city)->name;
                // })
                ->addColumn('action', function ($row) {
                        $statusIcon = 'fa fa-ban';
                        if($row->status == 1){
                            $statusIcon = 'fa fa-check-circle-o';
                        }
                        $action = '<div class="btn-group">';
                      //  if(auth()->user()->can('customer.read')){
                            $action .= '<a href="'.route('customer.view', $row->id).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
                     //   }
                     //   if(auth()->user()->can('customer.update')){
                            $action .= '<a href="'.route('customer.edit', $row->id).'" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Edit" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';
                     //   }
                    //    if(auth()->user()->can('customer.status')){
                            $action .= '<a href="javascript:void(0);" onclick="changeStatus('.$row->id.');" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Change Status" ><i class="ace-icon '.$statusIcon.' bigger-120"></i></a>';
                     //   }
                    //    if(auth()->user()->can('customer.delete')){
                            $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                    //    }
                        $action .= '</div>';
                        return $action;
                })->rawColumns(['status_label','action'])
                //  ->orderColumn('id', 'DESC')
                ->make(true);
        }
        return view('Customer.lists');
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
    public function store(CustomerFromRequest $request){

        $array = array(
            'name' => $request->get('name'),
            'info' => $request->get('info'),
            'cnic' => $request->get('cnic'),
            'email' => $request->get('email'),
            'mobile' => $request->get('mobile'),
            'address' => $request->get('address'),
            'status' => $request->get('status'),
        );

        try{
            DB::beginTransaction();
            $rec = Customer::create($array);
            DB::commit();
            if($request->ajax()){
                return response(['status' => true, 'message' => $this->name.' added successful!' ]);
            }else{
                return redirect(route('customer.list'))->with('success',$this->name.' added successful!');
            }
            
        }catch(\Exception $e){
            DB::rollBack();
            if($request->ajax()){
                return response()->json(['message' => 'Error.Please Contact Support'], 500);
            }else{
                return redirect()->back()->withInput($request->all())->with('error','Error.Please Contact Support');
            }
            
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show($id, Customer $customer){
        $this->viewData['row'] = Customer::findOrFail($id);
        $this->viewData['payments'] = []; // CustomerPayment::where('customer_id', $id)->get();
        $this->viewData['stats'] = $this->getCustomerStats($id);
        return view($this->view.'detail',$this->viewData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Customer $customer){
        $data['countries'] = Location::where('type','CO')->where('name','Pakistan')->pluck('name','id');
        $data['customer']  = Customer::find($id);
        return view('Customer.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update($id, CustomerFromRequest $request){


        $array = array(
            'name' => $request->get('name'),
            'mobile' => $request->get('mobile'),
            'name' => $request->get('name'),
            'info' => $request->get('info'),
            'cnic' => $request->get('cnic'),
            'email' => $request->get('email'),
            'mobile' => $request->get('mobile'),
            'address' => $request->get('address'),
            'status' => $request->get('status')
        );
        try{
            DB::beginTransaction();
            // Customer::where('id', $id)->update($array);
            $customer = Customer::find($id);
            $customer->forceFill($array)->update();
            DB::commit();
            return redirect(route('customer.list'))->with('success','Customer updated successful!');
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->back()->withInput($request->all())->with('error','Error.Please Contact   Support');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Customer $customer){

        Customer::where('id', $id)->update(['deleted_by' => auth()->user()->id]);
        $customer = Customer::find($id);
        $customer->delete();
        return redirect(route('customer.list'))->with('success','Customer delete successful!');
    }




    //-------------------------------------------------------------------------------------------


    public function getDatatable(){

        $customer = Customer::select();
        return Datatables::of($customer)
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
        $emp = Customer::find($eid);
        if($emp->status == 0){
            $emp->status = 1;
        }else{
            $emp->status = 0;
        }
        $emp->save();
        return array('status' => true);
    }

    function search(Request $request){
        $term = $request->get('query');
        // try{
        //     list($empcode, $cnic) = explode(' - ',$term);
        //     $term = $cnic;
        // }catch(\Exception $e){
        //     $term = $request->get('query');
        // }


        $data['results'] = Customer::
        select('id','name', 'cnic', DB::raw("concat(name,' - ', IF(mobile IS NOT NULL, mobile,'')) as text"))
        //select(DB::raw("concat(first_name,' ',last_name,' (',cnic,')') as text, id"))
           // ->where('first_name', 'like', "%".$term."%")
           // ->orWhere('last_name', 'like', "%".$term."%")
            ->where('name','like','%'.$term.'%')
            ->orWhere('mobile','like','%'.$term.'%')
            ->limit(15)
            ->get();
           // ->pluck('first_name');
        return $data;
    }


    function searchSelectTwo(Request $request){
        $term = $request->get('term');
        $data['results'] = Customer::
        select('id','name', 'cnic', DB::raw("concat(name,' - ', IF(mobile IS NOT NULL, mobile,'')) as text"))
        //select(DB::raw("concat(first_name,' ',last_name,' (',cnic,')') as text, id"))
           // ->where('first_name', 'like', "%".$term."%")
           // ->orWhere('last_name', 'like', "%".$term."%")
            ->where('name','like','%'.$term.'%')
            ->orWhere('mobile','like','%'.$term.'%')
            ->limit(15)
            ->get();
           // ->pluck('first_name');
        return $data;
    }

    function createViewModal(){
        return response()->json([
            'view' => View('Customer.create_customer_modal')->render(),
            'data' =>   [],
            'status' => true,
            'message' => 'OK'
        ]);
    }

    function getCustomerStats($customerId){    
        // $pay = CustomerPayment::where('customer_id',$customerId)->where('type','cash')->sum('amount');
        // $due = CustomerPayment::where('customer_id',$customerId)->where('type','late_payment')->sum('amount');
        $orders = SaleOrder::where('customer_id',$customerId)->count();
        $ordersAmount = SaleOrder::where('customer_id',$customerId)->sum('total_price');
        return [
            'pay' => 'N/A', //$pay,
            'due' => 'N/A', //$due,
            'orders' => $orders,
            'orders_amount' => $ordersAmount
        ];
     }


}
