@extends('Layout.master')
@section('title','User List')
@section('Title','User Management')
@section('URL',route("usermanagement.user.list"))
@section('PageName','User')

@section('content')
<div class="page-content">
    <div class="page-header">
        <div class="" style="float: left;">
        <h1>User<small><i class="ace-icon fa fa-angle-double-right"></i> List All </small></h1>
        </div>
        <div class="" style="float: right;">
            <a href="{{route('usermanagement.user.create')}}" class="btn btn-xs btn-light bigger"><i class="ace-icon fa fa-floppy-o"></i> Add Record </a> 
        </div>
        
    </div><!-- /.page-header -->
    @include('Layout.alerts')
    <div class="row">
        <div class="col-xs-12">
            <!-- PAGE CONTENT BEGINS -->
            <div class="row">
                <div class="col-xs-12">
                    <table id="simple-table" class="table table-bordered table-hover datatable">
                        <thead>
                            <tr>
                                <th class="center">SR</th>
                                <th class="center">User Name</th>
                                <th class="center">Role Name</th>
                                <th class="center" class="hidden-480">Status</th>
                                <th class="center" class="hidden-480">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                            </tr>
                        </tbody>
                    </table>
                </div><!-- /.span -->
            </div><!-- /.row -->

            <!-- PAGE CONTENT ENDS -->
        </div><!-- /.col -->
    </div>
    <!-- /.row -->
</div><!-- /.page-content -->
@stop

@section('script')
    <script>
        $(document).ready(function() {
            var table = $('.datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('usermanagement.user.list') }}",
                columns: [
                    {data: 'DT_RowIndex', searchable: false, orderable: false,"width": "2%"},
                    {data: 'name', name: 'name', "className": "dt-center" },
                    {data: 'roleName', name: 'roleName', "className": "dt-center" },
                    {data: 'status', name: 'status',"className": "dt-center"},
                    {data: 'action', name: 'action',"className": "dt-center"},
                ]
            });
        });
    </script>
@stop
