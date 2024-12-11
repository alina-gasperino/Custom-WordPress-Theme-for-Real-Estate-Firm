<?php $posts_per_page = 3 ?>
<?php if ($results = get_project_updates(get_the_ID(), $posts_per_page)): ?>
<div class="card latest__updates panel panel-default">
	<div class="panel-heading" role="tab" id="collapseLPUpdatesHeading">

		<h2 class="panel-title">
			<a class="collapsed" role="button" data-toggle="collapse" href="#collapseLPUpdates" aria-expanded="true" aria-controls="collapseLPUpdates" itemprop="name">
				Latest Project Updates
			</a>
		</h2>

		<button role="button" class="panel-toggle" data-toggle="collapse" href="#collapseLPUpdates" aria-expanded="true" aria-controls="collapseLPUpdates">
			<i class="fa fa-caret-up"></i>Hide
		</button>

	</div>
	<div id="collapseLPUpdates" class="card__content panel-collapse collapse in">
		<?php while ($results->have_posts()): $results->the_post(); ?>
			<?php get_template_part( 'templates/template', 'related-project'); ?>
		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>
	</div>
	<?php if ($results->max_num_pages > 1): ?>
		<a class="loadmore" data-count="<?php echo $posts_per_page ?>" data-projectid="<?php the_ID() ?>" data-action="card_latest_project_updates" data-target=".card__content" data-paged="2">Load More...</a>
	<?php endif; ?>
</div>
<?php endif; ?>

<script type="text/javascript">
jQuery(function($){
	var $card = $('.card.latest__updates');
	var $button = $card.find('.loadmore');
	$button.on('click', function(e) {
		e.preventDefault();
		$this = $(this);
		if ($this.hasClass('disabled')) return;

		$this.addClass('loading')
			.html('<i class="fa fa-large fa-spinner fa-spin"></i> Loading...');

		$.ajax({
			url: ajax_url,
			dataType: 'html',
			data: {
				action: $this.data('action'),
				projectid: $this.data('projectid'),
				count: $this.data('count'),
				paged: $this.data('paged')
			}
		}).done(function(response){
			$this.removeClass('loading').html('Load More...');
			if (response === '') {
				$this.removeClass('loading').html('No More Results').addClass('disabled');
			} else {
				$card.find('.card__content').append(response);
				$this.data('paged', $this.data('paged')+1);
			}
		});
	});
});
</script>