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
        <form method="post" id="create_customer_form" action="{{ route('customer.store') }}" novalidate class="form-horizontal product_form">
            @csrf
            <div class="row" style="margin:0px 50px;">
                <table class="table">
                <tr> <td>Sr</td> <td>Shop</td> <td>Role</td> </tr>
                @foreach($shops as $shop)
                    
                @endforeach
                </table>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="pos_app.saveCustomerForm();">Save Customer</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>