<?php
$map = get_sub_field( 'map' ); // Use field "Google Map" Field
?>
<div class="map-block">
	<div class="inner">
		<?php if ( $map ) : ?>
			<div data-latitude="<?php echo $map['lat']; ?>"
				 data-longitude="<?php echo $map['lng']; ?>"
				 data-zoom="<?php echo $map['zoom']; ?>"
				 data-icon="<?php echo get_theme_file_uri( 'images/marker.png' ); ?>"
				 class="map-canvas">
			</div>
		<?php endif; ?>
	</div>
</div>
