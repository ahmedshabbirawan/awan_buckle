@extends('Layout.master')
@section('title','Role Permissions')
@section('Title','User Management')
@section('URL',route("usermanagement.role.list"))
@section('PageName','Role Permissions')
@section('content')
<style>
    .right-margin-cb{
        margin-right: 15px;
    }
    .cb-group{
        margin: 20px;
    }
</style>
<div class="page-content">
    <div class="page-header">
        <h1>
            Roles
            <small>
                <i class="ace-icon fa fa-angle-double-right"></i>
                Permssions
            </small>
        </h1>
    </div><!-- /.page-header -->
    @include('Layout.alerts')
    <div class="row">
        <div class="col-xs-12">

            <!-- PAGE CONTENT BEGINS -->
            <div class="row">
                <div class="col-xs-12">
                    <form class="form-horizontal" method="POST" action="{{route('usermanagement.role.permissionStore',['id'=>$roleId])}}">
                        @csrf



<!------------------------------------------------------------------------------>


<div class="control-group">
    <h1>{{ $role->name }}</h1>    
</div>


<!------------------------------------------------------------------------------>


@foreach ($permissions as $lable => $permissionArr )
<div class="control-group cb-group">
        <label class="control-label bolder blue"><span style="color: green">{{ Illuminate\Support\Str::title($lable) }}</span></label>
        <div class="form-inline">
@foreach ($permissionArr as $permission )
<div class="checkbox right-margin-cb">
            <label>
                <input type="checkbox" name="permissions[]" class="ace" value="{{$permission->id}}" {{in_array($permission->id,$rolePermissions)?'checked':''}} />
                <span class="lbl bigger-120"> {{$permission->description}}</span>
            </label>
        </div>
@endforeach 
        </div>   
       
        
    </div>

    
@endforeach

                        
                        




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
