<?php

if ( ! function_exists( 'swissdelight_core_register_restaurant_menu_for_meta_options' ) ) {
	/**
	 * Function that add custom post type into global meta box allowed items array for saving meta box options
	 *
	 * @param array $post_types
	 *
	 * @return array
	 */
	function swissdelight_core_register_restaurant_menu_for_meta_options( $post_types ) {
		$post_types[] = 'restaurant-menu';

		return $post_types;
	}

	add_filter( 'qode_framework_filter_meta_box_save', 'swissdelight_core_register_restaurant_menu_for_meta_options' );
	add_filter( 'qode_framework_filter_meta_box_remove', 'swissdelight_core_register_restaurant_menu_for_meta_options' );
}

if ( ! function_exists( 'swissdelight_core_add_restaurant_menu_custom_post_type' ) ) {
	/**
	 * Function that adds restaurant-menu custom post type
	 *
	 * @param array $cpts
	 *
	 * @return array
	 */
	function swissdelight_core_add_restaurant_menu_custom_post_type( $cpts ) {
		$cpts[] = 'SwissDelightCore_Restaurant_Menu_CPT';

		return $cpts;
	}

	add_filter( 'swissdelight_core_filter_register_custom_post_types', 'swissdelight_core_add_restaurant_menu_custom_post_type' );
}

if ( class_exists( 'QodeFrameworkCustomPostType' ) ) {
	class SwissDelightCore_Restaurant_Menu_CPT extends QodeFrameworkCustomPostType {

		public function map_post_type() {
			$name = esc_html__( 'Restaurant Menu', 'swissdelight-core' );
			$this->set_base( 'restaurant-menu' );
			$this->set_menu_position( 10 );
			$this->set_menu_icon( 'dashicons-list-view' );
			$this->set_slug( 'restaurant-menu' );
			$this->set_name( $name );
			$this->set_path( SWISSDELIGHT_CORE_CPT_PATH . '/restaurant-menu' );
			$this->set_labels(
				array(
					'name'          => esc_html__( 'SwissDelight Restaurant Menu', 'swissdelight-core' ),
					'singular_name' => esc_html__( 'Restaurant Menu', 'swissdelight-core' ),
					'add_item'      => esc_html__( 'New Restaurant Menu', 'swissdelight-core' ),
					'add_new_item'  => esc_html__( 'Add New Restaurant Menu', 'swissdelight-core' ),
					'edit_item'     => esc_html__( 'Edit Restaurant Menu', 'swissdelight-core' ),
				)
			);
			$this->set_public( false );
			$this->set_archive( false );
			$this->set_supports(
				array(
					'title',
					'thumbnail',
				)
			);
			$this->add_post_taxonomy(
				array(
					'base'          => 'restaurant-menu-category',
					'slug'          => 'restaurant-menu-category',
					'singular_name' => esc_html__( 'Category', 'swissdelight-core' ),
					'plural_name'   => esc_html__( 'Categories', 'swissdelight-core' ),
				)
			);
		}
	}
}
