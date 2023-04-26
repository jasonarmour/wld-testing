<?php
WLD_WC_BI_Single_Product::init();
WLD_WC_BI_Loop::init();
WLD_WC_BI_Meta::init();
WLD_WC_BI_Acf::init();
WLD_WC_Checkout_Blocks::init();
WLD_WC_ACF_Location::init();
WLD_WC_Cart::init();
WLD_WC_Fields::init();
WLD_WC_Image_Sizes::init();
WLD_WC_My_Account::init();
WLD_WC_Sidebar::init( true );
WLD_WC_Cart_In_Menu::init();
WLD_WC_Global::init();
WLD_WC_BI_Product_Filters::init();

WLD_WC_Image_Sizes::init(
	array(
		'single' => array(
			'width'  => 770,
			'height' => 434,
			'crop'   => 0,
		),
	)
);

add_filter(
	'wp_get_attachment_metadata',
	static function ( $data ) {
		if ( doing_filter( 'wp_get_attachment_image_src' ) && count( $data ) === 1 ) {
			return array();
		}

		return $data;
	}
);



/**
* Source: https://github.com/woocommerce/woocommerce/wiki/Product-CSV-Importer-&-Exporter#adding-custom-import-columns-developers
**/
/**
 * Register the 'Custom Column' column in the importer.
 *
 * @param array $options
 * @return array $options
 */
function add_column_to_importer( $options ) {
	// column slug => column name
	$options['topic'] = 'Topic';
  $options['focus'] = 'Focus';
	return $options;
}

add_filter( 'woocommerce_csv_product_import_mapping_options', 'add_column_to_importer' );
/**
 * Add automatic mapping support for 'Custom Column'.
 * This will automatically select the correct mapping for columns named 'Custom Column' or 'custom column'.
 *
 * @param array $columns
 * @return array $columns
 */
function add_column_to_mapping_screen( $columns ) {

	// potential column name => column slug
	$columns['Topic'] = 'topic';
	$columns['topic'] = 'topic';
  $columns['Focus'] = 'focus';
	$columns['focus'] = 'focus';
	return $columns;
}
add_filter( 'woocommerce_csv_product_import_mapping_default_columns', 'add_column_to_mapping_screen' );



/**
 * Set taxonomy.
 *
 * @param  array  $parsed_data
 * @return array
 */
 function woocommerce_add_custom_taxonomy( $product, $data ) {

  // set a variable with your custom taxonomy slug
  $custom_taxonomies = array('topic', 'focus');

	if ( is_a( $product, 'WC_Product' ) ) {
		foreach ($custom_taxonomies as $custom_taxonomy) {
			if( ! empty( $data[ $custom_taxonomy ] ) ) {
				$product->save();
				$custom_taxonomy_values = $data[ $custom_taxonomy ];
				$custom_taxonomy_values = explode(",", $custom_taxonomy_values);
				$terms = array();
				foreach($custom_taxonomy_values as $custom_taxonomy_value){
						if(!get_term_by('name', $custom_taxonomy_value, $custom_taxonomy)){
									$custom_taxonomy_args= array(
											'cat_name' => $custom_taxonomy_value,
											'taxonomy' => $custom_taxonomy,
									);
									$custom_taxonomy_value_cat = wp_insert_category($custom_taxonomy_args);
									array_push($terms, $custom_taxonomy_value_cat);
						}else{
									$custom_taxonomy_value_cat = get_term_by('name', $custom_taxonomy_value, $custom_taxonomy)->term_id;
									array_push($terms, $custom_taxonomy_value_cat);
						}
				}
				wp_set_object_terms( $product->get_id(),  $terms, $custom_taxonomy );
			}
		}
	}
	return $product;
}
add_filter( 'woocommerce_product_import_inserted_product_object', 'woocommerce_add_custom_taxonomy', 10, 2 );
