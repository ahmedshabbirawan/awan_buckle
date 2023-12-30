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

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::with(['roles']);
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                $button = "";
                $button .= '<td class="center">';
                $button .= '<div class="hidden-sm hidden-xs btn-group">';
                    if($data->status == 1){
                        $button .= '<a href="'. route('usermanagement.user.status', ['id' => $data->id]) .'" data-toggle="tooltip" data-placement="top" title="Change Status">
                        <button class="btn btn-xs btn-warning">
                            <i class="ace-icon fa fa-ban bigger-120"></i>
                        </button>
                        </a>';
                    }else{

                        $button .= '<a href="'. route('usermanagement.user.status', ['id' => $data->id]) .'" data-toggle="tooltip" data-placement="top" title="Change Status">
                        <button class="btn btn-xs btn-success">
                            <i class="ace-icon fa fa-check bigger-120"></i>
                        </button>
                        </a>';
                    }

                    $button .=

                                    ' <a href="'. route('usermanagement.user.edit', ['id' => $data->id]) .'" data-toggle="tooltip" data-placement="top" title="Edit">
                                    <button class="btn btn-xs btn-primary">
                                        <i class="ace-icon fa fa-edit bigger-120"></i>
                                    </button>
                                    </a>

                                    <a href="'. route('usermanagement.user.delete', ['id' => $data->id]) .'" data-toggle="tooltip" data-placement="top" title="Delete">
                                    <button class="btn btn-xs btn-danger">
                                        <i class="ace-icon fa fa-trash-o bigger-120"></i>
                                    </button>
                                    </a>

                                </div>
                            </td>';
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::orderBy('name','asc')->get();
        $shops = Shop::orderBy('name','asc')->get()->pluck('name', 'id');
        return view('user_management.user.create',compact('roles','shops'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

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
           //  return response()->json(['status' => false, 'message' => $validator->errors()->first()],406);
            return redirect()->back()->withErrors($validator->errors());  //->with('success',$array['name'].' added successful!');
        }

        


        // dd($request->all());
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
                return redirect()->route('usermanagement.user.list')->with('success', 'User has been created Successfully');
            } else {

                return redirect()->back()->with('error', 'Something Went Wrong.Try Again');
            }


        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error' . 'Error.Please Contact PITB Support');

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
            return redirect()->back()->with('error','Error.Please Contact PITB SUpport');
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
            return redirect()->back()->with('error' . 'Error.Please Contact PITB Support');

        }
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
