<?php
namespace Jet_Engine\Query_Builder\Listings;

class Filters {

	private $provider_id = 'jet-engine';

	public function __construct() {
		add_action( 'jet-engine/query-builder/listings/on-query', array( $this, 'maybe_set_query_props' ), 10, 4 );
		add_action( 'jet-engine/query-builder/query/after-query-setup', array( $this, 'maybe_setup_filter' ) );
	}

	/**
	 * Check if JetSmartFilters request is currently processing
	 *
	 * @return boolean [description]
	 */
	public function is_filters_request() {

		if ( ! empty( $_REQUEST['action'] ) && 'jet_smart_filters' === $_REQUEST['action'] ) {
			return true;
		}

		if ( ! empty( $_REQUEST['jsf'] ) && 'jet-engine' === $_REQUEST['jsf'] ) {
			return true;
		}

		return false;

	}

	/**
	 * Setup filtered data if it was filters request
	 *
	 * @param  [type] $query [description]
	 * @return [type]        [description]
	 */
	public function maybe_setup_filter( $query ) {

		$remove_hook = false;

		// Get filtered query
		if ( $this->is_filters_request() ) {

			$filtered_query = jet_smart_filters()->query->get_query_from_request();

			if ( null === $filtered_query ) {
				$filtered_query = jet_smart_filters()->query->_query;
			}

			if ( ! empty( $filtered_query ) ) {
				foreach ( $filtered_query as $prop => $value ) {
					$query->set_filtered_prop( $prop, $value );
				}
			}

			$remove_hook = true;

			add_filter( 'jet-smart-filters/render/ajax/data', function( $data ) use ( $query ) {

				if ( ! isset( $data['fragments'] ) ) {
					$data['fragments'] = array();
				}

				$data['fragments'][ '.jet-engine-query-count.query-' . $query->id ] = $query->get_items_total_count();

				return $data;

			} );

		}

		// Process pager
		if ( $this->is_filters_request() && ( ! empty( $_REQUEST['paged'] ) || ! empty( $_REQUEST['jet_paged'] ) ) ) {

			if ( ! empty( $_REQUEST['paged'] ) ) {
				$page = absint( $_REQUEST['paged'] );
			} elseif ( ! empty( $_REQUEST['jet_paged'] ) ) {
				$page = absint( $_REQUEST['jet_paged'] );
			} else {
				$page = 1;
			}

			$query->set_filtered_prop( '_page', $page );

			$remove_hook = true;

		}

		if ( $remove_hook ) {
			remove_action( 'jet-engine/query-builder/query/after-query-setup', array( $this, 'maybe_setup_filter' ) );
		}

	}

	public function maybe_set_query_props( $query, $settings, $widget, $query_manager ) {

		$query_id = ! empty( $settings['_element_id'] ) ? $settings['_element_id'] : false;

		// Setup props for the pager
		jet_smart_filters()->query->set_props(
			'jet-engine',
			array(
				'found_posts'   => $query->get_items_total_count(),
				'max_num_pages' => $query->get_items_pages_count(),
				'page'          => $query->get_current_items_page(),
			),
			$query_id
		);

		// Store settings to localize it by SmartFilters later
		jet_smart_filters()->providers->store_provider_settings(
			$this->provider_id,
			$widget->get_required_settings(),
			$query_id
		);

	}

}
