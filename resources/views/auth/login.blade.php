<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>Login Page :: Point of Sale</title>

		<meta name="description" content="User login page" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
		<link rel="icon" type="image/x-icon" href="{{asset('assets/images/logo/favicon-32x32.png')}}">

		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}" />
		<link rel="stylesheet" href="{{asset('assets/font-awesome/4.5.0/css/font-awesome.min.css')}}" />

		<!-- text fonts -->
		<link rel="stylesheet" href="{{asset('assets/css/fonts.googleapis.com.css')}}" />

		<!-- ace styles -->
		<link rel="stylesheet" href="{{asset('assets/css/ace.min.css')}}" />

		<!--[if lte IE 9]>
			<link rel="stylesheet" href="{{asset('assets/css/ace-part2.min.css')}}" />
		<![endif]-->
		<link rel="stylesheet" href="{{asset('assets/css/ace-rtl.min.css')}}" />

		<!-- Google Fonts -->
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;0,1000;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900;1,1000&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

		<!-- 
			font-family: 'Nunito', sans-serif;
			font-family: 'Poppins', sans-serif;
		 -->

		<!--[if lte IE 9]>
		  <link rel="stylesheet" href="{{asset('assets/css/ace-ie.min.css')}}" />
		<![endif]-->

		<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

		<!--[if lte IE 8]>
		<script src="{{asset('assets/js/html5shiv.min.js')}}"></script>
		<script src="{{asset('assets/js/respond.min.js')}}"></script>
		<![endif]-->
	</head>
    <style>

        .login-layout .widget-box{
            background-color: green;
        }

        .login-box .toolbar {
        background: whitesmoke;
        border-top: 2px solid whitesmoke;
        }

        .login-box .forgot-password-link {
        color: black;
        }

		.login-img-area{
			height: 100vh;
			width:100%;
			background-image: url("{{asset('assets/images/login/login-bg.jpg')}}");
			background-size:cover;
			background-position:top center;
			background-repeat: no-repeat;
		}

		.padding-0{
			padding: 0;
		}
    </style>

	<body class="login-layout" style="background-color: white;">
		<div class="main-container">
			<div class="main-content" >
				<div class="row d-fles">
					<div class="col-sm-12 col-md-4">
						<div class="login-container">
							<div class="center">
								<h1>
									<img src="{{asset('assets/images/logo/logo.jpg')}}" class="login-logo" alt="">
									<span class="logo-text" id="id-text2">Point of Sale</span>
								</h1>
							</div>

							<div class="space-6"></div>

							<div class="position-relative">

								<div id="login-box" class="login-box visible">
									<div class="widget-body">
										<div class="widget-main">
                                         @include('Layout.alerts')
											<!-- <h4 class="header blue lighter bigger">
												Please Enter Your Information
											</h4> -->

											<div class="space-6"></div>

											<form method="post" action="{{route('do-login')}}">
                                                @csrf
												<fieldset>
													<div class="form-group">
														<label class="block clearfix form-label">Username</label>
														<span class="block input-icon input-icon-right">
															<input type="text" class="form-control login-fields" placeholder="Username" name="userName" value="{{old('userName')}}" />
															<i class="ace-icon fa fa-user"></i>
														</span>
													</div>

													<div class="form-group">
														<label class="block clearfix form-label">Passwrod</label>
														<span class="block input-icon input-icon-right">
															<input type="password" class="form-control login-fields" placeholder="Password" name="password" />
															<i class="ace-icon fa fa-lock"></i>
														</span>
													</div>

													

													<div class="space"></div>

													<div class="clearfix">
														<label class="inline">
															<input type="checkbox" class="ace" />
															<span class="lbl"> Remember Me</span>
														</label>

														
													</div>

													<div class="clearfix">
														<button type="submit" class="width-100 btn btn-sm btn-primary login-btn">
															<i class="ace-icon fa fa-key"></i>
															<span class="bigger-110">Login</span>
														</button>
													</div>
												</fieldset>
											</form>

											

										</div><!-- /.widget-main -->
										<!-- <div class="login-bottom-logos">
											<img src="{{asset('assets/images/login/bottom logos.svg')}}" alt="">
										</div> -->
									</div><!-- /.widget-body -->
								</div><!-- /.login-box -->
							</div><!-- /.position-relative -->

							{{-- <div class="navbar-fixed-top align-right">
								<br />
								&nbsp;
								<a id="btn-login-dark" href="#">Dark</a>
								&nbsp;
								<span class="blue">/</span>
								&nbsp;
								<a id="btn-login-blur" href="#">Blur</a>
								&nbsp;
								<span class="blue">/</span>
								&nbsp;
								<a id="btn-login-light" href="#">Light</a>
								&nbsp; &nbsp; &nbsp;
							</div> --}}
						</div>
					</div><!-- /.col -->
					<div class="col-sm-12 col-md-8 padding-0 login-side-img">
						<div class="login-img-area">
							<!-- <img src="{{asset('assets/images/login/login-bg.jpg')}}" class="login-bg" alt=""> -->
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.main-content -->
		</div><!-- /.main-container -->

		<!-- basic scripts -->

		<!--[if !IE]> -->
		<script src="{{asset('assets/js/jquery-2.1.4.min.js')}}"></script>

		<!-- <![endif]-->

		<!--[if IE]>
<script src="assets/js/jquery-1.11.3.min.js"></script>
<![endif]-->
		<script type="text/javascript">
			if('ontouchstart' in document.documentElement) document.write("<script src='{{asset('assets/js/jquery.mobile.custom.min.js')}}'>"+"<"+"/script>");
		</script>

		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			jQuery(function($) {
			 $(document).on('click', '.toolbar a[data-target]', function(e) {
				e.preventDefault();
				var target = $(this).data('target');
				$('.widget-box.visible').removeClass('visible');//hide others
				$(target).addClass('visible');//show target
			 });
			});



			//you don't need this, just used for changing background
			// jQuery(function($) {
			//  $('#btn-login-dark').on('click', function(e) {
			// 	$('body').attr('class', 'login-layout');
			// 	$('#id-text2').attr('class', 'white');
			// 	$('#id-company-text').attr('class', 'blue');

			// 	e.preventDefault();
			//  });
			//  $('#btn-login-light').on('click', function(e) {
			// 	$('body').attr('class', 'login-layout light-login');
			// 	$('#id-text2').attr('class', 'grey');
			// 	$('#id-company-text').attr('class', 'blue');

			// 	e.preventDefault();
			//  });
			//  $('#btn-login-blur').on('click', function(e) {
			// 	$('body').attr('class', 'login-layout blur-login');
			// 	$('#id-text2').attr('class', 'white');
			// 	$('#id-company-text').attr('class', 'light-blue');

			// 	e.preventDefault();
			//  });

			// });
		</script>
	</body>
</html>
