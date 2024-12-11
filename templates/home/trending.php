<?php 

/**
 * Template Part: Trending Projects
 */

$projects = new WP_Query([
	'post_type' => 'project',
	'posts_per_page' => 5,
	'orderby' => ['id', 'desc'],
]); ?>

<?php if ($projects->have_posts()): ?>

	<section class="trending" data-animation="fadeIn">
		<h3>
			<span class="tooltip-container">
				<i class='fa fa-question-circle-o'></i><span class="tooltip">Based on 24 hours of traffic</span>
			</span> Top Trending</h3>
		<ul>
		<?php while ($projects->have_posts()): $projects->the_post(); ?>
			<li><a href="<?php echo the_permalink() ?>"><?php the_title() ?></a></li>
		<?php endwhile ?>
		</ul>
	</section>

	<?php wp_reset_query(); ?>

<?php endif; ?>
