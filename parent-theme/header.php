<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head><?php wp_head(); ?></head>
<body <?php body_class(); ?>>
<?php
wp_body_open();

WLD_Cache::maybe_cache_template_part( 'templates/header' );

do_action( 'wld_after_header' );
