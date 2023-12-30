@extends('Layout.master')

@section('title')
Supplier
@endsection

@section('content')
@include('supplier.form')
@endsection

@section('javascript')
<script>


    $(document).ready(function() {
        //   update_all_account_balance();
    });
</script>
@endsection