<div id="floorplans-slider" class="flexslider floorplans-slider avia-gallery">
	<ul class="slides">
		<?php foreach( $floorplans as $image ): ?>
			<li><a class='' href="<?php echo $image['sizes']['large']; ?>"><img src="<?php echo $image['sizes']['flexslider']; ?>" alt="<?php echo $image['alt']; ?>" /></a></li>
		<?php endforeach; ?>
	</ul>
</div>

<div id="floorplans-carousel" class="flexslider floorplans-carousel">
	<ul class="slides">
		<?php foreach( $floorplans as $image ): ?>
			<li><img src="<?php echo $image['sizes']['flexsliderthumb']; ?>" alt="<?php echo $image['alt']; ?>" /></li>
		<?php endforeach; ?>
	</ul>
</div>
