
<div id="home" class="tab-pane active">
									<div class="row">
										<div class="col-xs-12 col-sm-3 center">
											<span class="profile-picture">
												<img class="editable img-responsive" alt="Profile Pic" id="avatar2" src="{{ $row->profile_pic }} " />
											</span>

											<div class="space space-4"></div>

											<a href="#" class="btn btn-sm btn-block btn-success">
												<i class="ace-icon fa fa-plus-circle bigger-120"></i>
												<span class="bigger-110">{{ $row->full_name }}</span>
											</a>

											<a href="tel:{{ $row->mobile }}" class="btn btn-sm btn-block btn-primary">
												<i class="ace-icon fa fa-cell-o bigger-110"></i>
												<span class="bigger-110">{{ $row->mobile }}</span>
											</a>
										</div><!-- /.col -->

										<div class="col-xs-12 col-sm-9">
											<h4 class="blue">
												<span class="middle">{{ $row->first_name.' '.$row->last_name }}</span>

												<span class="label label-purple arrowed-in-right">
													<i class="ace-icon fa fa-circle smaller-80 align-middle"></i>
													{{ $row->status_label }}
												</span>
											</h4>

											<div class="profile-user-info">
												<div class="profile-info-row">
													<div class="profile-info-name"> Email </div>
													<div class="profile-info-value">
														<span>{{ $row->email }}</span>
													</div>
												</div>


												<div class="profile-info-row">
													<div class="profile-info-name"> Designation </div>
													<div class="profile-info-value">
														<span class="editable editable-click" id="username">{{ $row->designation }}</span>
													</div>
												</div>

												<div class="profile-info-row">
													<div class="profile-info-name"> Project </div>
													<div class="profile-info-value">
														<span class="editable editable-click" id="username">{{ ($row->source == '')? $row->project : optional($row->project)->name }}</span>
													</div>
												</div>

												<div class="profile-info-row">
													<div class="profile-info-name"> Mobile </div>
													<div class="profile-info-value">
														<span>{{ $row->mobile }}</span>
													</div>
												</div>



												<div class="profile-info-row">
													<div class="profile-info-name"> CNIC </div>
													<div class="profile-info-value">
														<span>{{ $row->cnic }}</span>
													</div>
												</div>



											</div>

											<div class="hr hr-8 dotted"></div>


										</div><!-- /.col -->
									</div><!-- /.row -->

									<div class="space-20"></div>

									<div class="row">
										<div class="col-xs-12 col-sm-6">
											<div class="widget-box transparent">
												<div class="widget-header widget-header-small">
													<h4 class="widget-title smaller">
														<i class="ace-icon fa fa-check-square-o bigger-110"></i>
														Active Inventory
													</h4>
												</div>

												<div class="widget-body">
													<div class="widget-main">
														<p>Content</p>
													</div>
												</div>
											</div>
										</div>

										<div class="col-xs-12 col-sm-6">
											<div class="widget-box transparent">
												<div class="widget-header widget-header-small header-color-blue2">
													<h4 class="widget-title smaller">
														<i class="ace-icon fa fa-lightbulb-o bigger-120"></i>
														Stats
													</h4>
												</div>
												<div class="widget-body">
													<div class="widget-main padding-16">
														<div class="clearfix">
															<div class="grid3 center">
																<div class="easy-pie-chart percentage" data-percent="45" data-color="#CA5952">
																	<span class="percent">{{ $issue }}</span>
																</div>

																<div class="space-2"></div>
																Items Issue
															</div>

															<div class="grid3 center">
																<div class="center easy-pie-chart percentage" data-percent="90" data-color="#59A84B">
																	<span class="percent">{{ $in_hand }}</span>
																</div>

																<div class="space-2"></div>
																In Hand
															</div>

															<div class="grid3 center">
																<div class="center easy-pie-chart percentage" data-percent="80" data-color="#9585BF">
																	<span class="percent">{{ $return }}</span>
																</div>

																<div class="space-2"></div>
																Return
															</div>
														</div>

														<div class="hr hr-16"></div>

														<div class="profile-skills">
															<!-- <div class="progress">
																<div class="progress-bar" style="width:80%">
																	<span class="pull-left">HTML5 & CSS3</span>
																	<span class="pull-right">80%</span>
																</div>
															</div>

															<div class="progress">
																<div class="progress-bar progress-bar-success" style="width:72%">
																	<span class="pull-left">Javascript & jQuery</span>

																	<span class="pull-right">72%</span>
																</div>
															</div> -->




														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div><!-- /#home -->
@section('profile_tab')
@endsection

@section('profile_script')
<script>
    $(document).ready(function() {
        console.log('I am profile content!');
    });
</script>
@endsection