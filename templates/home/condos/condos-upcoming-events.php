<?php

/**
 * Template Part: Coming Soon
 *
 */

$query = get_transient( 'condos_query_upcoming_events' );

//if( $query === false ) {

    $query = new WP_Query( array(
        'post_type' => 'project',
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'key'     => 'calendly_upcoming_events',
                'compare' => 'EXISTS',
            ),
        ),
    ) );

    set_transient( 'condos_query_upcoming_events', $query, 1 * HOUR_IN_SECONDS );
//}

if ( $query->have_posts() ): ?>
<?php
// prepare all actual events, order them by start date grouped by project
// check there are events (in future)
    $has_future_events = false;
    $grouped_events = array();
    $transient_changed = false;
    $now = strtotime('now');

    while ( $query->have_posts() ): $query->the_post();
        $project_post_id = get_the_ID();
        $grouped_events[$project_post_id] = array();
        $events= get_post_meta($project_post_id, 'calendly_upcoming_events', true);


        // TODO: handle multi-days events
        foreach ($events as $event){
            if($now < strtotime($event['end_time'])){
                $has_future_events = true;
                $grouped_events[$project_post_id][] = $event;
            }
        }

        if(count($grouped_events[$project_post_id]) == 0){
            delete_post_meta($project_post_id, 'calendly_upcoming_events');
            $transient_changed = true;
        }
        else if(count($grouped_events[$project_post_id]) < count($events)){
            update_post_meta($project_post_id, 'calendly_upcoming_events', $grouped_events[$project_post_id]);
            $transient_changed = true;
        }

    endwhile;

    if($transient_changed)
        delete_transient('condos_query_upcoming_events');

    if($has_future_events == false)
        return;
    rewind_posts();


    foreach ($grouped_events as $project_post_id => $events){
        $ref_column[$project_post_id] = $events[0]['start_time'];
    }
    array_multisort($ref_column, SORT_ASC, $grouped_events);


    ?>
    <link href="https://assets.calendly.com/assets/external/widget.css" rel="stylesheet">
    <script src="https://assets.calendly.com/assets/external/widget.js" type="text/javascript"></script>

    <div class="condos-group" data-animation="fadeIn">
        <header>
            <h1 class="heading">Upcoming Events</h1>
        </header>

        <div id="condos-slider--soon" class="project-carousel condos-slider gscroll">
            <div class="condos-slider__scroller">
                <?php foreach ( $grouped_events as $project_events ): ?>
                <?php
                    $project_id = $project_events[0]['linked_project'];

                    global $post;
                    $post = get_post($project_id);
                    setup_postdata( $post );
                    global $events;
                    $events = array_slice($project_events, 0, 3);

                ?>
                    <div class='event-group-container'>
                        <div class='event-group'>
                            <a href="<?php the_permalink(); ?>"><h4><?=get_the_title()?></h4></a>
                            <?php get_template_part('templates/project/card-events-list'); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php wp_reset_postdata(); ?>
            </div>
        </div>

    </div>

    <?php wp_reset_query(); ?>

<?php endif; ?>
