@extends('Layout.master')

@section('title')
{{ $name }}
@endsection

@section('content')


<div class="page-content">

    <div class="page-header">
        <h1>
            Organization 
            <!-- <small>
                <i class="ace-icon fa fa-angle-double-right"></i>
                Static &amp; Dynamic Tables
            </small> -->
        </h1>
    </div><!-- /.page-header -->

@include('organization.form')
<!--end row-->
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        //   update_all_account_balance();

        setTimeout(function(){
            $('.form-group').removeClass('has-error');
        },5000);

        

    });
</script>
@endsection