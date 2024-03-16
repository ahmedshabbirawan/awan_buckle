<div class="modal fade bd-example-modal-lg" id="assign_role_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Assign Role</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" id="assign_users_roles_form" action="{{ route('usermanagement.user_role.save_user_shop_role') }}" novalidate class="form-horizontal product_form">
            @csrf
            <input type="hidden" name="user_id" value="{{ $userId }}" >
            <div class="row" style="margin:0px 50px;">
                <table class="table">
                <thead><tr> <td>ID</td> <td>Shop</td> <td>Role</td> </tr></thead>
                <tbody>
                @foreach($shops as $shop)
                    <tr> 
                      <td><input type="hidden" name="shop_ids[]" value="{{ $shop->id }}" >{{ $shop->id }}</td> 
                      <td>{{ $shop->name }}</td> 
                      <td> <select name="role_ids[]"> <option value="0">No Select</option> @foreach($roles as $role) <option @if($role->id == $shop->role_id) selected="selected"  @endif value="{{ $role->id }}">{{ $role->name }}  </option>   @endforeach </select>  </td>
                   </tr>
                @endforeach
                </tbody>
                </table>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="pos_app.saveUserRoleAssignForm();">Save Users Roles</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>