<div class="container-fluid">
	<div class="panel panel-default <?= value_if_test(!empty($vd->wr_raw_data->editor_comments), 'form-col', 'form-col-1') ?>">
		<div class="panel-body">
			<div class="row">

				<div class="col-lg-12">
					<?= $ci->load->view('manage/writing/partials/progress-bar') ?>
					<header>
						<div class="row">
							<div class="col-lg-12 page-title">
								<h2>Review Your Order</h2>
							</div>
						</div>
					</header>
					<hr class="marbot-30" />

					<form action="" method="post" class="writing-session-form required-form marbot-30 has-premium">
						<div class="row">
							<div class="<?= value_if_test(!empty($vd->wr_raw_data->editor_comments), 'col-lg-8 col-md-8 form-col-1', 'col-lg-12') ?> ">
								<label class="review-label">
									Order Details
								</label>
								<div class="table-responsive">
									<table class="table table-striped table-bordered writing-process-review-table marbot-30">
										<tbody>
											<tr>
												<td>Your Name</td>
												<td><?= Auth::user()->name() ?></td>
											</tr>
											<tr>
												<td>Your Email</td>
												<td><?= Auth::user()->email ?></td>
											</tr>
											<tr>
												<td>Order Code</td>
												<td><?= $vd->m_wr_session->id_to_code() ?></td>
											</tr>
											<tr>
												<td>Date Created</td>
												<td>
													<?php $dt_created = Date::out($vd->m_wr_session->date_created); ?>
													<?= $dt_created->format('M j, Y') ?>&nbsp;
													<span class="text-muted"><?= $dt_created->format('H:i') ?></span>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<label class="review-label">
									Company Details (<a href="manage/writing/process/<?= $vd->m_wr_session->id ?>/1">edit</a>)
								</label>
								<div class="table-responsive">
									<table class="table table-striped table-bordered writing-process-review-table marbot-30">
										<tbody>
											<tr>
												<td>Company Name</td>
												<td><?= $vd->esc($this->newsroom->company_name) ?></td>
											</tr>
											<tr>
												<td>Country</td>
												<?php $country = Model_Country::find(@$vd->m_profile->address_country_id); ?>
												<td><?= $vd->esc(@$country->name) ?></td>
											</tr>
											<tr>
												<td>Address</td>
												<td>							
													<div class="street-address">
														<?= $vd->esc(@$vd->m_profile->address_street) ?>
														<?php if (@$vd->m_profile->address_apt_suite): ?>
														#<?= $vd->esc(@$vd->m_profile->address_apt_suite) ?>
														<?php endif ?>
													</div>
													<div class="postal-region">
														<?php if (strlen(@$vd->m_profile->address_state) + 
															strlen(@$vd->m_profile->address_city) <= 12): ?> 
															<?php if ($vd->m_profile->address_city): ?>
															<?= $vd->esc(@$vd->m_profile->address_city) ?>, 
															<?php endif ?>
															<?= $vd->esc(@$vd->m_profile->address_state) ?>
															<?php if (@$vd->m_profile->address_state ||
																		 @$vd->m_profile->address_city): ?>
															<br />
															<?php endif ?>
															<?= $vd->esc(@$vd->m_profile->address_zip) ?>
														<?php else: ?>
															<?php if (@$vd->m_profile->address_city): ?>
																<?= $vd->esc(@$vd->m_profile->address_city) ?>
																<br ?>
															<?php endif ?>
															<?php if (@$vd->m_profile->address_state): ?>
																<?= $vd->esc(@$vd->m_profile->address_state) ?>
																<br />
															<?php endif ?>
															<?= $vd->esc(@$vd->m_profile->address_zip) ?>
														<?php endif ?>
													</div>
												</td>
											</tr>
											<tr>
												<td>Phone</td>
												<td><?= $vd->esc(@$vd->m_profile->phone) ?></td>
											</tr>
											<tr>
												<td>Website</td>
												<td><?= $vd->esc(@$vd->m_profile->website) ?></td>
											</tr>
											<tr>
												<td>Company Description</td>
												<td><?= $vd->esc(@$vd->m_profile->summary) ?></td>
											</tr>
										</tbody>
									</table>
								</div>
								<label class="review-label">
									Press Release Details (<a href="manage/writing/process/<?= $vd->m_wr_session->id ?>/2">edit</a>)
								</label>
								<div class="table-responsive">
									<table class="table table-striped table-bordered writing-process-review-table marbot-30">
										<tbody>
											<tr>
												<td>Nature</td>
												<?php if (!empty($vd->wr_raw_data->writing_angle)): ?>
												<td><?= $vd->esc(Model_Writing_Order::full_angle_name(@$vd->wr_raw_data->writing_angle)) ?></td>
												<?php else: ?>
												<td class="status-false">Missing</td>
												<?php endif ?>
											</tr>
											<tr>
												<td>Details</td>
												<?php if (!empty($vd->wr_raw_data->angle_detail)): ?>
												<td><?= $vd->esc(@$vd->wr_raw_data->angle_detail) ?></td>
												<?php else: ?>
												<td class="status-false">Missing</td>
												<?php endif ?>
											</tr>
											<tr>
												<td>Additional Comments</td>
												<td><?= $vd->esc(@$vd->wr_raw_data->additional_comments) ?></td>
											</tr>
											<tr>
												<td>Primary Keyword</td>
												<?php if (!empty($vd->wr_raw_data->primary_keyword)): ?>
												<td><?= $vd->esc(@$vd->wr_raw_data->primary_keyword) ?></td>
												<?php else: ?>
												<td class="status-false">Missing</td>
												<?php endif ?>
											</tr>
											<tr>
												<td>Tags</td>
												<td><?= $vd->esc($vd->m_content->get_tags_string()) ?></td>
											</tr>
											<tr>
												<td>Category</td>
												<td>
													<?php $selected_beats = $vd->m_content ? $vd->m_content->get_beats() : array(); ?>
													<?php foreach ($selected_beats as $beat): ?>
														<div><?= $beat->name ?></div>
													<?php endforeach ?>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<label class="review-label">
									Press Release Media (<a href="manage/writing/process/<?= $vd->m_wr_session->id ?>/3">edit</a>)
								</label>
								<div class="table-responsive">
									<table class="table table-striped table-bordered writing-process-review-table marbot-40">
										<tbody>
											<?php $images = $vd->m_content->get_images() ?>
											<?php if (count($images)): ?>
											<tr>
												<td>Images</td>
												<td class="writing-process-review-images">									
													<?php foreach ($images as $image): ?>
														<?php $im_variant = $image->variant('finger') ?>
														<?php $im_variant_file = $im_variant->filename; ?>
														<?php $im_variant_url = Stored_Image::url_from_filename($im_variant_file) ?>
														<?php $a_variant = $image->variant('original') ?>
														<?php $a_variant_file = $a_variant->filename; ?>
														<?php $a_variant_url = Stored_Image::url_from_filename($a_variant_file) ?>
														<a href="<?= $a_variant_url ?>" target="_blank">
															<img src="<?= $im_variant_url ?>" />
														</a>
													<?php endforeach ?>
												</td>
											</tr>
											<?php endif ?>
											<?php if (!empty($vd->m_content->rel_res_pri_link) 
												    || !empty($vd->m_content->rel_res_sec_link)): ?>
											<tr>
												<td>Links</td>
												<td>
													<ul>
														<?php if ($vd->m_content->rel_res_pri_link): ?>
														<?php if (!$vd->m_content->rel_res_pri_title) 
															$vd->m_content->rel_res_pri_title = 
															$vd->m_content->rel_res_pri_link; ?>
															<li><a href="<?= $vd->esc($vd->m_content->rel_res_pri_link) ?>"><?= 
																$vd->esc($vd->m_content->rel_res_pri_title) ?></a></li>
														<?php endif ?>
														<?php if ($vd->m_content->rel_res_sec_link): ?>
														<?php if (!$vd->m_content->rel_res_sec_title) 
															$vd->m_content->rel_res_sec_title = 
															$vd->m_content->rel_res_sec_link; ?>
															<li><a href="<?= $vd->esc($vd->m_content->rel_res_sec_link) ?>"><?= 
																$vd->esc($vd->m_content->rel_res_sec_title) ?></a></li>
														<?php endif ?>
													</ul>
												</td>
											</tr>
											<?php endif ?>
											<?php if (!empty($vd->m_content->stored_file_id_1) 
												    || !empty($vd->m_content->stored_file_id_2)): ?>
											<tr>
												<td>Files</td>
												<td>
													<ul class="related-files-with-icons">
														<?php if ($vd->m_content->stored_file_id_1): ?>
															<?= $ci->load->view('browse/view/partials/related_resources_file',
																array('stored_file_id' => $vd->m_content->stored_file_id_1,
																		'stored_file_name' => $vd->m_content->stored_file_name_1)); ?>
														<?php endif ?>
														<?php if ($vd->m_content->stored_file_id_2): ?>
															<?= $ci->load->view('browse/view/partials/related_resources_file',
																array('stored_file_id' => $vd->m_content->stored_file_id_2,
																		'stored_file_name' => $vd->m_content->stored_file_name_2)); ?>
														<?php endif ?>
													</ul>
												</td>
											</tr>
											<?php endif ?>
											<?php if (!empty($vd->m_content->web_video_id)): ?>
											<tr>
												<td>Video</td>
												<td>
													<?php $video = Video::get_instance(
														$vd->m_content->web_video_provider, 
														$vd->m_content->web_video_id); ?>
													<span class="media-block pull-left clearfix">
														<?= $video->render(352, 198) ?>
													</span>
												</td>
											</tr>
											<?php endif ?>
											<tr>
												<td>Social</td>
												<td class="writing-process-review-social">
													<?php if (!empty($vd->m_profile->soc_twitter)): ?>
													<a target="_blank" href="<?= 
														$vd->esc(Social_Twitter_Profile::url($vd->m_profile->soc_twitter))
														?>"><i class="fa fa-fw fa-twitter-square"></i></a>
													<?php endif ?>
													<?php if (!empty($vd->m_profile->soc_facebook)): ?>
													<a target="_blank" href="<?= 
														$vd->esc(Social_Facebook_Profile::url($vd->m_profile->soc_facebook))
														?>"><i class="fa fa-fw fa-facebook-square"></i></a>
													<?php endif ?>
													<?php if (!empty($vd->m_profile->soc_gplus)): ?>
													<a target="_blank" href="<?= 
														$vd->esc(Social_GPlus_Profile::url($vd->m_profile->soc_gplus))
														?>"><i class="fa fa-fw fa-google-plus-square"></i></a>
													<?php endif ?>
													<?php if (!empty($vd->m_profile->soc_youtube)): ?>
													<a target="_blank" href="<?= 
														$vd->esc(Social_Youtube_Profile::url($vd->m_profile->soc_youtube))
														?>"><i class="fa fa-fw fa-youtube-square"></i></a>
													<?php endif ?>
													<?php if (!empty($vd->m_profile->soc_linkedin)): ?>
													<a target="_blank" href="<?= 
														$vd->esc(Social_Linkedin_Profile::url($vd->m_profile->soc_linkedin))
														?>"><i class="fa fa-fw fa-linkedin-square"></i></a>
													<?php endif ?>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="row">
									<div class="col-lg-12">
										<?php if ($vd->can_submit && $vd->m_wr_order): ?>
											<button type="submit" name="is_continue" value="1"
												class="btn btn-primary marbot-30">Update Order</button>
										<?php elseif ($vd->can_submit): ?>
											<button type="submit" name="is_continue" value="1"
												class="btn btn-primary marbot-30">Submit Order</button>
										<?php endif ?>
									</div>
								</div>
							</div>

							<div class="col-lg-4 col-md-4">
								<div class="aside_tips" id="locked_aside">
									<?= $ci->load->view('manage/writing/partials/editor-comments') ?>
									<?= $ci->load->view('manage/writing/partials/reply-to-admin') ?>
								</div>
							</div>
						</div>
					</form>

					<?php 

						$loader = new Assets\JS_Loader(
							$ci->conf('assets_base'), 
							$ci->conf('assets_base_dir'));
						$loader->add('lib/jquery.lockfixed.js');
						$render_basic = $ci->is_development();
						$ci->add_eob($loader->render($render_basic));

					?>
							
					<script>
					
					$(function() {

						if (is_desktop()) {
							var options = { offset: { top: 100 } };
							$.lockfixed("#locked_aside", options);
						}

					});
					
					</script>
						</div>
					</form>

				</div>
			</div>
		</div>
	</div>
</div>