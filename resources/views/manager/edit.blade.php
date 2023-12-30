@extends('Layout.master')

@section('title')
DG / ADG
@endsection

@section('content')
@include('manager.form')
@endsection

@section('javascript')
<script>


    $(document).ready(function() {
        //   update_all_account_balance();
    });
</script>
@endsection