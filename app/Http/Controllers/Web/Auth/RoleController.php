<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = Role::all();
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                $button = "";
                $button .= '<td class="center">';
                $button .= '<div class="hidden-sm hidden-xs btn-group">';
                    if($data->status == 1){
                        $button .= '<a href="'. route('usermanagement.role.status', ['id' => $data->id]) .'" data-toggle="tooltip" data-placement="top" title="Change Status">
                        <button class="btn btn-xs btn-warning">
                            <i class="ace-icon fa fa-ban bigger-120"></i>
                        </button>
                        </a>';
                    }else{

                        $button .= '<a href="'. route('usermanagement.role.status', ['id' => $data->id]) .'" data-toggle="tooltip" data-placement="top" title="Change Status">
                        <button class="btn btn-xs btn-success">
                            <i class="ace-icon fa fa-check bigger-120"></i>
                        </button>
                        </a>';
                    }

                    $button .='<a href="'. route('usermanagement.role.permissions', ['id' => $data->id]) .'" data-toggle="tooltip" data-placement="top" title="Permissions">
                                    <button class="btn btn-xs btn-primary"> <i class="ace-icon fa fa-edit bigger-120"></i></button>
                                    </a>';
                    $button .='<a href="'. route('usermanagement.role.delete', ['id' => $data->id]) .'" data-toggle="tooltip" data-placement="top" title="Delete">
                                    <button class="btn btn-xs btn-danger"><i class="ace-icon fa fa-trash-o bigger-120"></i></button>
                                    </a>';

                                    $button .='</div></td>';
                            return $button;
                })
                ->addIndexColumn()
                ->addColumn('status', function ($data) {

                    if ($data->status == 1) {
                        $status = "<div style='color:green;'>Active</div>";
                    } elseif ($data->status == 0) {
                        $status = "<div style='color:red;'>InActive</div>";
                    } elseif ($data->status == 2) {
                        $status = "<div style='color:pink;'>Disabled</div>";
                    }
                    return $status;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('user_management.role.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('UserManagement.Role.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            DB::beginTransaction();
            $obj = new Role();
            $obj->name = $request->name;
            $obj->save();
            DB::commit();
            return redirect()->route('usermanagement.role.list')->with('success','Role has been added successfully');
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->route('usermanagement.role.list')->with('error','Error.Please Contact   Support.');

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function status($id)
    {
        try{
            DB::beginTransaction();

            $obj = Role::where('id',$id)->first();

            if($obj->status==1){
                $status_string = "InActived";
                $obj->status = 0;
                $obj->save();

            }else{
                $obj->status = 1;
                $status_string = "Actived";
                $obj->save();
            }
            DB::commit();
            return redirect()->back()->with('success','Status has been '.$status_string.' Successfully');
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->back()->with('error','Error.Please Contact   Support');

        }


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function edit(Request $request,$id)
    // {
    //     $obj = Role::Where('id',$id)->first();
    //     return view('UserManagement.Role.edit',compact('obj'));
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, $id)
    // {
    //     $obj = Role::Where('id',$id)->first();
    //     $obj->name = $request->name;
    //     $obj->save();
    //     return redirect()->route('usermanagement.role.list')->with('success','Role has been updated successfully');
    // }

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
    public function permissions($id){
        $roleId = $id;
        $role = Role::where('id',$id)->first();
        // $permissions = Permission::all();

        // $collect = collect(Permission::get()->toArray());
        // $grouped = $collect->mapToGroups(function (array $item, int $key) {
        //     return [$item['name'] => $item];
        // });
        // dd($grouped);

        $permissionsArr = [];

        foreach(Permission::all() as $per){
            if(strtolower(substr($per->name ,0,5))  == 'user.'){
                $permissionsArr['user'][] = $per;
            }elseif(strtolower(substr($per->name ,0,5))  == 'role.'){
                $permissionsArr['role'][] = $per;
            }elseif(strtolower(substr($per->name ,0,5))  == 'shop.'){
                $permissionsArr['shop'][] = $per;
            }elseif(strtolower(substr($per->name ,0,8))  == 'product.'){
                $permissionsArr['product'][] = $per;
            }elseif(strtolower(substr($per->name ,0,4))  == 'pos.'){
                $permissionsArr['pos'][] = $per;
            }elseif(strtolower(substr($per->name ,0,5))  == 'sale.'){
                $permissionsArr['sale'][] = $per;
            }elseif(strtolower(substr($per->name ,0,9))  == 'purchase.'){
                $permissionsArr['purchase'][] = $per;
            }elseif(strtolower(substr($per->name ,0,15))  == 'stock_exchange.'){
                $permissionsArr['stock_exchange'][] = $per;
            }elseif(strtolower(substr($per->name ,0,16))  == 'stock_adjustment.'){
                $permissionsArr['stock_adjustment'][] = $per;
            }elseif(strtolower(substr($per->name ,0,9))  == 'customer.'){
                $permissionsArr['customer'][] = $per;
            }elseif(strtolower(substr($per->name ,0,9))  == 'supplier.'){
                $permissionsArr['supplier'][] = $per;
            }

            // supplier
            // stock_adjustment
            // stock_exchange
           // elseif(){

            // }
            else{
                $permissionsArr['z-other'][] = $per;
            }
        }

        $permissions = collect($permissionsArr)->sortKeys()->all();


      //   dd($permissionsArr);

        $rolePermissions = DB::table('role_has_permissions')->where('role_id',$id)->pluck('permission_id')->toArray();
        return view('user_management.role.permission',compact('permissions','rolePermissions','roleId','role'));
    }
    public function permissionsStore(Request $request,$roleId){

        try{
            DB::beginTransaction();
            $rolePermissions = DB::table('role_has_permissions')->where('role_id',$roleId)->delete();
            // $rolePermissions->map->delete();
            $role = Role::where('id',$roleId)->first();
            $permissionArray = $request->permissions;
            $role->syncPermissions([$permissionArray]);
            DB::commit();
            return redirect()->route('usermanagement.role.list')->with('success','Permissions added Successfully');
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->route('usermanagement.role.list')->with('error','Error.Please Contact   Support');
        }



    }
}
