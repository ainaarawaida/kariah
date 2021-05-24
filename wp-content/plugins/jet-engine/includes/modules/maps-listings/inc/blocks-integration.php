<?php
namespace Jet_Engine\Modules\Maps_Listings;

class Blocks_Integration {

	/**
	 * Constructor for the class
	 */
	public function __construct() {
		add_action( 'jet-engine/blocks-views/register-block-types', array( $this, 'register_block_types' ) );
		add_filter( 'jet-engine/blocks-views/editor/config',        array( $this, 'add_editor_config' ) );
	}

	/**
	 * Register block types
	 *
	 * @param  object $blocks_types
	 * @return void
	 */
	public function register_block_types( $blocks_types ) {
		require jet_engine()->modules->modules_path( 'maps-listings/inc/blocks-types/maps-listings.php' );

		$maps_listing_type = new Maps_Listing_Blocks_Views_Type();

		$blocks_types->register_block_type( $maps_listing_type );
	}

	/**
	 * Add editor config.
	 *
	 * @param  array $config
	 * @return array
	 */
	public function add_editor_config( $config = array() ) {

		$config['atts']['mapsListing'] = jet_engine()->blocks_views->block_types->get_block_atts( 'maps-listing' );

		return $config;
	}

}
