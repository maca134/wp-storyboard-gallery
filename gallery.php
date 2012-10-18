
<button id="wpstoryboardgallery_showplayer">Open Gallery</button>
<script>
jQuery(function($) {
	$('#wpstoryboardgallery_container').appendTo('body');
	storyboard.animateTime = <?php echo wpsbg_of_get_option('wpstoryboardgallery_animate_time', 2000); ?>;
	storyboard.animateEasing = '<?php echo wpsbg_of_get_option('wpstoryboardgallery_gallery_easing', 'easeInOutQuint'); ?>';
	storyboard.advanceSlideNum = <?php echo $advanceslide; ?>;

	storyboard.init();

	$('#wpstoryboardgallery_showplayer').on('click', function () {
		storyboard.show();
	});
	<?php if ($autoopen == 1) { ?>
	setTimeout(function () {
		storyboard.show();
	}, 1000);
	<?php } ?>
});
</script>
<div id="wpstoryboardgallery_container">
	<a href="#" class="wpstoryboardgallery_close">&times;</a>
	<div id="wpstoryboardgallery" class="cf">
	<?php 
	foreach ($gallery as $n => $img) { 
		$mobile = sprintf($timthumb, $img['image'], $mobile_width, 95);
		$medium = sprintf($timthumb, $img['image'], $medium_width, 95);
		$low = sprintf($timthumb, $img['image'], $low_width, 50);
		?>
		<div <?php if (!empty($img['link'])) { ?>class="linkimage" data-url="<?php echo $img['link']; ?>" <?php } ?>>
			<?php if (!empty($img['link'])) { ?><h2><?php echo $img['text']; ?></h2><?php } ?>
			<img width="<?php echo $img['size'][0]; ?>" height="<?php echo $img['size'][1]; ?>" src="<?php echo $low; ?>" 
			data-mobile="<?php echo $mobile; ?>" 
			data-medium="<?php echo $medium; ?>" 
			data-full="<?php echo $img['image']; ?>" 
			data-id="<?php echo $n; ?>">
			<span><?php echo $img['caption']; ?></span>
		</div>
		<?php 
	} 
	?>
	</div>
	<div id="wpstoryboardgallery_pagination">
		<?php /*<ul>
			<li><a href="#" data-slide="prev">&laquo;</a></li><?php 
				for ($i = 0; $i < ceil(count($gallery) / $advanceslide); $i++) { 
					?><li><a href="#" data-slide="<?php echo $i; ?>"<?php if ($i == 0) { ?> class="active"<?php } ?>><?php echo $i + 1; ?></a></li><?php 
				} 
			?><li><a href="#" data-slide="next">&raquo;</li>
		</ul>*/ ?>
	</div>
</div>