<section class="section-test" <?php wld_the( 'background', '100x100' ); ?>>
	<div class="inner">
		<?php
		/**
		 * Easy use of the most common fields
		 */
		?>
		<?php wld_the( 'title', 'section-title' ); ?>
		<?php wld_the( 'text' ); ?>
		<?php wld_the( 'image', '100x100' ); ?>
		<?php wld_the( 'link' ); ?>
		<?php wld_the( 'button', 'btn' ); ?>
		<?php wld_the( 'phone' ); ?>
		<?php wld_the( 'email' ); ?>
		<?php wld_the( 'form' ); ?>
		<?php wld_the( 'map' ); ?>
		<?php wld_the( 'menu' ); ?>

		<hr>

		<?php
		/**
		 * If the field is "repeater" or 'flexible_content" then a sub cycle will be set for it
		 *
		 * $wrapper - To wrap elements in one element you can specify in this format
		 */
		?>
		<?php while ( wld_loop( 'items', '<div class="wrap-items">' ) ) : ?>
			<div class="item">
				<?php wld_the( 'title' ); ?>
				<?php wld_the( 'text' ); ?>
			</div>
		<?php endwhile; ?>

		<hr>

		<?php
		/**
		 * If this field is of type "relationship" then the global variable $post will be set
		 *
		 * $wrapper - For more complex element wrapping you can use "%s"
		 */
		?>
		<?php while ( wld_loop( 'posts', '<div class="wrap"><div class="inner">%s</div></div>' ) ) : ?>
			<div class="post">
				<a href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail( '460x320' ); ?>
				</a>
				<h2><?php the_title(); ?></h2>
				<?php wld_the( 'custom_post_field' ); ?>
			</div>
		<?php endwhile; ?>

		<hr>

		<?php
		/**
		 * If the field is of type "link", "wld_contact_link" or value URL then the content will be wrapped in a <a>
		 * If another type then the content will be wrapped in a <div>
		 *
		 * $class - html attribute class
		 * $force - by default, if there is no value, the content will be wrapped in a <div>, this can be disabled
		 * $attrs - you can also add other html attributes for the wrapper the $attrs parameter
		 */
		?>
		<?php while ( wld_wrap( 'link', 'wrap-link', false, array( 'data-i' => 1 ) ) ) : ?>
			<?php wld_the( 'title' ); ?>
			<?php wld_the( 'text' ); ?>
		<?php endwhile; ?>

		<hr>

		<?php
		/**
		 * Checks if there are non-empty fields
		 *
		 * $selectors_and_maybe_conditional - selectors separated by commas, you can also specify
		 * 'AND' as the first argument, in this case all fields must be filled
		 */
		?>
		<?php if ( wld_has( 'title', 'text' ) ) : ?>
			<div class="wrap-title-and-text">
				<?php wld_the( 'title' ); ?>
				<?php wld_the( 'text' ); ?>
			</div>
		<?php endif; ?>

		<hr>

		<?php
		/**
		 * Title field supports:
		 * $class
		 * $wrapper
		 */
		wld_the( 'title', 'section-title', '<div class="wrap-title">' );
		?>

		<?php
		/**
		 * Text, textarea or wysiwyg fields supports:
		 * $wrapper
		 */
		wld_the( 'text', '<div class="wrap-text">' );
		?>

		<?php
		/**
		 * Image field supports:
		 * $size - image must always have a size
		 * $attr - @see wp_get_attachment_image() $attr param
		 * $wrapper
		 */
		wld_the( 'image', '460x320', array( 'class' => 'alignright' ), '<div class="img">' );
		?>

		<?php
		/**
		 * Link field supports:
		 * $class
		 * $empty
		 * $wrapper
		 */
		wld_the( 'link', 'more-link', false, '<div class="wrap-link">' );
		?>

		<?php
		/**
		 * WLD Contact Link field supports:
		 * $class
		 * $wrapper
		 * $attrs
		 */
		wld_the( 'phone', 'phone-link', '<p>', array( 'data-i' => 1 ) );
		?>

		<?php
		/**
		 * Form field supports:
		 * $show_title - whether to show the form header, hide by default
		 * $show_description- whether to show the form description, hide by default
		 */
		wld_the( 'form', true, true );
		?>

		<?php
		/**
		 * Google Map field supports:
		 * $marker - marker icon, should be in the "images" folder of the active theme
		 * $attrs
		 */
		wld_the( 'map', 'marker.png', array( 'data-i' => 1 ) );
		?>

		<?php
		/**
		 * Menu field supports:
		 * $args - @see wp_nav_menu() $args param
		 */
		wld_the( 'menu', array( 'container' => 'nav' ) );
		?>
	</div>
</section>
