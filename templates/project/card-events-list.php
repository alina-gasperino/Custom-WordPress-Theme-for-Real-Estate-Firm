<?php

$old_timezone = date_default_timezone_get();
date_default_timezone_set('America/New_York');

//$events = get_post_meta(get_the_ID(), 'calendly_upcoming_events', true);
global $events;
global $post;

foreach ($events as $event){
?>
    <div class="event">
        <div class="event_calendar" onclick="Calendly.initPopupWidget({url: '<?=$event['calendly_event_full_link']?>?hide_event_type_details=1&background_color=fffffe&text_color=494c52&primary_color=283740'});return false;">
            <span class="month"><?=date('F', strtotime($event['start_time']))?></span>
            <span class="day"><?=date('j', strtotime($event['start_time']))?></span>
        </div>
        <div class="event_info">
            <div class="title"><?=get_the_title($post)?> - Platinum VIP Booking Event</div>
            <div class="sub-title"><?=date('l F d, '.formatTimeAmPm($event['start_time']), strtotime($event['start_time']))?> - <?=date(formatTimeAmPm($event['end_time']), strtotime($event['end_time']))?></div>
        </div>
        <div class="event_actions">
            <a href="" onclick="Calendly.initPopupWidget({url: '<?=$event['calendly_event_full_link']?>?hide_event_type_details=1&background_color=fffffe&text_color=494c52&primary_color=283740'});return false;"> <i class='fa fa-check' aria-hidden="true" ></i>Reserve Your Spot</a>
        </div>
    </div>
<?php }
date_default_timezone_set($old_timezone);
?>