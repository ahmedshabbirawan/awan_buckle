<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\ShopAdmin;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserRoleController extends Controller{

    function assignRoleView(Request $request, $userID){
        $shops = Shop::all();
        $shops->map(function ($shop) use ($userID) {
             $shopAdmin         = ShopAdmin::where(['user_id' => $userID, 'shop_id' => $shop->id])->first();
             $shop->role_id     = optional($shopAdmin)->role_id;
        });

        $data['shops'] = $shops;
        $data['roles'] = Role::all();
        $data['userId'] = $userID;
        return response()->json([
            'view' => View('user_management.user.assign_role_modal',$data)->render(),
            'data' =>   [],
            'status' => true,
            'message' => 'OK'
        ]);
    }

    function assignRolesave(Request $request){
        $shopIds    = $request->get('shop_ids');
        $roleIds    = $request->get('role_ids'); 
        $userId     = $request->get('user_id');
        if(count($shopIds) > 0){
            $index = 0;
            foreach($shopIds as $shopId){
                $roleId = (isset($roleIds[$index]))?$roleIds[$index]:0;
                // echo $shopId .' ==> '.$roleIds[$index];
                // echo '<br>';
                $user = ShopAdmin::firstOrNew(array('shop_id' => $shopId, 'user_id' => $userId));
                $user->role_id = $roleId;
                $user->save();
                $index++;
            }
        }
        return response()->json(['status' => true, 'message' => 'OK'],200); 

    }

    function changeUserCurrentShopView(){
        $userId = auth()->user()->id;
        $data['shops'] = ShopAdmin::with(['role','shop'])->where(['user_id' => $userId])->where('role_id','!=',0)->get();
        return response()->json([
            'view' => View('user_management.user.change_user_current_shop_modal',$data)->render(),
            'data' =>   [],
            'status' => true,
            'message' => 'OK'
        ]);
    }


    function changeUserCurrentShopSave(Request $request){

        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'shop_id'      => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()],406); 
        }


        $userId = auth()->user()->id;
       //  dd($userId);
        $shopId = $request->get('shop_id');

      //  dd(['shop_id' => $shopId, 'user_id' => $userId]);

        $shopAdmin = ShopAdmin::where(['shop_id' => $shopId, 'user_id' => $userId])->first();
        // dd($shopAdmin);
        if($shopAdmin->role){

            $user = User::find(auth()->user()->id);
            $user->shop_id = $shopId;
            $user->save();
            $user->syncRoles([$shopAdmin->role->name]);
            Auth::setUser($user);


            // auth()->user()->syncRoles([$shopAdmin->role->name]);
            // auth()->user()->update(['shop_id', $shopId]);
        }
        
        
        
        // auth()->user()->syncRoles(['writer', 'admin']);
        return response()->json(['status' => true, 'message' => 'Done']); 


    }
  
}
