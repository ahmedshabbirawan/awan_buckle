@extends('Layout.master')

@section('title')
Edit Orgnization
@endsection

@section('content')
<div class="page-content">
<div class="page-header">
        <h1> Organization  </h1>
    </div><!-- /.page-header -->
@include('organization.form')
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