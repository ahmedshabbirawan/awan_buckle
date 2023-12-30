@extends('Layout.master')

@section('title')
Project Detail
@endsection

@section('content')
<style>
    .profile-info-name{
        width: 190px;
    }
</style>



<div class="page-content">

<div class="page-header" style="min-height:40px;">
    <div class="" style="float: left;">
        <h1>Projects</h1>
    </div>
    <div class="" style="float: right;">
    <a href="{{ route('Settings.project.list') }}" class="btn btn-xs btn-light bigger"><i class="ace-icon "></i> Back </a> 
    </div>
</div>




<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px;">

        <div class="card radius-10 border-top border-0 border-4 border-danger">
       



            <div class="row">







                <div class="col-xs-12 col-sm-12">
                    <div class="widget-box">
                        <div class="widget-header">
                            <h4 class="widget-title">Detail</h4>
                        </div>
                        <div class="widget-body" style="display: block;">


	
    

                            <div class="profile-user-info profile-user-info-striped">
                                <!-- <div class="profile-info-row">
                                    <div class="profile-info-name"> ID </div>

                                    <div class="profile-info-value">
                                        <span class="editable editable-click" id="username">{{ $project->id }}</span>
                                    </div>
                                </div> -->

                                <div class="profile-info-row">
                                    <div class="profile-info-name"> Supplier Name </div>

                                    <div class="profile-info-value">
                                        <span class="editable editable-click" id="username">{{ $project->name }}</span>
                                    </div>
                                </div>

                               
                                <div class="profile-info-row">
                                    <div class="profile-info-name"> Organization </div>
                                    <div class="profile-info-value">
                                        <span class="editable editable-click" id="username">{{ optional($project->organization)->name }}</span>
                                    </div>
                                </div>


                                <div class="profile-info-row">
                                    <div class="profile-info-name"> DG / ADG </div>
                                    <div class="profile-info-value">
                                        <span class="editable editable-click" id="username">{{ $project->dg }}</span>
                                    </div>
                                </div>


                                


                                <div class="profile-info-row">
                                    <div class="profile-info-name"> Status </div>
                                    <div class="profile-info-value">
                                        <span class="editable editable-click" id="username">{{ $project->status_label }}</span>
                                    </div>
                                </div>










                        </div>


                    </div>
                </div><!-- /.span -->
            </div>





        </div>
    </div>
</div>
<!--end row-->

<!--- delete confirmation modal --->
<div class="modal" tabindex="-1" role="dialog" id="deletePopup">
    <div class="modal-dialog" role="document">
        <div class="modal-content">


            <form action="#" method="post" id="delete_form">
                <input type="hidden" name="_method" value="delete" />

                <div class="modal-header">
                    <h5 class="modal-title">confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Do you really want to delete ?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Confirm</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>

            </form>

        </div>
    </div>
</div>
<!--- delete confirmation modal end --->
@endsection

@section('javascript')
<script>
    

    $(document).ready(function() {
        //   update_all_account_balance();
    });
</script>
@endsection