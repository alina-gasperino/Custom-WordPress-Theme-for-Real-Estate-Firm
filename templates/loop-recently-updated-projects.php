<?php
global $post;

if ($post->post_type == 'project_update') {
	$project = get_post(reset(get_field('project')));
	$title = apply_filters('the_title', $project->post_title );
	$thumb = get_the_post_thumbnail($project->ID, 'portfolio_small');
	$title_link = rtrim( get_the_permalink( $project->ID ), '/');
	$readmore_link = $title_link . '/#' . $post->post_name;
} else {
	$title = get_the_title();
	$thumb = get_the_post_thumbnail($post->ID, 'portfolio_small');
	$title_link = rtrim( get_the_permalink( $post->ID ), '/');
	$readmore_link = $title_link;
}

$regex = '/<a class="readmore"[^>]*>[^<]*<\/a>/';
$replacement = "<a class='readmore' href='$readmore_link'>Read More...</a>";
$excerpt = preg_replace($regex, $replacement, get_the_excerpt());

?>

<div class="project clearfix">
	<div class='thumbnail'>
		<a href='<?php echo $title_link ?>'>
		<?php if ($thumb): ?>
			<?php echo $thumb ?>
		<?php else: ?>
			<img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/thumbnail-placeholder.jpg' ?>" />
		<?php endif; ?>
		<p><?php echo $title ?></p>
		</a>
	</div>
	<div class='update'>
		<h3><a href='<?php echo $readmore_link ?>'><?php the_title() ?></a></h3>

		<div class="post__meta">
			<span class="post__author"><i class="fa fa-fw fa-user"></i> <?php the_author(); ?></span>
			<?php if (get_the_date()): ?>
			<span class="post__date"><i class="fa fa-fw fa-calendar"></i> <?php echo get_the_date(); ?></span>
			<?php endif; ?>
		</div>

		<?php echo $excerpt ?>

	</div>
</div>
