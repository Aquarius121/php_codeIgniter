<?php

function generate_thumbnail_link($id)
{
	return "https://img.youtube.com/vi/{$id}/maxresdefault.jpg";
}

?>

<div id="tutorial-video-content">
	<div class="row">
		<div class="col-lg-12 page-title"><h2>Tutorial Videos</h2></div>
	</div>
	<?php foreach ($vd->videos as $video): ?>
		<div class="video-item col-lg-3 col-md-4 col-sm-6 col-xs-12">
			<div class="wrapper container">
				  <div class="col-xs-12 video-image-column">
						<a class='video-link' data-video-id="<?= $video['id'] ?>" data-video-title="<?= $video['title'] ?>">
							 <div class="thumbnail">
								  <img src="<?= generate_thumbnail_link($video['id'])?>" alt="<?= $video['title'] ?>">
								  <div class="preview">
										<i class="fa fa-play-circle-o" aria-hidden="true"></i>
								  </div>
							 </div>
						</a>
				  </div>
				  <div class="col-xs-12 video-info">
						<div class="video-title">
							 <h3><?= $video['title'] ?></h3>
						</div>
						<div class="video-meta">
							 <span class="meta-duration meta-divider"><?= $video['length'] ?></span>
						</div>
				  </div>
				</div>
		</div>
	<?php endforeach ?>
</div>

<script>

$(function() {
	
	var modalId = <?= json_encode($vd->modal_id) ?>;	
	var video_modal = $('#' + modalId);

	$('.video-link').click(function(){	

		var vid = $(this).data('video-id');
		var title = $(this).data('video-title');

		video_modal.find('#modalLabel').html(title);

		var youtube_iframe = $('<div class="has-flash-content">\
			<iframe class="video-youtube" width="804" height="452" \
			src="//www.youtube.com/embed/'+ vid +'?autoplay=1" \
			frameborder="0" allowfullscreen></iframe></div>');

		video_modal.find('h3').html(title);
		video_modal.find('.modal-content').html(youtube_iframe);
		video_modal.modal('show');

	});

	video_modal.on('hidden.bs.modal', function () {
		 $(this).find('.modal-content').html('');
	});

});

</script>