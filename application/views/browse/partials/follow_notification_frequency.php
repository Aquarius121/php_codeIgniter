<?php /* using these shorter variables for visibility */ ?>
<?php $notify_instant = Model_Subscription::NOTIFY_INSTANT ?>
<?php $notify_daily = Model_Subscription::NOTIFY_DAILY ?>
<?php $notify_never = Model_Subscription::NOTIFY_NEVER ?>

<table class="table form-table">
	<tbody>
		<tr>
			<td class="col-md-6">Press Releases</td>
			<td>
				<input type="radio" name="pr_update" id="notifPressRel1" 
					value="<?= $notify_instant ?>"
					<?= value_if_test(($vd->sub && $vd->sub->notify_pr == $notify_instant) || 
						!$vd->sub, "checked='checked'") ?>>
				<label for="notifPressRel1">Instant</label>
			</td>
			<td>
				<input type="radio" name="pr_update" id="notifPressRel2"
					value="<?= $notify_daily ?>"
					<?= value_if_test($vd->sub && $vd->sub->notify_pr == $notify_daily, "checked=checked") ?>>
				<label for="notifPressRel2">Daily</label>
			</td>
			<td>
				<input type="radio" name="pr_update" id="notifPressRel3"
					value="<?= $notify_never ?>"
					<?= value_if_test($vd->sub && $vd->sub->notify_pr == $notify_never, "checked=checked") ?>>
				<label for="notifPressRel3">Never</label>
			</td>
		</tr>
		<tr>
			<td class="col-md-6">News</td>
			<td>
				<input type="radio" name="news_update" id="notifNews1" 
					value="<?= $notify_instant ?>"
					<?= value_if_test(($vd->sub && $vd->sub->notify_news == $notify_instant) || 
						!$vd->sub, "checked='checked'") ?>>
				<label for="notifNews1">Instant</label>
			</td>
			<td>
				<input type="radio" name="news_update" id="notifNews2"
					value="<?= $notify_daily ?>"
					<?= value_if_test($vd->sub && $vd->sub->notify_news == $notify_daily, "checked='checked'") ?>>
				<label for="notifNews2">Daily</label>
			</td>
			<td>
				<input type="radio" name="news_update" id="notifNews3"
					value="<?= $notify_never ?>"
					<?= value_if_test($vd->sub && $vd->sub->notify_news == $notify_never, "checked='checked'") ?>>
				<label for="notifNews3">Never</label>
			</td>
		</tr>
		<tr>
			<td class="col-md-6">Event</td>
			<td>
				<input type="radio" name="event_update" id="notifEvent1"
					value="<?= $notify_instant ?>"
					<?= value_if_test(($vd->sub && $vd->sub->notify_event == $notify_instant) || 
						!$vd->sub, "checked='checked'") ?>>
				<label for="notifEvent1">Instant</label>
			</td>
			<td>
				<input type="radio" name="event_update" id="notifEvent2"
					value="<?= $notify_daily ?>"
					<?= value_if_test($vd->sub && $vd->sub->notify_event == $notify_daily, "checked='checked'") ?>>
				<label for="notifEvent2">Daily</label>
			</td>
			<td>
				<input type="radio" name="event_update" id="notifEvent3"
					value="<?= $notify_never ?>"
					<?= value_if_test($vd->sub && $vd->sub->notify_event == $notify_never, "checked='checked'") ?>>
				<label for="notifEvent3">Never</label>
			</td>
		</tr>
		<tr>
			<td class="col-md-6">Blog</td>
			<td></td>
			<td>
				<input type="radio" name="blog_update" id="notifBlog2"
					value="<?= $notify_daily ?>"
					<?= value_if_test($vd->sub && $vd->sub->notify_blog == $notify_daily, "checked='checked'") ?>>
				<label for="notifBlog2">Daily</label>
			</td>
			<td>
				<input type="radio" name="blog_update" id="notifBlog3" 
					value="<?= $notify_never ?>"
					<?= value_if_test(($vd->sub && $vd->sub->notify_blog == $notify_never) || 
						!$vd->sub, "checked='checked'") ?>>
				<label for="notifBlog3">Never</label>
			</td>
		</tr>
		<tr>
			<td class="col-md-6">Facebook</td>
			<td></td>
			<td>
				<input type="radio" name="facebook_update" id="notifFacebook2"
					value="<?= $notify_daily ?>"
					<?= value_if_test($vd->sub && $vd->sub->notify_facebook == $notify_daily, "checked='checked'") ?>>
				<label for="notifFacebook2">Daily</label>
			</td>
			<td>
				<input type="radio" name="facebook_update" id="notifFacebook3" 
					value="<?= $notify_never ?>"
					<?= value_if_test(($vd->sub && $vd->sub->notify_facebook == $notify_never) || 
						!$vd->sub, "checked='checked'") ?>>
				<label for="notifFacebook3">Never</label>
			</td>
		</tr>
		<tr>
			<td class="col-md-6">Twitter</td>
			<td></td>
			<td>
				<input type="radio" name="twitter_update" id="notifTwitter2"
					value="<?= $notify_daily ?>"
					<?= value_if_test($vd->sub && $vd->sub->notify_twitter == $notify_daily, "checked='checked'") ?>>
				<label for="notifTwitter2">Daily</label>
			</td>
			<td>
				<input type="radio" name="twitter_update" id="notifTwitter3" 
					value="<?= $notify_never ?>"
					<?= value_if_test(($vd->sub && $vd->sub->notify_twitter == $notify_never) || 
						!$vd->sub, "checked='checked'") ?>>
				<label for="notifTwitter3">Never</label>
			</td>
		</tr>
	</tbody>
</table>