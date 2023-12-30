@extends('Layout.master')

@section('title')
Supplier Detail
@endsection

@section('content')



<style>

.text-white {
    color: #fff !important;
}
.ui-bg-overlay-container, .ui-bg-video-container {
    position: relative;
}
.ui-bg-cover {
    background-color: transparent;
    background-position: center center;
    background-size: cover;
}
.ui-bg-overlay-container .ui-bg-overlay {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    display: block;
}

.bg-dark {
    background-color: rgba(24,28,33,0.9) !important;
}
.opacity-50 {
    opacity: .5 !important;
}
.bg-dark {
    background-color: rgba(24,28,33,0.9) !important;
}
.ui-bg-overlay-container>*, .ui-bg-video-container>* {
    position: relative;
}

</style>



<div class="ui-bg-cover ui-bg-overlay-container text-white" style="background:#00BFFF;">
  <div class="ui-bg-overlay bg-dark opacity-50"></div>
  <div class="container py-5">
    <div class="media col-md-10 col-lg-8 col-xl-7 p-0 my-4 mx-auto" style="    margin: 40px 10px;" >
      <!-- <img src="https://bootdey.com/img/Content/avatar/avatar1.png" alt class="d-block ui-w-100 rounded-circle"> -->
      <div class="media-body" style="width:10%" >
            <h4 class="font-weight-bold mb-4"><i class="menu-icon ace-icon glyphicon glyphicon-qrcode" style="font-size: 40px;" ></i></h4>
      </div>
      <div class="media-body" style="width:90%;     padding: 0px 30px;" >
        <h4 class="font-weight-bold mb-4">{{ $row->name }}</h4>
        <div class="opacity-75 mb-4">
        {{ $row->description }}
        </div>
        <!-- <a href="#" class="d-inline-block text-white">
          <strong>234</strong>
          <span class="opacity-75">followers</span>
        </a>
        <a href="#" class="d-inline-block text-white ml-3">
          <strong>111</strong>
          <span class="opacity-75">following</span>
        </a> -->
      </div>
    </div>
  </div>

  <!-- <div class="ui-bg-overlay-container">
    <div class="ui-bg-overlay bg-dark opacity-25"></div>
    <ul class="nav nav-tabs tabs-alt justify-content-center border-transparent">
      <li class="nav-item">
        <a class="nav-link text-white py-4 active" href="#">Posts</a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white py-4" href="#">Likes</a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white py-4" href="#">Followers</a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white py-4" href="#">Following</a>
      </li>
    </ul>
  </div> -->
</div>
<!-------------------------------------------------------------------------------------------------------------------------->

<style>
    .profile-info-name{
        width: 190px;
    }
</style>
<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px;">

        <div class="card radius-10 border-top border-0 border-4 border-danger">
        <div class="card-body">
                
        </div>



            <div class="row">

                <div class="col-xs-12 col-sm-12">
                    <div class="widget-box" style="margin:10px">
                        <div class="widget-header">
                        <div class="col-xs-10 col-sm-10">
                            <h4 class="widget-title">Detail</h4>
                        </div>
                        <div class="col-xs-2 col-sm-2 text-right">
                            <a href="{{ route('supplier.list') }}" class="btn btn-xs btn-light bigger right-text"><i class="ace-icon "></i> Back </a> 
                            </div>
                        </div>
                        <div class="widget-body" style="display: block;">


	
    

                            <div class="profile-user-info profile-user-info-striped">
                                <div class="profile-info-row">
                                    <div class="profile-info-name"> ID </div>

                                    <div class="profile-info-value">
                                        <span class="editable editable-click" id="username">{{ $row->id }}</span>
                                    </div>
                                </div>

                                <div class="profile-info-row">
                                    <div class="profile-info-name"> Product Title </div>

                                    <div class="profile-info-value">
                                        <span class="editable editable-click" id="username">{{ $row->name }}</span>
                                    </div>
                                </div>

                               
                                <div class="profile-info-row">
                                    <div class="profile-info-name"> Categories </div>
                                    <div class="profile-info-value">
                                        <span class="editable editable-click" id="username">{{ $categories['category']['name'] }} / {{ $categories['sub_category']['name'] }} / {{ $categories['product_category']['name'] }} </span>
                                    </div>
                                </div>


                                <div class="profile-info-row">
                                    <div class="profile-info-name"> Attributes </div>
                                    <div class="profile-info-value">
                                        <span class="editable editable-click" id="username">
                                        <?php 
                                            foreach($row->product_attribute_value->pluck('value','attribute_type.name') as $key => $value):
                                                echo $key.' : '.$value;
                                                echo '<br>';
                                            endforeach;
                                        ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="profile-info-row">
                                    <div class="profile-info-name"> Description </div>
                                    <div class="profile-info-value">
                                        <span class="editable editable-click" id="username">{{ $row->description }}</span>
                                    </div>
                                </div>
                                <div class="profile-info-row">
                                    <div class="profile-info-name"> Measure Unit </div>
                                    <div class="profile-info-value">
                                        <span class="editable editable-click" id="username">{{ (isset($row->uom->name))?$row->uom->name:'' }}</span>
                                    </div>
                                </div>
                                <div class="profile-info-row">
                                    <div class="profile-info-name"> Threshold Quantity </div>
                                    <div class="profile-info-value">
                                        <span class="editable editable-click" id="username">{{ $row->threshold_qty }}</span>
                                    </div>
                                </div>
                                <div class="profile-info-row">
                                    <div class="profile-info-name"> Status </div>
                                    <div class="profile-info-value">
                                        <span class="editable editable-click" id="username"><?=$row->status_label?></span>
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
    function deleteConfirmation(objID) {
        $('#delete_form').attr('action', '{{ $adminURL }}/' + objID);
        $('#deletePopup').modal('show');
    }

    $(document).ready(function() {
        //   update_all_account_balance();
    });
</script>
@endsection