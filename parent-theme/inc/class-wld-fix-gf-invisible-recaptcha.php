<?php /** @noinspection PhpUndefinedNamespaceInspection, PhpUndefinedClassInspection */


class WLD_Fix_GF_Invisible_ReCaptcha {
	public static function init() : void {
		if (
			! is_admin() &&
			wld_is_plugin_active( 'invisible-recaptcha/invisible-recaptcha.php' ) &&
			wld_is_plugin_active( 'gravityforms/gravityforms.php' )
		) {
			$cf = InvisibleReCaptcha\Modules\ContactForms\ContactFormsPublicModule::getInstance();
			$gf = InvisibleReCaptcha\Modules\ContactForms\ContactFormsAdminModule::OPTION_GF_PROTECTION_ENABLED;

			if ( $cf->getOption( $gf ) ) {
				add_filter(
					'wld_enqueue_get_theme_object',
					array( static::class, 'set_data_in_theme_object' )
				);
				add_action(
					'wp_enqueue_scripts',
					array( static::class, 'dequeue_invisible_recaptcha_script' ),
					PHP_INT_MAX
				);
				add_filter(
					'gform_validation',
					array( static::class, 'validation_if_there_is_an_invisible_recaptcha_field' )
				);
			}
		}
	}

	public static function set_data_in_theme_object( array $theme ) : array {
		$spm   = InvisibleReCaptcha\Modules\Settings\SettingsPublicModule::getInstance();
		$key   = $spm->getOption( InvisibleReCaptcha\Modules\Settings\SettingsAdminModule::OPTION_SITE_KEY );
		$badge = $spm->getOption( InvisibleReCaptcha\Modules\Settings\SettingsAdminModule::OPTION_BADGE_POSITION );
		$class = InvisibleReCaptcha\Modules\BasePublicModule::RECAPTCHA_HOLDER_CLASS_NAME;

		$theme['reCaptchaInvisibleSiteKey']         = $key;
		$theme['reCaptchaInvisibleBadgePosition']   = $badge;
		$theme['reCaptchaInvisibleHolderClassName'] = '.' . $class;

		if ( WLD_NEVER ) { // The condition is never fulfilled, only for IDE
			?>
			<script>
				window.theme = {
					reCaptchaInvisibleSiteKey: '',
					reCaptchaInvisibleBadgePosition: '',
					reCaptchaInvisibleHolderClassName: ''
				};
			</script>
			<?php
		}

		return $theme;
	}

	public static function dequeue_invisible_recaptcha_script() : void {
		wp_dequeue_script( 'google-invisible-recaptcha' );
	}

	public static function validation_if_there_is_an_invisible_recaptcha_field( array $validation_result ) : array {
		if ( $validation_result['is_valid'] ) {
			$validation_result['is_valid'] = isset( $_POST['g-recaptcha-response'] ); // phpcs:ignore
		}

		return $validation_result;
	}
}
