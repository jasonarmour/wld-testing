<?php


class WLD_ACF_Background_Field extends acf_field_image {
	public function initialize(): void {
		parent::initialize();

		$this->name  = 'background';
		$this->label = __( 'Background', 'parent-theme' );

		add_filter(
			'acf/field_wrapper_attributes',
			array( $this, 'wrapper_attributes' ),
			10,
			2
		);
	}

	public function wrapper_attributes( $wrapper, $field ) {
		$type = $this->name;
		if ( $field['type'] === $type ) {
			$wrapper = array_map(
				static function ( $v ) use ( $type ) {
					return str_replace( $type, 'image', $v );
				},
				$wrapper
			);
		}

		return $wrapper;
	}
}
