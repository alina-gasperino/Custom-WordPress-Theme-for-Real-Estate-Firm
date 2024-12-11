<?php if ($updates = get_project_updates( get_the_ID() )): ?>
<div id='project-updates' class='main_color container_wrap fullsize'>
	<div class="container">
		<a name="project-updates"></a>
		<div class="template-page site-content twelve alpha units">
			<?php $n = 1 ?>
			<div class="togglecontainer enable_toggles">
				<section class='av_toggle_section'>
					<?php while ($updates->have_posts()): $updates->the_post(); ?>
					<p class="clearfix toggler activeTitle" data-fake-id="<?php echo " #toggle-id-$n " ?>">
						<a name="<?php echo $post->post_name ?>" class='offset-anchor'></a>
						<?php the_title(); ?>
						<span class="author">By: <?php the_author() ?> / <?php the_date() ?></span>
						<span class="toggle_icon">
							<span class="vert_icon"></span>
							<span class="hor_icon"></span>
						</span>
					</p>
					<div id="<?php echo " toggle-id-$n-container " ?>" class='toggle_wrap active_tc'>
						<div class="toggle_content invers-color">
							<?php the_content() ?>
						</div>
					</div>
					<?php $n++; ?>
					<?php endwhile; ?>
					<?php wp_reset_query() ?>
				</section>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>
