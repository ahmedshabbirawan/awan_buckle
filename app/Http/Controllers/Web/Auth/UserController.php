<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\User;
use App\Models\Shop;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller{
    

    public function index(Request $request){
        if ($request->ajax()) {
            $data = User::with(['roles']);
            return DataTables::of($data)
                ->addColumn('action', function ($row) {



                    $statusIcon = 'fa fa-ban';
                    if($row->status == 1){
                    $statusIcon = 'fa fa-check-circle-o';
                    }
                     $action = '<div class="btn-group">';
                  //    $action .= '<a href="'.route('product.view', $row->id).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
                     $action .= '<a href="'. route('usermanagement.user.edit', ['id' => $row->id]) .'" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Edit" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';
                     $action .= '<a href="'.route('usermanagement.user.status', ['id' => $row->id]).'"  class="btn btn-xs btn-warning" data-toggle="tooltip" title="Change Status" ><i class="ace-icon '.$statusIcon.' bigger-120"></i></a>';
                     $action .= '<a href="javascript:void(0);"  class="btn btn-xs btn-warning" data-toggle="tooltip" title="Change Role" onclick="pos_app.showRoleAssignFormModal('.$row->id.');" ><i class="ace-icon fa fa-user bigger-120"></i></a>';
                    // $action .= '<a href="'. route('usermanagement.user.delete', ['id' => $row->id]) .'" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                    $action .= '<a href="javascript:void(0);" onclick="pos_app.showChangePasswordModal('.$row->id.')"  class="btn btn-xs btn-success" data-toggle="tooltip" title="Change Password" ><i class="ace-icon fa fa-lock bigger-120"></i></a>';
                     $action .= '</div>';
                    return $action;
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
                ->addColumn('roleName', function ($data) {
                    $rolesData = '';
                    if (!empty($data->getRoleNames())) {
                        foreach ($data->getRoleNames() as $v) {
                            $rolesData = '<label class="badge badge-pill badge-success">' . $v . '</label>';
                        }
                    }
                    return $rolesData;
                })
                ->rawColumns(['action', 'status','roleName'])
                ->make(true);
        }
        return view('user_management.user.list');

    }


    public function create(){
        $roles = Role::orderBy('name','asc')->get();
        $shops = Shop::orderBy('name','asc')->get()->pluck('name', 'id');
        return view('user_management.user.create',compact('roles','shops'));
    }

    
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'email'     => 'required',
            'username'  => 'required',
            'password'  => 'required',
            'roleId' => [
                'required',                
                function ($attribute, $value, $fail) use ($request) {
                    $shopID = $request->get('shop_id');
                    if($shopID == ''){
                        $fail('The Shop ID is required.');
                    }
                },
            ],
        ]);
        if ($validator->fails()) {
            if($request->ajax()){
                return response()->json(['status' => false, 'message' => $validator->errors()->first()],406); 
            }else{
                return redirect()->back()->withErrors($validator->errors());
            }
        }

        try {
            DB::beginTransaction();
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->userName = $request->username;
            $user->password = Hash::make($request->password);

            $user->shop_id = $request->get('shop_id');
            
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $uniqueid = uniqid();
                $image_name = $uniqueid . '.' . $image->getClientOriginalExtension();
                $image_name_server = 'uploads/user/' . $uniqueid . '.' . $image->getClientOriginalExtension();
                $user->image = $image_name_server;
                Storage::disk('public')->put('/uploads/user/' . $image_name, file_get_contents($request->file('image')));
            }
            $user->save();
            $user->syncRoles($request->roleId);
            if ($user->id) {
                DB::commit();
                if($request->ajax()){
                    return response()->json(['status' => true, 'message' => 'User has been created Successfully'],200); 
                }else{
                    return redirect()->route('usermanagement.user.list')->with('success', 'User has been created Successfully');
                }
            } else {
                if($request->ajax()){
                    return response()->json(['status' => false, 'message' => 'Something Went Wrong.Try Again'],406);
                }else{
                    return redirect()->back()->with('error', 'Something Went Wrong.Try Again');
                }
            }


        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error' . 'Error.Please Contact Support');

        }
    }


    public function status($id){
        try{
            DB::beginTransaction();
            $obj = User::where('id',$id)->first();

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
            return redirect()->back()->with('error','Error.Please Contact SUpport');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $roles = Role::orderBy('name','asc')->get();
        $obj = User::where('id', $id)->with('roles')->first();
        $shops = Shop::orderBy('name','asc')->get()->pluck('name', 'id');
        if ($obj) {
            return view('user_management.user.edit', compact('obj', 'roles','shops'));
        } else {
            return redirect()->back()->with('error', 'User Not Found');
        }
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
        try {
            DB::beginTransaction();
            $user = User::where('id',$id)->first();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->userName = $request->username;
            $user->shop_id = $request->shop_id;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $uniqueid = uniqid();
                $image_name = $uniqueid . '.' . $image->getClientOriginalExtension();
                $image_name_server = 'uploads/user/' . $uniqueid . '.' . $image->getClientOriginalExtension();
                $user->image = $image_name_server;
                Storage::disk('public')->put('/uploads/user/' . $image_name, file_get_contents($request->file('image')));
            }
            $user->save();
            $user->syncRoles($request->roleId);
            if ($user->id) {
                DB::commit();
                return redirect()->route('usermanagement.user.list')->with('success', 'User has been Updated Successfully');
            } else {

                return redirect()->back()->with('error', 'Something Went Wrong.Try Again');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error' . 'Error.Please Contact Support Department');

        }
    }

    


    function changePasswordModalView($userID){
        return response()->json([
            'view' => View('user_management.user.change_password_modal',['userID' => $userID])->render(),
            'data' =>   [],
            'status' => true,
            'message' => 'OK'
        ]);
    }

    function savePassword(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id'   => 'required',
            'password'  => ['required', 'confirmed', Password::min(6)],
           // 'password_confirmation' => 'required|password_confirmation'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()],406);
        }
        $userID = $request->get('user_id');
        try {
            DB::beginTransaction();
            $user = User::where('id',$userID)->first();
            $user->password = Hash::make($request->password);
            $user->save();
            if ($user->id) {
                DB::commit();
                return response(['status' => true, 'data' => [], 'message' => 'User has been Updated Successfully.' ]);
            } else {
                return response()->json(['status' => false, 'message' => 'Error.Something Went Wrong.Try Again'],406);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Error.Please Contact Support Department'],406);
        }
    }

    

}

// php artisan make:migration shops_admin_table
