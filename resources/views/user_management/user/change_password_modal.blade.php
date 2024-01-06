<div class="modal" id="update_password_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create Product</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form method="post" id="change_password_form" action="{{ route('usermanagement.user.save_password') }}" novalidate class="form-horizontal product_form">
                @csrf
                <input type="hidden" name="user_id" value="{{ $userID }}" >
                <div class="row">
                    <div class="col-lg-12 col-sm-12">
                        <label for="form-field-1"> New Password: </label>
                        <div class="">
                            <input type="text" required class="form-control @error('password') is-invalid @enderror" name="password" id="password" placeholder="New Password">
                        </div>
                    </div>
                    <div class="col-lg-12 col-sm-12">
                        <label for="form-field-1"> Confirm Password: </label>
                        <div class="">
                            <input type="text" required class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password">
                        </div>
                    </div>
                    <div style="clear:both;"></div>
                </div>
            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="pos_app.savePasswordForm();">Save Password</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


