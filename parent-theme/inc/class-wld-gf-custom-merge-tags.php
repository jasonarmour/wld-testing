<?php

class WLD_GF_Custom_Merge_Tags {
	public static function init() : void {
		add_filter(
			'gform_custom_merge_tags',
			array( static::class, 'add' )
		);
		add_filter(
			'gform_replace_merge_tags',
			array( static::class, 'replace' )
		);
		add_filter(
			'gform_notification_disable_from_warning',
			array( static::class, 'disable_from_warning' )
		);
	}

	public static function add( array $merge_tags ) : array {
		$merge_tags[] = array(
			'label' => esc_html__( 'Domain', 'parent-theme' ),
			'tag'   => '{domain}',
		);
		$merge_tags[] = array(
			'label' => esc_html__( 'Site Name', 'parent-theme' ),
			'tag'   => '{site_name}',
		);
		$merge_tags[] = array(
			'label' => esc_html__( 'No Reply Email', 'parent-theme' ),
			'tag'   => '{no_reply_email}',
		);

		return $merge_tags;
	}

	public static function replace( string $text ) : string {
		$domain         = str_replace( array( 'https://', 'www.' ), '', home_url( '', 'https' ) );
		$site_name      = wp_strip_all_tags( get_bloginfo( 'name' ) );
		$no_reply_email = 'noreply@' . $domain;

		return str_replace(
			array( '{domain}', '{site_name}', '{no_reply_email}' ),
			array( $domain, $site_name ),
			$text
		);
	}

	public static function disable_from_warning() : bool {
		return true;
	}
}
