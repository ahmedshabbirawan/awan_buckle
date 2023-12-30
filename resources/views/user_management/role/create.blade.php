@extends('Layout.master')
@section('title','Role Create')
@section('Title','User Management')
@section('URL',route("usermanagement.role.list"))
@section('PageName','Roles')
@section('content')
<div class="page-content">
    <div class="page-header">
        <h1>
            Roles
            <small>
                <i class="ace-icon fa fa-angle-double-right"></i>
                Create
            </small>
        </h1>
        {{-- <a href="{{route('category.parent.create')}}"><button class="btn btn-primary" style="position:absolute;right:20px;top:15px;">Add New</button></a> --}}
    </div><!-- /.page-header -->
    @include('Layout.alerts')
    <div class="row">
        <div class="col-xs-12">

            <!-- PAGE CONTENT BEGINS -->
            <div class="row">
                <div class="col-xs-12">
                    <form class="form-horizontal" method="POST" action="{{route('usermanagement.role.store')}}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mt-2">
                                <label><b>Enter Role Name</b></label>
                                <input type="text" name="name" placeholder="Role Name" class="form-control" required />
                            </div>
                        </div>
                        <div style="margin-top:10px;">
                            <input type="submit" class="btn btn-success">
                        </div>
                    </form>
                </div><!-- /.span -->
            </div><!-- /.row -->

            <!-- PAGE CONTENT ENDS -->
        </div><!-- /.col -->
    </div>
    <!-- /.row -->
</div><!-- /.page-content -->
@stop

@section('script')

@stop
