<?php
$title      = get_sub_field( 'title' );
$text       = get_sub_field( 'text' );
$categories = array();
if ( have_rows( 'items' ) ) {
	the_row();
	$field = get_sub_field_object( 'categories' );
	if ( $field['choices'] ) {
		$categories = $field['choices'];
	}
	reset_rows();
}
?>
<section class="gallery-section">
	<div class="inner">
		<?php wld_the_title( $title, 'section-title' ); ?>
		<div class="text-content"><?php echo $text; ?></div>
		<?php if ( $categories ) : ?>
			<ul class="filter">
				<li class="active">
					<a href="#all">All</a>
				</li>
				<?php foreach ( $categories as $category ) : ?>
					<li><a href="#<?php echo sanitize_title( $category ); ?>"><?php echo $category; ?></a></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<?php if ( have_rows( 'items' ) ) : ?>
			<div class="wrap">
				<?php while ( have_rows( 'items' ) ) : ?>
					<?php
					the_row();
					$id = get_sub_field( 'image', false );
					if ( ! $id ) {
						continue;
					}
					$image      = wld_get_sub_image( 'image', '300x300' );
					$url        = wp_get_attachment_image_url( $id, 'full' );
					$caption    = get_sub_field( 'caption' );
					$categories = array_map( 'sanitize_title', (array) get_sub_field( 'categories' ) );
					?>
					<div class="item all <?php echo esc_attr( implode( ' ', $categories ) ); ?>">
						<?php echo $image; ?>
						<a href="<?php echo esc_url( $url ); ?>"
						   title="<?php echo esc_attr( $caption ); ?>"><?php echo $caption; ?></a>
					</div>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
