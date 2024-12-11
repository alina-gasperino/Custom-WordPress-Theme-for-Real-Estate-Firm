<?php

/**
 * Template Part: Condos Soon
 *
 * @since 1.4.4
 */

$query = get_transient('condos_query_soon_carousel');

if ($query === false) {

    //TODO: presort
    $query = new WP_Query(array(
        'post_type' => 'project',
        'posts_per_page' => 24,
        'tax_query' => array(
            array(
                'taxonomy' => 'salesstatus',
                'field' => 'slug',
                'terms' => 'launching-soon',
            )
        )
    ));

    set_transient('condos_query_soon_carousel', $query, 1 * MINUTE_IN_SECONDS);

}

/**
 * group projects by year + month | season
 * filter out projects with launch date in past
 */
function cmp($a, $b)
{
    return strcmp($a["sort_str"], $b["sort_str"]);
}

// check there are events (in future)
$has_future_projects = true; // TODO: IMPLEMENT (should be false at init)
$short_months = [1 => 'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
$long_months = [1 => 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October' . 'November', 'December'];
$months_days = [1 => 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
$season_months = [5 => 'spring', 8 => 'summer', 11 => 'fall']; // no winter


$list = [];
$grouped_projects = array();

if ($query->have_posts()):


    $now = (new DateTime())->format('Y-m-d');

    while ($query->have_posts()): $query->the_post();
        global $post;

//        echo "<pre>";
        $p = [];
        $p['title'] = $post->post_title;
        $p['address'] = get_field("address");
        $p['city'] = get_field("city");

        $p['permalink'] = get_the_permalink($post);

        $p['thumb'] = get_template_directory_uri() . '/assets/images/layout/condo-placeholder.jpg';
        $thumbnails = wp_get_attachment_image_src(get_post_thumbnail_id(), 'project_small');
        if (is_array($thumbnails) && !empty($thumbnails) && !is_local())
            $p['thumb'] = $thumbnails[0];

        $p['year'] = get_field("launch_year");
        $p['month'] = get_field("launch_month");
        $p['season'] = get_field("launch_season");
        $p['day'] = get_field("launch_day");
        $p['day_override'] = get_field("launch_day_override");

        // cleaning up edge cases
        if (empty($p['year'])) { // possible case due to acf (day can be set)
            if (!empty($p['season'])) update_field('season', null);
            if (!empty($p['month'])) update_field('month', null);
            if (!empty($p['day'])) update_field('day', null);
            $p['season'] = $p['month'] = $p['day'] = null;
        }

        if (isset($p['month']) && isset($p['season'])) {// just in case
            if (!empty($p['season'])) update_field('season', null);
            $p['season'] = null;
        }

        if (empty($p['month']) && empty($p['season']) && isset($p['day'])) {// possible case due to acf (day can be set)
            if (!empty($p['day'])) update_field('day', null);
            $p['day'] = null;
        }


        // NOTE: this is sorting that should happen in query (by a field created on acf/save)
        // when there is a season - we set date to the last month, last month day
        $year = intval($p['year']) ?: 9999;
        $month = array_search($p['month'], $short_months) ?: array_search($p['season'], $season_months) ?: 99;
        $day = intval($p['day']) ?: 99;

        $earliest_date_string = '' . $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
        $p['sort_str'] = $earliest_date_string;
        if ($now < $p['sort_str']) {
            array_push($list, $p);
        }
    endwhile;


    // sort by "sort_str" asc
    usort($list, 'cmp');

//    echo "<pre>";
//    var_dump($list);
//    echo "</pre>";


    foreach ($list as $p) {
        // prepare data grouped[year|TBA][month|season|TBA][...projects data]

        $group_label = '';

        if (!empty($p['month']))
            $group_label = $long_months[array_search($p['month'], $short_months)];

        if (!empty($p['season']))
            $group_label = ucfirst($p['season']);

        if (!empty($p['year']))
            $group_label .= ' ' . $p['year'];
        else
            $group_label = 'TBA';

        if (empty($grouped_projects[$group_label])) {
            $grouped_projects[$group_label] = [];
        }
        array_push($grouped_projects[$group_label], $p);


//        echo "<pre>";
//        var_dump($p);
//        echo "</pre>";
    }

//    echo "<pre>";
//    global $projects;
//    var_dump($projects);
//    var_dump($grouped_projects['May 2021']);
//    echo "</pre>";

    if ($has_future_projects == false)
        return;

    rewind_posts();


    ?>
    <!--    <link href="https://assets.calendly.com/assets/external/widget.css" rel="stylesheet">-->
    <!--    <script src="https://assets.calendly.com/assets/external/widget.js" type="text/javascript"></script>-->

    <div class="condos-group" data-animation="fadeIn">
        <header>
            <h1 class="heading">Condo Launch
                <Calendar></Calendar>
                (templates/home/condos/condos-soon-carousel.php)
            </h1>
        </header>

        <div id="condos-slider--soon" class="project-carousel condos-slider gscroll">
            <div class="condos-slider__scroller">
                <?php global $projects;
                foreach ($grouped_projects as $group_label => $projects): ?>
                    <div class='condo-event-group-container'>
                        <div class='condo-event-group'>
                            <a href="#"><h4><?= $group_label ?></h4></a>
                            <?php get_template_part('templates/project/card-condo-soon-carousel-list'); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php wp_reset_postdata(); ?>
            </div>
        </div>

    </div>

    <?php wp_reset_query(); ?>

<?php endif; ?>
