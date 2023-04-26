<?php
/**
 * Template Name: Flexible Content
 */

get_header();

if ( have_posts() ) {
	the_post();
	if ( post_password_required() ) {
		echo get_the_password_form();
	} else {
		$need_run = apply_filters( 'wld_need_run_content', true );

		if ( $need_run ) {
			do_action( 'wld_run_content' );
			if ( have_rows( 'content' ) ) {
				while ( have_rows( 'content' ) ) {
					the_row();
					WLD_ACF_Flex_Content::the_content();
				}
			}
			do_action( 'wld_end_content' );
		} else {
			do_action( 'wld_not_need_run_content' );
		}
	}
}

get_footer();
