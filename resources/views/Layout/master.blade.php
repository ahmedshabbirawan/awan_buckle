<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>@yield('title') :: Point of Sale</title>

		<meta name="description" content="overview &amp; stats" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
		<meta name="csrf-token" content="{{ csrf_token() }}" />
		<link rel="icon" type="image/x-icon" href="{{asset('assets/images/logo/favicon-32x32.png')}}">
		<link rel="stylesheet" href="{{asset('assets/css/chosen.min.css')}}" />

		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}" />
		<link rel="stylesheet" href="{{asset('assets/font-awesome/4.5.0/css/font-awesome.min.css')}}" />

		<!-- page specific plugin styles -->
		<link rel="stylesheet" href="{{asset('assets/css/bootstrap-duallistbox.min.css')}}" />



		<link rel="stylesheet" href="{{asset('assets/css/bootstrap-datepicker3.min.css')}}" />
		<link rel="stylesheet" href="{{asset('assets/css/bootstrap-timepicker.min.css')}}" />
		<link rel="stylesheet" href="{{asset('assets/css/daterangepicker.min.css')}}" />
		<link rel="stylesheet" href="{{asset('assets/css/bootstrap-datetimepicker.min.css')}}" />



		<!-- text fonts -->
		<link rel="stylesheet" href="{{asset('assets/css/fonts.googleapis.com.css')}}" />

		<!-- ace styles -->
		<link rel="stylesheet" href="{{asset('assets/css/ace.min.css')}}" class="ace-main-stylesheet" id="main-ace-style" />


		<!--[if lte IE 9]>
			<link rel="stylesheet" href="assets/css/ace-part2.min.css" class="ace-main-stylesheet" />
		<![endif]-->
		<link rel="stylesheet" href="{{asset('assets/css/ace-skins.min.css')}}" />
		<link rel="stylesheet" href="{{asset('assets/css/ace-rtl.min.css')}}" />



		<link rel="stylesheet" href="{{asset('assets/css/select2.min.css')}}" />



		
		<!--[if lte IE 9]>
		  <link rel="stylesheet" href="assets/css/ace-ie.min.css" />
		<![endif]-->

		<!-- inline styles related to this page -->

		<!-- ace settings handler -->
		<script src="{{asset('assets/js/ace-extra.min.js')}}"></script>

		<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

		<!--[if lte IE 8]>
		<script src="assets/js/html5shiv.min.js"></script>
		<script src="assets/js/respond.min.js"></script>
		<![endif]-->


        <!----- Yajra Data Tables ---->
        {{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"> --}}


	</head>

	<body class="no-skin">
		<!----- Nav--->
        @include('Layout.navbar')

		<div class="main-container ace-save-state" id="main-container">
			<script type="text/javascript">
				try{ace.settings.loadState('main-container')}catch(e){}
			</script>
			@include('Layout.sidebar')

            <div class="main-content">
                <div class="main-content-inner">

                    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                        <ul class="breadcrumb">
                            <li>
                                <i class="ace-icon fa fa-home home-icon"></i>
                                <a href="@yield('URL')">@yield('Title')</a>
                            </li>
                            <li class="active">@yield('PageName')</li>
                        </ul><!-- /.breadcrumb -->
                    </div>

                        @yield('content')
                </div>
            </div>

            <div class="footer">
                <div class="footer-inner">
                    <div class="footer-content">
                        <span class="bigger-120">
                            <span class="blue bolder">TechBite</span>
                            Point of Sale &copy; 2021-<?php echo date('Y');?>
                        </span>
                    </div>
                </div>
            </div>

			<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
				<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
			</a>
		</div><!-- /.main-container -->

		<!-- basic scripts -->

		<!--[if !IE]> -->
		<script src="{{asset('assets/js/jquery-214.min.js')}}"></script>

		<!-- <![endif]-->

		<!--[if IE]>
<script src="assets/js/jquery-1.11.3.min.js"></script>
<![endif]-->
		<script type="text/javascript">
			if('ontouchstart' in document.documentElement) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>
		<script src="{{asset('assets/js/bootstrap.min.js')}}"></script>
		<script src="{{asset('assets/js/chosen.jquery.min.js')}}"></script>

		<!-- page specific plugin scripts -->

		<!--[if lte IE 8]>
		  <script src="assets/js/excanvas.min.js"></script>
		<![endif]-->

		{{-- <script src="{{asset('assets/js/jquery-ui.custom.min.js')}}"></script>
		<script src="{{asset('assets/js/jquery.ui.touch-punch.min.js')}}"></script>
		<script src="{{asset('assets/js/jquery.easypiechart.min.js')}}"></script>
		<script src="{{asset('assets/js/jquery.sparkline.index.min.js')}}"></script>
		<script src="{{asset('assets/js/jquery.flot.min.js')}}"></script>
		<script src="{{asset('assets/js/jquery.flot.pie.min.js')}}"></script>
		<script src="{{asset('assets/js/jquery.flot.resize.min.js')}}"></script>
		
		

        <script src="{{ asset('assets/js/buttons.flash.min.js')}}"></script>
        <script src="{{ asset('assets/js/buttons.html5.min.js')}}"></script>
        <script src="{{ asset('assets/js/buttons.print.min.js')}}"></script>
        <script src="{{ asset('assets/js/buttons.colVis.min.js')}}"></script> --}}



				
				

				<script src="https://cdn.jsdelivr.net/npm/@emretulek/jbvalidator"></script>




	    <!-- page specific plugin scripts -->
        <script src="{{asset('assets/js/jquery.bootstrap-duallistbox.min.js')}}"></script>


        <!------ Yajra DataTables--->
{{-- <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script> --}}
<script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.dataTables.bootstrap.min.js')}}"></script>

<script src="{{asset('assets/js/bootstrap-datepicker.min.js')}}"></script>

<script src="{{ asset('assets/plugin/jconfirm/jquery-confirm.min.js')}}"></script>
<link rel="stylesheet" href="{{ asset('assets/plugin/jconfirm/jquery-confirm.min.css')}}">

<script src="{{ asset('assets/plugin/loading/loading.js')}}"></script>

<script src="{{ asset('assets/js/select2.min.js')}}"></script>

<script src="{{ asset('assets/js/jquery-typeahead.js')}}"></script>

<script src="{{asset('assets/js/printThis.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-wysiwyg.min.js')}}"></script>



		<!-- ace scripts -->
		<script src="{{asset('assets/js/ace-elements.min.js')}}"></script>
		<script src="{{asset('assets/js/ace.min.js')}}"></script>

<!-- 
tagging.js
tag-basic-style.css -->

<script src="{{ asset('assets/plugin/jtag/tagging.js') }}"></script>
<script>
	var baseURL = "{{ url('/') }}/";	
</script>
<script src="{{ asset('assets/js/pos_main.js') }}"></script>
<script src="{{ asset('assets/js/pos_app.js') }}"></script>

<!-- <script src="{{ asset('assets/plugin/tags/tagsinput.js') }}"></script> -->



	<style>
	/* Default jquery-loading styles */

.loading-overlay {
  display: table;
  opacity: 0.7;
}

.loading-overlay-content {
  text-transform: uppercase;
  letter-spacing: 0.4em;
  font-size: 1.15em;
  font-weight: bold;
  text-align: center;
  display: table-cell;
  vertical-align: middle;
}

.loading-overlay.loading-theme-light {
  background-color: #fff;
  color: #000;
}

.loading-overlay.loading-theme-dark {
  background-color: #000;
  color: #fff;
}

</style>


        @yield('script')

		<!-- inline scripts related to this page -->
	@yield('javascript')

`
	<script>

		$(document).ready(function(){
				// $('.input_date').attr('autocomplete','off');
				// $('.input_date').attr('onkeydown','return false');
				// $('.input_date').datepicker({ autoclose: true, format: 'yyyy-m-d' });
		});

		$( document ).on( "ready", function() {
			$('.input_date').attr('autocomplete','off');
				$('.input_date').attr('onkeydown','return false');
				$('.input_date').datepicker({ autoclose: true, format: 'yyyy-m-dd', 
					maxDate: new Date() 
				
				});
});
$(document).ready(function(){
	// pos_app.showAddProductModal();
});


	</script>
<div id="add_product_modal_con"></div>
	</body>
</html>
