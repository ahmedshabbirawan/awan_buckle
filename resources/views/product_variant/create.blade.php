@extends('Layout.master')

@section('title')
Create Attribute
@endsection

@section('content')

<div class="page-content">
    

<div class="page-header">
        <h1>
        Create Attribute
        </h1>
    </div><!-- /.page-header -->

@include('product_variant.form')
</div>
<!--end row-->

@endsection

@section('javascript')
<script>
    function deleteConfirmation(objID) {
    
    }

    $(document).ready(function() {
        //   update_all_account_balance();
    });
</script>
@endsection