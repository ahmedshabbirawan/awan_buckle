@extends('Layout.master')

@section('title')
Edit Orgnization
@endsection

@section('content')
<div class="page-content">
<div class="page-header">
<h1> Attributes <small> <i class="ace-icon fa fa-angle-double-right"></i> {{ $name }} </h1>
    </div><!-- /.page-header -->
@include('Attribute.form')
<!--end row-->
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        //   update_all_account_balance();
    });
</script>
@endsection