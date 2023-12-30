@extends('Layout.master')

@section('title')
Employee
@endsection

@section('content')
<div class="page-content">
<div class="page-header"><h1>Employee</h1></div>
@include('Employee.form')
</div>
@endsection

@section('javascript')
<script>


    $(document).ready(function() {
        //   update_all_account_balance();
    });
</script>
@endsection