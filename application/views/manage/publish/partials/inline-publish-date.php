<?php if ($vd->m_content && $vd->m_content->is_identity_locked): ?>
<div class="input-field-locker input-group">
<?php endif ?>
	<input class="form-control in-text datepicker required" id="publish-date" type="text" 
		data-date-format="yyyy-mm-dd hh:ii" name="date_publish" 
		<?php if ($vd->m_content && $vd->m_content->is_identity_locked): ?>
		data-required-use-parent="1"
		<?php endif ?>
		value="<?= @$vd->m_content->date_publish_str ?>"
		<?= value_if_test($vd->m_content && $vd->m_content->is_identity_locked, 'disabled') ?>
		placeholder="Publish Date" />
<?php if ($vd->m_content && $vd->m_content->is_identity_locked): ?>
	<label class="input-group-addon lock-date">
		<input type="checkbox" id="publish-date-unlock"
			name="date_publish_unlock" value="1" />
		<span></span>
	</label>
</div>
<script>
	
defer(function() {

	var publish_date = $("#publish-date");
	var unlock = $("#publish-date-unlock");
	var lock_date = $(".lock-date");
	unlock.on("change", function() {
		publish_date.prop("disabled", !unlock.is(":checked"));
		lock_date.addClass("bg-white");
	});
});

</script>
<?php endif ?>
<script>

defer(function() {
	
	var nowTemp = new Date();
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), 
		nowTemp.getDate(), 0, 0, 0, 0);
	
	var publish_date = $("#publish-date");
	var is_publish_date_msg_shown = false;
	
	publish_date.datetimepicker({
		startDate: now,
		autoclose: true,
		todayBtn: true,
		minView: 1,
		forceParse: false,
	});

	var string_to_date = function(s) {

		var dateParts = s.split(' ')[0].split('-'); 
		var timeParts = s.split(' ')[1].split(':');
		var d = new Date(dateParts[0], --dateParts[1], dateParts[2],
			timeParts[0], timeParts[1]);

		return d;

	};

	var date_to_string = function(d) {

		var format = "{{Ye}}-{{Mo}}-{{Da}} {{Ho}}:{{Mi}}";
		return format.format({
			Ye: (d.getFullYear()),
			Mo: (d.getMonth() + 1).pad(2),
			Da: (d.getDate()).pad(2),
			Ho: (d.getHours()).pad(2),
			Mi: (d.getMinutes()).pad(2)
		});

	};

	publish_date.on("changeDate", function(ev) {

		var pub_date_str = publish_date.val();
		if (!pub_date_str.length) return;
		var pub_date = string_to_date(pub_date_str);
		pub_date.setMinutes(0);
		pub_date_str = date_to_string(pub_date);
		publish_date.val(pub_date_str);

	});

	publish_date.on("change", function(ev) {

		<?php if ((!isset($vd->m_content) || !$vd->m_content->is_published) && 
			$vd->content_type == Model_Content::TYPE_PR): ?>

			var pub_date_str = publish_date.val();
			if (!pub_date_str.length) return;
			var pub_date = string_to_date(pub_date_str);
			if (is_publish_date_msg_shown) return;
			var now = string_to_date(<?= json_encode((string) Date::out()) ?>);
			var diff_hours = (pub_date - now) / 36e5;

			if (diff_hours >= -2 && diff_hours <= 4) {
				bootbox.alert("Press release submissions have an average processing time of 2-4 hours");
				is_publish_date_msg_shown = true;
			}

		<?php endif ?>

	});
	
});

</script>
<?php if ($this->newsroom->timezone): ?>
<p class="smaller help-block ta-right">
	<?= $vd->esc(TimeZone::common_name($this->newsroom->timezone)) ?>
	(<a target="_blank" href="manage/newsroom/company" class="status-info-muted">edit</a>)
</p>
<?php endif ?>