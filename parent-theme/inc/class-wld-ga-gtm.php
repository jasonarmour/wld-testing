<?php
/**
 * phpcs:disable WordPress.WP.EnqueuedResources
 *
 * @noinspection ES6ConvertVarToLetConst, JSUnresolvedVariable, EqualityComparisonWithCoercionJS,
 *               JSSuspiciousEqPlus, PhpFormatFunctionParametersMismatchInspection
 */

class WLD_GA_GTM {
	private static $ga_id;
	private static $gtag_id;
	private static $gtm_id;

	public static function init() : void {
		add_action(
			'acf/init',
			array( static::class, 'acf_init' )
		);
	}

	public static function acf_init() : void {
		self::$ga_id   = get_field( 'wld_api_ga_id', 'options' );
		self::$gtag_id = get_field( 'wld_api_gtag_id', 'options' );
		self::$gtm_id  = get_field( 'wld_api_gtm_id', 'options' );

		if ( 'production' === wp_get_environment_type() ) {
			if ( self::$ga_id || self::$gtag_id || self::$gtm_id ) {
				add_action( 'wp_head', array( self::class, 'head' ), 1 );
			}
			if ( self::$gtm_id ) {
				add_action( 'wp_body_open', array( self::class, 'body' ), 1 );
			}
		}
	}

	public static function head() : void {
		if ( self::$ga_id ) {
			// Google Analytics https://developers.google.com/analytics/devguides/collection/analyticsjs
			echo sprintf(
				'
				<script>
					window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;
					ga("create","%s","auto");
					ga("send","pageview");
				</script>
				<script async src="https://www.google-analytics.com/analytics.js"></script>',
				self::$ga_id
			);
		}

		if ( self::$gtag_id ) {
			// Global Site Tag https://developers.google.com/gtagjs/devguide/snippet
			echo sprintf(
				'
				<script async src="https://www.googletagmanager.com/gtag/js?id=%s"></script>
				<script>
					window.dataLayer = window.dataLayer || [];
					function gtag(){dataLayer.push(arguments)}
					gtag("js",new Date());
					gtag("config","%s");
				</script>',
				self::$gtag_id,
				self::$gtag_id
			);
		}

		if ( self::$gtm_id ) {
			// Google Tag Manager https://developers.google.com/tag-manager/quickstart
			echo sprintf(
				'
				<script>
					(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({"gtm.start":
					new Date().getTime(),event:"gtm.js"});var f=d.getElementsByTagName(s)[0],
					j=d.createElement(s),dl=l!="dataLayer"?"&l="+l:"";j.async=true;j.src=
					"https://www.googletagmanager.com/gtm.js?id="+i+dl;f.parentNode.insertBefore(j,f);
					})(window,document,"script","dataLayer","%s");
				</script>',
				self::$gtm_id
			);
		}
	}

	public static function body() : void {
		echo sprintf(
			'
			<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=%s"
				height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
			',
			self::$gtm_id
		);
	}
}
