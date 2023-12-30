@extends('Layout.master')

@section('title')
Product
@endsection

@section('content')
<div class="page-content">
<div class="page-header"><h1>Products</h1></div>
@include('product.simple.form')
</div>
@endsection

@section('javascript')
<script>


    $(document).ready(function() {
        //   update_all_account_balance();
    });
</script>
@endsection