<?php


$old_timezone = date_default_timezone_get();
date_default_timezone_set('America/New_York');

//$events = get_post_meta(get_the_ID(), 'calendly_upcoming_events', true);
global $projects;
global $post;

//echo "<pre>";
//var_dump($projects);
////var_dump($post);
//echo "</pre>";

foreach ($projects as $project) {
//echo "<pre>";
//var_dump($projects);
////var_dump($post);
//echo "</pre>";


    ?>
    <div class="condo-event" href="<?= $project['permalink'] ?>">
        <a href="<?= $project['permalink'] ?>">
            <div class="event_calendar">
                <span class="month">&nbsp;<?= !empty($project["month"]) ? $project["month"] : $project["season"] ?>&nbsp;</span>
                <?php if (!empty($project["day_override"])) { ?>
                    <span class="day-override"><?= $project["day_override"] ?></span>
                <?php } else { ?>
                    <span class="day"><?= $project["day"] ?: 'TBA' ?></span>
                <?php } ?>

            </div>

            <figure>
                <img src="<?= $project['thumb'] ?>">
            </figure>

            <div class="event_info">
                <div class="title"><?= $project['title'] ?></div>
                <div class="sub-title"><?= $project['address'] . ',' . $project['city'] ?></div>
            </div>
        </a>
        <div class="event_actions">
            <a href="<?= $project['permalink'] ?>">Pre-Register</a>
        </div>
    </div>
<?php }
date_default_timezone_set($old_timezone);
?>