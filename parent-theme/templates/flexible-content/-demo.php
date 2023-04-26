<?php

// Naming
// Blocks should be tried not to be named according to content, for example, our works or about us,
// but according to structural elements.
// For example, if there is textual content and an image on the side, then you can name the "Text & Image" file
// text-image.php, but not always possible, then according to the content.
// If the block consists of separate repeating elements, then it is better to call it blocks- and some
// modifier, if these are links to internal pages, then the "Blocks: Pages" file is blocks-pages.php or
// for example, there are company employees then the "Blocks: Team" file blocks-team.php.
// If a slider is present in the block, then it is better to add a slider to the name, for example, "Testimonials Slider"
// file testimonials-slider.php
// There are also well-established names "Banner", "Title Page", "Logos", "Gallery"

// Single fields
$bg       = wld_get_sub_bg( 'background' ); // Use field "Image"
$title    = get_sub_field( 'title' ); // Use field "Title"
$subtitle = get_sub_field( 'subtitle' ); // Use field "Title"
$text     = get_sub_field( 'text' ); // Use field "WYSIWYG" or field "Textarea"
$image    = wld_get_sub_image( 'image', '100x100' ); //  Use field "Image". Be sure to specify the image size
$link     = wld_get_sub_link( 'link' ); // Use field "Link"
$button   = wld_get_sub_link( 'button', 'btn btn-red' ); // Use field "Link"
$form     = wld_get_sub_form( 'form' ); // Use field "Forms"
$menu     = wld_get_sub_menu( 'menu' ); // Use field "Menu"

// Modifications (field "Group Buttons")
// Used to change the block, no need to cling to the page class, you always need to start from
// block modifiers. If the layout designer is hooked on the page class, ask for a modifier.
$type  = get_sub_field( 'type' ); // for example: vertical horizontal ...
$width = get_sub_field( 'width' ); // wide narrow ...
$align = get_sub_field( 'image_align' ); // image-left image-right ...
$color = get_sub_field( 'colorize' ); // dark-bg white-bg ...
$class = implode( ' ', [ $type, $width, $align, $color ] );

// Relations
// It is best to add true_false "All", and if false, display the relationship field in the admin panel.
// This design is not suitable for all types, well suited for reviews, teams, but for example for pages
// it’s better to create a repeater with the necessary fields, and not rely on the fields of the page itself. One side
// It’s good when you can select a post and display its fields, but as practice shows, often at different
// pages use different descriptions, pictures, etc. in the end it turns out you need to create fields for
// type for different options and with confusing names. In general, if you are sure that the blocks will have
// everywhere the same information do relationship, if not repeater
if ( get_sub_field( 'all' ) ) {
	$posts = get_posts(
		array(
			'post_type'      => 'testimonial',
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
			'orderby'        => 'menu_order',
			'order'          => 'asc',
		)
	);
} else {
	$posts = get_sub_field( 'posts' );
}

// Footer
// Often in front of the footer there is one, two blocks that are repeated on all pages. Once upon a time we had
// the standard is that the blocks in the basement should be editable for each page,
// but now it seems we are moving away from this.
// There are three approaches. To do just blocks, this will allow the use of such blocks anywhere, with
// different contents or not to use at all, but you have to configure on each page. Or make
// theme settings and pull from there, plus you need to add a switch so that you can turn it off
// for a specific page. And the third combined create a block and create a default content for it
// in the theme settings. In general, ask Natasha until there is a clearer standard :)

// Archives & single
// You need to look at the design if it is not block or contains a couple of blocks (header + content),
// then it’s better to just create templates for custom types and fields for them. And if the design is block,
// moreover, blocks with regular pages, then you need to connect the flex template to the template files and
// specify these custom in its settings types.

// Breadcrumbs
// I think it's better to use just a switch in the template / block. As a rule, crumbs are in one place
// pages and it makes no sense to move them somewhere. There is no standard yet.
// Images
// The whole image must be customizable, all these are backgrounds, icons, just pictures, SVG I don’t even know what
// yet, in general, everything. And all need to specify the size. But of course there are exceptions, but very rarely.
// Indicate the size depending on the situation, focusing on layout,
// usually need to be limited in width, but in repeating blocks it is better
// set width + height + cover, and in the slider of small logos and icons width + height + FALSE.
// For backgrounds, the default size is 1920x0, as a rule, for large blocks it can not be changed, but sometimes it can
// you will need to limit the size, but this is rare and you need to look at the situation.
//
?>
<!-- The section will automatically be id = section- $ i so that it can be referenced in a link -->
<!--
The page should have one <H1> heading. But as a rule, a manager can assemble a page from blocks into
which in HTML was not <H1> or vice versa, the manager can use two sides with <H1>.
In order to display only the first heading <H1> and lower the rest, use the wld_get_the_title ($ title, $ level, $ class) function to display the heading, and the main heading in the block should always be the first level, even if it is the second in the layout. On the first call to this function, <H1> will be displayed, and on all subsequent calls, the headers will be lowered.
And in this regard, if the layout designer is hooked on a tag, you need to ask him to hook on a class.
-->
<section class="block-name <?php echo $class; ?>" <?php echo $bg; ?>>
	<div class="inner">
		<div class="wrap text-content">
			<?php if ( $title ) : ?>
				<!-- Headings of all levels do not need to be displayed if they are not filled -->
				<!--
				Sometimes in the headings, and in the text, there are <strong>, then you can use in the field
				square brackets, and in the code, the wld_replace function will replace them with <strong>.
				It is better to specify [text] in the description of such a field - bold style
				-->
				<h1 class="section-title"><?php echo wld_get_the_replace( $title ); ?></h1>
			<?php endif; ?>
			<?php if ( $subtitle ) : ?>
				<h2 class="subtitle"><?php echo $subtitle; ?></h2>
			<?php endif; ?>
			<!-- The headings above are for example, but it’s NOT NECESSARY to display :) -->
			<!-- That's it -->
			<?php wld_the_title( $title, 'section-title' ); ?>
			<?php wld_the_title( $subtitle, 'subtitle' ); ?>
			<!-- As a rule, this is WYSIWYG, but if it is textarea then in its settings you need to wrap in <p> -->
			<?php echo $text; ?>
			<!-- If suddenly the text from another field does not turn into <p> in ACF, you need to wrap it here -->
			<?php if ( $text ) : ?>
				<!-- Text in blocks always needs to be wrapped in <p>, just like that text should not lie! -->
				<!-- If the text in HTML is just text, ask the typesetter to wrap it in <p> -->
				<p><?php echo $text; ?></p>
			<?php endif; ?>
		</div>
		<?php if ( $image ) : ?>
			<!-- As a rule, if the picture has a wrapper, then the wrapper must be hidden if there is no picture -->
			<!-- As a rule, in such blocks the size is indicated only in width. -->
			<div class="image"><?php echo $image; ?></div>
		<?php endif; ?>
		<!--
		All links / buttons should open in a new window if they lead to external resources or to files
		Now it is implemented in init.js, if suddenly not, ask the layout designer to add it.
		-->
		<?php if ( $link ) : ?>
			<!-- As a rule, if the link has a wrapper, then the wrapper needs to be hidden if there is no link -->
			<div class="links">
				<?php echo $link; ?>
			</div>
		<?php endif; ?>
		<?php if ( $button ) : ?>
			<!-- As a rule, if the button has a wrapper, then the wrapper must be hidden if there are no buttons -->
			<div class="btns">
				<?php echo $button; ?>
			</div>
		<?php endif; ?>
		<?php if ( have_rows( 'buttons' ) ) : ?>
			<!-- As a rule, you need to hide the wrapper if there are no buttons -->
			<div class="btns">
				<?php while ( have_rows( 'buttons' ) ) : ?>
					<?php
					the_row();
					$style = get_sub_field( 'style' ); // Use ACF field "Group Buttons" or "Checkboxes"
					$class = implode( ' ', (array) $style ); // If multiple choices are allowed

					wld_the_sub_link( 'btn ' . $class );
					?>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
		<?php if ( $posts ) : ?>
			<!-- As a rule, you need to hide the wrapper if there are no posts -->
			<div class="wrap">
				<!-- !!! The variable must be called $post !!! -->
				<?php foreach ( $posts as $post ) : ?>
					<?php
					setup_postdata( $post );
					// Remember that you need to use get_field, not get_sub_field
					$info  = get_field( 'info' );
					$icon  = wld_get_image( 'icon', '30x30' );
					$bg    = wld_get_bg( 'background', '500x0' );
					$title = get_the_title();
					?>
					<div class="item" <?php echo $bg; ?>>
						<?php if ( $icon ) : ?>
							<div class="icon"><?php echo $icon; ?></div>
						<?php endif; ?>
						<div class="image">
							<!-- Here the wrapper is sometimes needed, even if there is no image -->
							<!-- Be sure to specify the image size -->
							<!-- The size in such blocks is usually "cover" -->
							<?php the_post_thumbnail( '100x100' ); ?>
						</div>
						<?php wld_the_title( $title, 'title' ); ?>
						<!--
						This function truncates text without trimming a word and takes text from ACF if there
						is no editor or excerpt, there are other settings, but in general it is not very successful :(
						-->
						<div class="text"><?php wld_the_excerpt( 120 ); ?></div>
						<?php if ( $info ) : ?>
							<div class="text"><?php echo $info; ?></div>
						<?php endif; ?>
						<a href="<?php the_permalink(); ?>">Read More...</a>
					</div>
				<?php endforeach; ?>
				<!-- !!! Be sure to reset the data for $post !!! -->
				<?php wp_reset_postdata(); ?>
			</div>
		<?php endif; ?>
		<?php if ( have_rows( 'logos' ) ) : ?>
			<!-- As a rule, you need to hide the wrapper if there are no elements -->
			<div class="wrap">
				<?php while ( have_rows( 'logos' ) ) : ?>
					<?php
					the_row();
					$image = wld_get_sub_image( 'image', '100x100' );
					// Here it would be possible to use the URL field for the link, but sometimes you need to hang
					// on the link class for tracking or set target = blank.
					$link = get_sub_field( 'link' );
					?>
					<div class="logo">
						<!-- Sometimes you need to wrap the content in a link, you can do it like this -->
						<!-- As a rule, in such blocks the width and height are indicated in size. -->
						<?php wld_the_maybe_link( $image, $link ); ?>
					</div>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
		<!-- If there are two columns with the same content, then two groups can be made and displayed as follows. -->
		<?php if ( have_rows( 'left' ) || have_rows( 'right' ) ) : ?>
			<div class="wrap">
				<?php foreach ( array( 'left', 'right' ) as $col_name ) : ?>
					<?php if ( have_rows( $col_name ) ) : ?>
						<?php while ( have_rows( $col_name ) ) : ?>
							<?php the_row(); ?>
							<div class="<?php echo $col_name; ?>">
								<?php the_sub_field( 'text' ); ?>
							</div>
						<?php endwhile; ?>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<?php if ( $form ) : ?>
			<!--
			Standard / simple forms of sending letters should after sending reboots.
			Now it is implemented in init.js, if suddenly not, ask the layout designer to add it.
			For such forms, Invisible reCaptcha must be enabled.
			-->
			<div class="form">
				<?php echo $form; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
<!--
In fact, WLD_ACF_Flex_Content will try to clear empty wrappers with the text-content | image | wrap | btns classes
and remove the empty headers from the blocks, but still you need to remember that if there is no content,
then the wrapper is better to remove.
-->
