@extends('Layout.master')

@section('title')
{{ '' }}
@endsection

@section('content')


<div class="page-content">
<div class="page-header">
        <h1> Attribute </h1>
    </div>

<div class="row ">
<div class="col-12 col-lg-12" style="margin-top:20px;">
<div class="card radius-10 border-top border-0 border-4 border-danger">
<div class="card-body">
    @include('Layout.alerts')
    <div class="table-responsive">
        <table class="table table-striped table-bordered datatable" style="width:100%">
            <thead class="table-dark">
                <tr>
                    
                    <th>Key</th>
                    <th>Label</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $count = 1;
                if (count($list) > 0) {
                    foreach ($list as $type => $name) :  ?>
                        <tr>
                            
                            <td>{{ $type }}</td>
                            <td>{{ $name }}</td>
                            <td>
                                <div class="hidden-sm hidden-xs action-buttons">

                                <a href="{{ route('attribute.'.$type.'.list') }}" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Edit" >
                                    <i class="ace-icon fa fa-eye bigger-120"></i></a>


                               


                                    <!-- <a href="javascript:void(0);" onclick="deleteConfirmation({{ '' }});" class="red">
                                        <i class="ace-icon fa fa-trash-o bigger-130"></i> </a> -->
                                </div>
                            </td>
                        </tr>
                <?php endforeach;
                } else {
                    echo '<tr><td colspan="6" > No Record!</td></tr>';
                } ?>
            </tbody>
        </table>
    </div>
</div>
</div>
</div>
</div>
<!--end row-->
</div>

@endsection


@section('javascript')
<script>
    $(document).ready(function() {
        //   update_all_account_balance();
    });
</script>
@endsection