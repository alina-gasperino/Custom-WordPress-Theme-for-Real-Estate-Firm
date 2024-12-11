<?php

if ($floorplan['availability'] == 'Available') {
    $availability = 'available';
} elseif ($floorplan['availability'] == 'Sold Out') {
    $availability = 'sold-out';
}

$title = $floorplan['suite_name'];
if ($floorplan['specialty_type']) $title .= " (" . $floorplan['specialty_type'] . ")";
$title = trim_title($title, 26);

// $attachment = get_post($floorplan['image']);
$thumbnail = wp_get_attachment_image_src($floorplan['image'], 'medium');
$fullimage = wp_get_attachment_image_src($floorplan['image'], 'full');

$data_attributes = [
    'fullimage' => $fullimage[0],
    'thumbnail' => $thumbnail[0],
    'price' => $floorplan['price'],
    'formattedprice' => $floorplan['price'] ? number_format($floorplan['price']) : '',
    'beds' => $floorplan['beds'],
    'baths' => $floorplan['baths'],
    'size' => $floorplan['size'],
    'availability' => $availability,
    'suite-name' => $floorplan['suite_name'],
    'project' => get_the_title(),
    'projectname' => get_the_title(),
    'exposure' => @implode('/', $floorplan['exposure']),
    'floorplan-url' => get_floorplans_link($floorplan['image']),
    'project-url' => get_permalink(),
    'reserve-link' => get_floorplans_link($floorplan['image']) . 'reserve',
];

$quick_view_url = admin_url('admin-ajax.php?action=floorplan_quick_view&attachment_id=' . $floorplan['image']);
$quick_view_url = $fullimage[0];

?>

<p>hhh</p>