<?php $maybe_events = get_post_meta(get_the_ID(), 'calendly_upcoming_events', true); ?>
<?php if ($maybe_events):
    global $events;
    $events = $events = array_slice($maybe_events, 0, 5);
?>
    <link href="https://assets.calendly.com/assets/external/widget.css" rel="stylesheet">
    <script src="https://assets.calendly.com/assets/external/widget.js" type="text/javascript"></script>

    <div class="panel panel-default">

        <div class="panel-heading" role="tab" id="collapseEventsHeading">

            <h2 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" href="#collapseEvents" aria-expanded="true" aria-controls="collapseEvents" itemprop="name">
                    <?php the_title() ?> - Upcoming Events
                </a>
            </h2>

            <button role="button" class="panel-toggle" data-toggle="collapse" href="#collapseEvents" aria-expanded="true" aria-controls="collapseEvents">
                <i class="fa fa-caret-up"></i>Hide
            </button>

        </div>

        <div id="collapseEvents" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="collapseEventsHeading">

            <div class="panel-body">

                <?php get_template_part( 'templates/project/card-events-list' ); ?>

            </div>

        </div>

    </div>

<?php ?>

<?php endif; ?>
