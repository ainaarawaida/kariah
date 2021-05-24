<?php
namespace Jet_Engine\Query_Builder\Listings;

use Jet_Engine\Query_Builder\Manager as Query_Manager;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Query {

	public $source;
	public $source_meta;

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		$this->source      = Query_Manager::instance()->listings->source;
		$this->source_meta = Query_Manager::instance()->listings->source_meta;

		add_filter( 'jet-engine/listing/grid/query/' . $this->source, array( $this, 'query_items' ), 10, 3 );

		add_action( 'jet-engine/listings/frontend/reset-data', function( $data ) {
			if ( $this->source === $data->get_listing_source() ) {
				wp_reset_postdata();
			}
		} );

	}

	public function query_items( $items, $settings, $widget ) {

		$listing_id = jet_engine()->listings->data->get_listing()->get_main_id();

		if ( ! $listing_id ) {
			return array();
		}

		$query_id = get_post_meta( $listing_id, $this->source_meta, true );

		if ( ! empty( $settings['custom_query'] ) && ! empty( $settings['custom_query_id'] ) ) {
			$query_id = absint( $settings['custom_query_id'] );
		}

		if ( ! $query_id ) {
			return array();
		}

		$query = Query_Manager::instance()->get_query_by_id( $query_id );

		if ( ! $query ) {
			return array();
		}

		$query->setup_query();

		do_action( 'jet-engine/query-builder/listings/on-query', $query, $settings, $widget, $this );

		$widget->query_vars['page']    = $query->get_current_items_page();
		$widget->query_vars['pages']   = $query->get_items_pages_count();
		$widget->query_vars['request'] = array( 'query_id' => $query_id );

		return $query->get_items();

	}

}
