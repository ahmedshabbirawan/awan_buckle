@extends('Layout.master')
@section('title', {{$obj_title}}.'List')
@section('Title',{{$$obj_title}})
@section('URL',route("category.parent.list"))
@section('PageName','List')
@section('content')
<div class="page-content">
    <div class="page-header">
        <h1>
            {{$obj_title}}
            <small>
                <i class="ace-icon fa fa-angle-double-right"></i>
                List
            </small>
        </h1>
        {{-- <a href="{{route('category.parent.create')}}"><button class="btn btn-primary" style="position:absolute;right:20px;top:15px;">Add New</button></a> --}}
    </div><!-- /.page-header -->

    <div class="row">
        <div class="col-xs-12">

            <!-- PAGE CONTENT BEGINS -->
            <div class="row">
                <div class="col-xs-12">
                   <h1>Coming Soon</h1>
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
