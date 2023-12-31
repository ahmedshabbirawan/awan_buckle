@extends('Layout.master')

@section('title')
Project
@endsection

@section('content')

<div class="page-content">
    

<div class="page-header">
        <h1>
            Projects
            <!-- <small>
                <i class="ace-icon fa fa-angle-double-right"></i>
                Static &amp; Dynamic Tables
            </small> -->
        </h1>
    </div><!-- /.page-header -->

@include('project.form')
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