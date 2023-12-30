@extends('Layout.master')
@section('title','Role List')
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
                List All
            </small>
        </h1>
        <a href="{{route('usermanagement.role.create')}}"><button class="btn btn-primary" style="position:absolute;right:20px;top:15px;">Add New</button></a>
    </div><!-- /.page-header -->
    @include('Layout.alerts')
    <div class="row">
        <div class="col-xs-12">
        <div class="table-responsive">
            <table id="simple-table" class="table table-bordered table-hover datatable">
                <thead>
                    <tr>
                        <th class="center">SR</th>
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
        </div>
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
                ajax: "{{ route('usermanagement.role.list') }}",
                columns: [
                    {data: 'DT_RowIndex', searchable: false, orderable: false,"width": "2%"},
                    {data: 'name', name: 'name', "className": "dt-center" },
                    {data: 'status', name: 'status',"className": "dt-center"},
                    {data: 'action', name: 'action',"className": "dt-center"},
                ]
            });
        });
    </script>
@stop
