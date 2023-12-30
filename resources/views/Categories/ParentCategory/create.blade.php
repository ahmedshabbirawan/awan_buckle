@extends('Layout.master')
@section('title','Create Parent Category')
@section('Title','ParentCategory')
@section('URL',route("category.parent.list"))
@section('PageName','Create')
@section('content')
<div class="page-content">
    <div class="page-header">
        <h1>
            Parent Category
            <small>
                <i class="ace-icon fa fa-angle-double-right"></i>
                Create
            </small>
        </h1>
        {{-- <a href="{{route('category.parent.create')}}"><button class="btn btn-primary" style="position:absolute;right:20px;top:15px;">Add New</button></a> --}}
    </div><!-- /.page-header -->

    <div class="row">
        <div class="col-xs-12">

            <!-- PAGE CONTENT BEGINS -->
            <div class="row">
                <div class="col-xs-12">
                    <form class="form-horizontal" method="POST" action="{{route('category.parent.store')}}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mt-2">
                                <label><b>Enter Category Name</b></label><br>
                                <input type="text" name="name" placeholder="Category Name" class="form-control" style="margin-top: 10px;" required />
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
