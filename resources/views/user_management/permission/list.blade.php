@extends('Layout.master')
@section('title','Permissions List')
@section('Title','User Management')
@section('URL',route("usermanagement.permission.list"))
@section('PageName','User')
@section('content')
<div class="page-content">
    <div class="page-header">
        <h1>
            Permission
            <small>
                <i class="ace-icon fa fa-angle-double-right"></i>
                List All
            </small>
        </h1>
        {{-- <a href="{{route('usermanagement.permissions.create')}}"><button class="btn btn-primary" style="position:absolute;right:20px;top:15px;">Add New</button></a> --}}
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
                                <th class="center">Permission Name</th>
                                <th class="center">Permission Description</th>
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
                ajax: "{{ route('usermanagement.permission.list') }}",
                columns: [
                    {data: 'DT_RowIndex', searchable: false, orderable: false,"width": "2%"},
                    {data: 'name', name: 'name', "className": "dt-center" },
                    {data: 'description', name: 'description', "className": "dt-center" },
                ]
            });
        });
    </script>
@stop
