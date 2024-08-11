<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px; width:100%">
        @include('Layout.alerts')
        <div class="card radius-10 border-top border-0 border-4 border-danger">
            <div id="add_product_modal_con" ></div>
        </div>
    </div>
</div>
@section('script')
<script>
$(document).ready(function(){
    pos_app.showAddProductModal(false);
});
 
</script>
@endsection