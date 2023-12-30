<?php

namespace App\Http\Controllers\ReturnItem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ReturnDetail;
use Yajra\DataTables\DataTables;

use App\Models\Employee;

class ReturnItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->ajax()){


        $groupBy = $request->get('group_by');

// return_detail_id
        $items = ReturnDetail::with(['employee','returnItem'])->where(function($query) use ($request) {
        //     $empID      = $request->get('emp_id');
        //    //  $itemStatus = $request->get('item_status');
        //     $returnable = $request->get('returnable');
        //     if( $empID != '' ){
        //         $query->where('emp_id',$empID);
        //     }
        //     if( $returnable == '1' ){
        //         $query->whereNull('return_date');
        //     }
        }); 
        
        
        // if($groupBy != ''){
        //     $items = $items->selectRaw('*,count(id) as item_count')->groupBy('issue_key');
        // }
        $items = $items->get();
        return Datatables::of($items)
        // ->addColumn('product_detail', function ($row) {
        //     $item =  "Product : ".$row->product->name.'<br>';
        //     $item .= "Serial  : ".$row->serial_number.'<br>';
        //     $item .= "Project : ".optional($row->project)->name;
        //     return $item;
        // })
        // ->addColumn('product_attribute', function ($row) {
        //     return str_replace(' / ','</br>',strip_tags($row->product->attributeHTML()));
        // })
        // ->addColumn('item_condition', function ($row) {
        //     $condition = $row->return_condition_status;
        //     $select  = '<select name="return_condition_status_'.$row->id.'">';
        //     $select .= '<option value="1" '.( ($condition == 1) ? 'selected="selected"' : '' ).' >Working Condition</option>';
        //     $select .= '<option value="2" '.( ($condition == 2) ? 'selected="selected"' : '' ).' >Dead</option>';
        //     $select .= '<option value="3" '.( ($condition == 3) ? 'selected="selected"' : '' ).' >Damaged</option>';
        //     $select .= '<option value="4" '.( ($condition == 4) ? 'selected="selected"' : '' ).' >Faulty</option>';
        //     $select .= '</select>';
        //     return $select;
        //  })


        ->addColumn('item_count', function ($row) {
            return count($row->returnItem); // (isset($row->issuanceItem))?$row->item_count:'';
        })
        ->addColumn('item_count', function ($row) {
            return count($row->returnItem); // (isset($row->issuanceItem))?$row->item_count:'';
        })
        ->addColumn('return_date', function ($row) {
            return $row->created_at->format('d-m-Y');
        })
        ->addColumn('employee_detail', function ($row) {
            $emp = $row->employee;
            return '<a  href="'.route('Settings.employee.view',$row->emp_id).'">'.optional($emp)->full_name.' - '.optional($emp)->designation.' </a>';
        })
        ->addColumn('received_by', function ($row) {
            return $row->receivedBy->name;
        })
        ->addColumn('action', function ($row) {
            $file = $row->return_report_file;
            $statusIcon = 'fa fa-ban';
            if($row->status == 1){
                $statusIcon = 'fa fa-check-circle-o';
            }

            $action = '<div class="hidden-sm hidden-xs btn-group">';
            $action .= '<a href="'.route('return.detail', $row->id ).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
            if($file != ''){
                $action .= '<a href="'.asset('storage/documents/'.$file).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="View File"><i class="ace-icon fa fa-file bigger-120"></i></a>';
            }
            $action .= '</div>';
            return $action;
        })->rawColumns(['product_detail','product_attribute','status_label','action','item_condition','employee_detail','return_condition_status_label'])
        ->make(true);
        }else{
            return view('ReturnItem.lists');
        }
    }


    function detail($id){
        $returnDetail      = ReturnDetail::find($id);
        $employee          = Employee::findOrFail($returnDetail->emp_id);
        return view('ReturnItem.detail',['detail_id' => $id, 'row' => $employee]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
