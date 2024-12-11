<?php
$year = get_field("launch_year");
$month = get_field("launch_month");
$season = get_field("launch_season");
$day = get_field("launch_day");

//$month = 'Dec';
//$day = null;

$when = "Soon";
$now = new DateTime();
$now_this_month = date('M');
$now_next_month = date('M', strtotime('+1 month'));
$date = $now;
$diff = new DateInterval("P0D");
if ($year && $month) {
    try {
        if ($day) { // we know the day

            $date = DateTime::createFromFormat('Y-M-j', '' . $year . '-' . $month . '-' . $day);
            if ($date->getTimestamp() < $now->getTimestamp()) throw new Error('coming soon date is in past, skipping all details');
            $diff = $date->diff($now);

            if ($diff->y == 0) {
                if ($diff->days > 1) $when = "in " . $diff->days . ' Days';
                elseif ($diff->days == 1) $when = ' Tomorrow';
                elseif ($diff->days == 0) $when = ' Today';
            }
        } else {
            $date = DateTime::createFromFormat('Y-M', '' . $year . '-' . $month);
            if ($date->getTimestamp() < $now->getTimestamp()) throw new Error('coming soon date is in past, skipping all details');
            $diff = $date->diff($now);

            if ($diff->y == 0) {
                if (strtolower($now_this_month) == strtolower($month)) $when = ' This Month';
                elseif (strtolower($now_next_month) == strtolower($month)) $when = ' Next Month';
                elseif ($diff->days > 60) $when = ' in ' . ($diff->m + 1) . ' Months';
            }
        }
    } catch (Throwable $th) {
        $year = $month = $season = $day = null;
//        echo $th->getMessage();
    }
}


//echo "<pre>";
//var_dump($year, $month, $day, $now_this_month, $now_next_month, $now, $date, $diff);
//echo "</pre>";
?>

<div class="launching-soon-announcement">
    <div class="event_calendar">
        <span class="month">&nbsp;<?= $month ?: $season ?>&nbsp;</span>
        <span class="day"><?= $day ?: 'TBA' ?></span>
    </div>
    <h4><?= get_the_title(); ?> is Launching <?= $when ?></h4>

    <div class="event_actions">
        <a href="<?= leadpages_form_button_extract_url(get_field('leadpagesform')) ?>" target="_blank"
           class="btn btn-alt">Pre-Register for First Access</a>
    </div>
</div>