<?php if ($recent_updates = get_recent_updates(get_the_ID())): ?>
<div class="container">
	<div id="recent-updates">
		<span class='badge'><?php echo $recent_updates->post_count ?></span><?php echo ($recent_updates->post_count > 1) ? 'NEW PROJECT UPDATES!' : 'NEW PROJECT UPDATE!'; ?>
		<?php $recent_updates->the_post(); ?>
			<a href='<?php the_permalink() ?>'>Click here to read the latest update: <?php the_title() ?></a>
		<?php wp_reset_postdata(); ?>
	</div>
</div>
<?php endif; ?>

