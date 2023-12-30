@extends('Layout.master')

@section('title')
Shop / Branch
@endsection

@section('content')
@include('shop.form')
@endsection

@section('javascript')
<script>


    $(document).ready(function() {
        //   update_all_account_balance();
    });
</script>
@endsection