<?php
echo "<hr>";
error_reporting(E_ALL);
ini_set("display_errors", 1);
?>
<link href="https://assets.calendly.com/assets/external/widget.css" rel="stylesheet">
<script src="https://assets.calendly.com/assets/external/widget.js" type="text/javascript"></script>
<link href="https://dev.talkcondo.com/wp-content/themes/talkcondo/assets/css/style.css?ver=1599910355" rel="stylesheet">
<style>
    .event-group{
        display: inline-grid;
        margin: 5px 20px 5px 5px;
    }

    .event-group-container{
        align-content: flex-start;
        vertical-align: super;
    }

    html {
        background-color: transparent;
    }

    .event > .event_calendar > .month {
        background-color: indianred;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        color: white;
        font-size: x-small;
        padding: 3px;
        display: block;
    }
</style>

<?php
//get_template_part('templates/home/condos/condos-upcoming-events');
//
//die();



//get_template_part('templates/home/condos/condos-upcoming-events');
//
//die();

echo "<hr>{$_SERVER['SERVER_NAME']}<br>";
echo "<hr>{$_SERVER['REQUEST_URI']}<br>";

$headers = apache_request_headers();

foreach ($headers as $header => $value) {
    echo "$header: $value <br />\n";
}


$projects_with_events = get_posts(array(
        'post_type' => 'project',
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'key'     => 'calendly_upcoming_events',
                'compare' => 'EXISTS',
            ),
        ),
    )
);

echo "<h3>Upcoming Events</h3>";
echo "<div class='event-group-container'>";




foreach ($projects_with_events as $project){
    global $post;
    $post = $project;
    global $events;
    $events = get_post_meta($post->ID, 'calendly_upcoming_events', true);

    echo "<hr>events:<pre>";
    var_dump($events);
    echo "</pre>";

    ?>
    <div class='event-group'>
        <h4><?=$post->post_title?></h4>
        <?php get_template_part('templates/project/card-events-list'); ?>
    </div>

<?php
}
echo "</div>";


echo "<hr>projects_with_events:<pre>";
var_dump($projects_with_events);
echo "</pre>";
die();





global $post;
$post = get_post('49017');
setup_postdata($post);
$post_id = get_the_ID();




$events = get_post_meta($post_id, 'calendly_upcoming_events', true);


?>


<?php


$event_post_id = '58492';


$event = calendly_get_event($events[0]['calendly_event_slug']);
$event_fields = get_fields( $event_post_id );

    echo "<hr>event_fields:<pre>";
    var_dump($event_fields['calendly_event_slug']);
    var_dump($event_fields);
    echo "</pre>";

$project_events = get_post_meta($post_id, 'calendly_upcoming_events', true);

$old_timezone = date_default_timezone_get();
date_default_timezone_set('America/New_York');

foreach ($events as $event){
?>
<div class="event">
    <div class="event_calendar">
        <span class="month"><?=date('F', strtotime($event['start_time']))?></span>
        <span class="day"><?=date('j', strtotime($event['start_time']))?></span>
    </div>
    <div class="event_info">
        <div class="title">Social Condos - Platinum VIP Booking Event</div>
        <div class="sub-title"><?=date('l F d, '.formatTimeAmPm($event['start_time']), strtotime($event['start_time']))?> - <?=date(formatTimeAmPm($event['end_time']), strtotime($event['end_time']))?></div>
    </div>
    <div class="event_actions">
        <a href="" onclick="Calendly.initPopupWidget({url: '<?=$event['calendly_event_full_link']?>?hide_event_type_details=1&background_color=fffffe&text_color=494c52&primary_color=283740'});return false;"> <i class='fa fa-check' aria-hidden="true" ></i>Reserve Your Spot</a>
    </div>
</div>
<?php }

date_default_timezone_set($old_timezone);
?>

