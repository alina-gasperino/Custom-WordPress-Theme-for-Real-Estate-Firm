<script type="text/javascript">
(function() {
'use strict';
	jQuery(document).ready(function($) {

		$('.quickview-gallery').on('click', '.quick-view', function(e) {
			e.preventDefault();
			var $this = $(this);
			var $gallery = $this.closest('.quickview-gallery');
			var $floorplan = $this.closest('.floorplan');
			var items = [];
			var $floorplans = $gallery.find('.floorplan:visible');
			$floorplans.each( function( index, row ) {
				var $this = $(this);
				if ( ! ($this.attr('data-fullimage') && $this.attr('data-thumbnail'))) return;

				var caption = "";
				caption += '<div class="floorplan ' + $this.attr('data-availability') + '">';
				caption += '<a href="' + $this.attr('data-floorplan-url') + '">' + $this.attr('data-suite-name') + '</a>';
				caption += ' <span class="separator">at</span> ';
				caption += '<a href="' + $this.attr('data-project-url') + '">' + $this.attr('data-projectname') + '</a>';
				caption += '<br>';
				if( $this.attr('data-availability') == 'sold-out' ) {
                    caption += ' Sold Out ';
                } else {
                    caption += ' From $' + parseFloat($this.attr('data-price')).toLocaleString();
                }
				caption += '<br>';
				caption += $this.attr('data-size') + 'sq.ft.';
				caption += ' <span class="separator"></span> ';
				caption += $this.attr('data-beds') + ' Bed ' + $this.attr('data-baths') + ' Bath ' + $this.attr('data-exposure');
				caption += '</div>';

				var item = {
					type: 'image',
					src: $this.attr('data-fullimage'),
					thumb: $this.attr('data-thumbnail'),
					opts: {
						caption: caption
					}
				};
				items.push(item);
			});

			if (!items.length) return;

			var index;
			console.log(items);
			var opts = {};

			$.fancybox.open( items, opts, $floorplans.index( $floorplan.get(0) ) );

		});
	});
}());
</script>
