<?php
$floorplansfolder = get_post_meta( $post->ID, 'floorplansfolder', true);

$googledrive = (strpos($floorplansfolder, 'drive.google.com') !== false);
if ($googledrive) {
	$a = parse_url($floorplansfolder, PHP_URL_QUERY);
	parse_str($a, $b);
	$id = $b['id'];
}
?>

<h1>Floor Plans for <?php the_title() ?></h1>

<?php if ($googledrive && $id): ?>
	<iframe src="https://drive.google.com/embeddedfolderview?id=<?php echo $id ?>#list" width="800" height="600" frameborder="0"></iframe>
<?php else: ?>
	<h3><?php echo $floorplansfolder ?></h3>
<?php endif; ?>
