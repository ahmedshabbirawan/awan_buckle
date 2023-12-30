@extends('Layout.master')
@section('Title','SubCategory')
@section('URL',route("category.sub.list"))
@section('PageName','List')
@section('content')
<div class="page-content">
    <div class="page-header">
        <h1>
            Sub Category
            <small>
                <i class="ace-icon fa fa-angle-double-right"></i>
                List All
            </small>
        </h1>
        <a href="{{route('category.sub.create')}}"><button class="btn btn-primary" style="position:absolute;right:20px;top:15px;">Add New</button></a>
    </div><!-- /.page-header -->
    @include('Layout.alerts')
    <div class="row">
        <div class="col-xs-12">
            <!-- PAGE CONTENT BEGINS -->
            <div class="row">
                <div class="col-xs-12">
                    <table id="simple-table" class="table table-bordered table-hover datatable" style="width:100%" >
                        <thead>
                            <tr>
                                <th class="center">SR</th>
                                <th class="center">Parent Category Name</th>
                                <th class="center">Sub Category Name</th>
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
                ajax: "{{ route('category.sub.list') }}",
                columns: [
                    {data: 'DT_RowIndex', searchable: false, orderable: false,"width": "2%"},
                    {data: 'parentCategoryName', name: 'parentCategoryName', "className": "dt-center" },
                    {data: 'name', name: 'name', "className": "dt-center" },
                    {data: 'status', name: 'status',"className": "dt-center"},
                    {data: 'action', name: 'action',"className": "dt-center"},
                ]
            });
        });
    </script>
@stop
