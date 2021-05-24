<?php
/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Listings_Ajax_Handlers' ) ) {

	class Jet_Engine_Listings_Ajax_Handlers {

		public function __construct() {
			add_action( 'wp_ajax_jet_engine_ajax',        array( $this, 'handle_ajax' ) );
			add_action( 'wp_ajax_nopriv_jet_engine_ajax', array( $this, 'handle_ajax' ) );
		}

		/**
		 * Handle AJAX request
		 *
		 * @return void
		 */
		public function handle_ajax() {

			if ( ! isset( $_REQUEST['handler'] ) || ! is_callable( array( $this, $_REQUEST['handler'] ) ) ) {
				return;
			}

			do_action( 'jet-engine/ajax-handlers/before-call-handler', $this );

			call_user_func( array( $this, $_REQUEST['handler'] ) );

		}

		/**
		 * Load more handler.
		 */
		public function listing_load_more() {

			$query           = ! empty( $_REQUEST['query'] ) ? $_REQUEST['query'] : array();
			$widget_settings = ! empty( $_REQUEST['widget_settings'] ) ? $_REQUEST['widget_settings'] : array();

			$data = array(
				'id'         => 'jet-listing-grid',
				'elType'     => 'widget',
				'settings'   => $widget_settings,
				'elements'   => array(),
				'widgetType' => 'jet-listing-grid',
			);

			$widget = false;

			if ( jet_engine()->has_elementor() ) {

				$widget = Elementor\Plugin::$instance->elements_manager->create_element_instance( $data );

				if ( ! $widget ) {
					throw new \Exception( 'Widget not found.' );
				}

			}

			do_action( 'jet-engine/elementor-views/ajax/load-more', $widget );

			ob_start();

			$base_class       = 'jet-listing-grid';
			$equal_cols_class = '';

			if ( ! empty( $widget_settings['equal_columns_height'] ) ) {
				$equal_cols_class = 'jet-equal-columns';
			}

			if ( jet_engine()->has_elementor() ) {
				jet_engine()->listings->data->set_listing(
					Elementor\Plugin::$instance->documents->get_doc_for_frontend( $widget_settings['lisitng_id'] )
				);

			} else {

				$l_settings = get_post_meta( $widget_settings['lisitng_id'], '_elementor_page_settings', true );

				if ( empty( $listing_settings ) ) {
					$listing_settings = array();
				}

				$source = ! empty( $l_settings['listing_source'] ) ? $l_settings['listing_source'] : 'posts';
				$post_type = ! empty( $l_settings['listing_post_type'] ) ? $l_settings['listing_post_type'] : 'post';
				$tax = ! empty( $l_settings['listing_tax'] ) ? $l_settings['listing_tax'] : 'category';
				$repeater_source = ! empty( $l_settings['repeater_source'] ) ? $l_settings['repeater_source'] : '';
				$repeater_field = ! empty( $l_settings['repeater_field'] ) ? $l_settings['repeater_field'] : '';

				$doc = jet_engine()->listings->get_new_doc( array(
					'listing_source'    => $source,
					'listing_post_type' => $post_type,
					'listing_tax'       => $tax,
					'is_main'           => true,
					'repeater_source'   => $repeater_source,
					'repeater_field'    => $repeater_field,
				), absint( $widget_settings['lisitng_id'] ) );

				jet_engine()->listings->data->set_listing( $doc );

			}

			$listing_source = jet_engine()->listings->data->get_listing_source();
			$page           = ! empty( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;
			$query['paged'] = $page;

			$render_instance = jet_engine()->listings->get_render_instance( 'listing-grid', $widget_settings );

			switch ( $listing_source ) {

				case 'posts':
					$widget_settings['posts_num'] = $query['posts_per_page'];

					$query = apply_filters(
						'jet-engine/listing/grid/posts-query-args',
						$query,
						$render_instance,
						$widget_settings
					);

					$offset          = ! empty( $query['offset'] ) ? absint( $query['offset'] ) : 0;
					$query['offset'] = $offset + ( $page - 1 ) * absint( $widget_settings['posts_num'] );

					// Added to remove slash from regex meta-query
					if ( ! empty( $query['meta_query'] ) ) {
						$query['meta_query'] = wp_unslash( $query['meta_query'] );
					}

					$posts_query     = new WP_Query( $query );
					$posts           = $posts_query->posts;
					break;

				case 'terms':
					$offset          = ! empty( $query['offset'] ) ? absint( $query['offset'] ) : 0;
					$query['offset'] = $offset + ( $page - 1 ) * absint( $widget_settings['posts_num'] );
					$posts           = get_terms( $query );
					break;

				case 'users':

					$query['offset'] = ( $page - 1 ) * absint( $widget_settings['posts_num'] );
					$user_query      = new WP_User_Query( $query );
					$posts           = (array) $user_query->get_results();

					break;

				default:

					$posts = apply_filters(
						'jet-engine/listing/grid/query/' . $listing_source,
						array(),
						$widget_settings,
						$render_instance
					);

					break;
			}

			if ( 1 < $query['paged'] ) {
				$start_from = ( $query['paged'] - 1 ) * absint( $widget_settings['posts_num'] ) + 1;
			} else {
				$start_from = false;
			}

			if ( jet_engine()->has_elementor() ) {
				Elementor\Plugin::instance()->frontend->start_excerpt_flag( null );
			}

			$render_instance->posts_loop(
				$posts,
				$widget_settings,
				$base_class,
				$equal_cols_class,
				$start_from
			);

			wp_send_json_success( array( 'html' => ob_get_clean() ) );

		}

		/**
		 * Get whole listing through AJAX
		 */
		public function get_listing() {

			$query            = ! empty( $_REQUEST['query'] ) ? $_REQUEST['query'] : array();
			$widget_settings  = ! empty( $_REQUEST['widget_settings'] ) ? $_REQUEST['widget_settings'] : array();
			$post_id          = ! empty( $_REQUEST['post_id'] ) ? absint( $_REQUEST['post_id'] ) : false;
			$queried_obj_data = ! empty( $_REQUEST['queried_id'] ) ? explode( '|', $_REQUEST['queried_id'] ) : false;
			$queried_id       = ! empty( $queried_obj_data[0] ) ? absint( $queried_obj_data[0] ) : false;
			$queried_obj_type = ! empty( $queried_obj_data[1] ) ? $queried_obj_data[1] : 'WP_Post';
			$element_id       = ( ! empty( $_REQUEST['element_id'] ) && 'false' !== $_REQUEST['element_id'] ) ? $_REQUEST['element_id'] : false;
			$response         = array();

			if ( $queried_id && 'WP_Post' === $queried_obj_type ) {
				global $post;
				$post = get_post( $queried_id );
			}

			if ( $post_id && $element_id ) {

				$listing_type = ( isset( $_REQUEST['listing_type'] ) && 'false' !== $_REQUEST['listing_type'] ) ? $_REQUEST['listing_type'] : false;

				if ( jet_engine()->has_elementor() && ( ! $listing_type || 'elementor' === $listing_type ) ) {

					$elementor = \Elementor\Plugin::instance();
					$document = $elementor->documents->get( $post_id );

					if ( $document ) {
						$widget = $this->find_element_recursive( $document->get_elements_data(), $element_id );

						if ( $widget ) {
							$widget_instance = $elementor->elements_manager->create_element_instance( $widget );
							$widget_settings = $widget_instance->get_settings_for_display();
							$_REQUEST['query'] = null;
						}

					}
				} elseif ( $listing_type ) {

					$widget_settings = apply_filters(
						'jet-engine/listings/ajax/settings-by-id/' . $listing_type,
						array(),
						$element_id,
						$post_id
					);

				}

			}

			$_widget_settings = $widget_settings;
			$is_lazy_load     = ! empty( $widget_settings['lazy_load'] ) ? filter_var( $widget_settings['lazy_load'], FILTER_VALIDATE_BOOLEAN ) : false;

			// Reset `lazy_load` to avoid looping.
			if ( $is_lazy_load ) {

				$widget_settings['lazy_load'] = '';

				// if ( jet_engine()->has_elementor() ) {
				//	Elementor\Plugin::instance()->frontend->start_excerpt_flag( null );
				//}

			}

			if ( empty( $widget_settings['lisitng_id'] ) ) {
				wp_send_json_success( array( 'html' => __( 'Request data is incorrect', 'jet-engine' ) ) );
			}

			ob_start();

			$render_instance = jet_engine()->listings->get_render_instance( 'listing-grid', $widget_settings );

			if ( $is_lazy_load && $queried_id ) {
				switch ( $queried_obj_type ) {
					case 'WP_Post':
						jet_engine()->listings->data->set_current_object( get_post( $queried_id ) );
						break;
					case 'WP_Term':
						jet_engine()->listings->data->set_current_object( get_term( $queried_id ) );
						break;
				}
			}

			if ( $is_lazy_load && ! empty( $query ) ) { // for Archive pages

				if ( jet_engine()->has_elementor() ) {
					jet_engine()->listings->data->set_listing(
						Elementor\Plugin::$instance->documents->get_doc_for_frontend( $widget_settings['lisitng_id'] )
					);

				} else {

					$l_settings = get_post_meta( $widget_settings['lisitng_id'], '_elementor_page_settings', true );

					if ( empty( $listing_settings ) ) {
						$listing_settings = array();
					}

					$source = ! empty( $l_settings['listing_source'] ) ? $l_settings['listing_source'] : 'posts';
					$post_type = ! empty( $l_settings['listing_post_type'] ) ? $l_settings['listing_post_type'] : 'post';
					$tax = ! empty( $l_settings['listing_tax'] ) ? $l_settings['listing_tax'] : 'category';
					$repeater_source = ! empty( $l_settings['repeater_source'] ) ? $l_settings['repeater_source'] : '';
					$repeater_field = ! empty( $l_settings['repeater_field'] ) ? $l_settings['repeater_field'] : '';

					$doc = jet_engine()->listings->get_new_doc( array(
						'listing_source'    => $source,
						'listing_post_type' => $post_type,
						'listing_tax'       => $tax,
						'is_main'           => true,
						'repeater_source'   => $repeater_source,
						'repeater_field'    => $repeater_field,
					), absint( $widget_settings['lisitng_id'] ) );

					jet_engine()->listings->data->set_listing( $doc );

				}

				$posts_query = new WP_Query( $query );
				$posts       = $posts_query->posts;

				$render_instance->posts_query = $posts_query;

				$render_instance->query_vars['page']    = $posts_query->get( 'paged' ) ? $posts_query->get( 'paged' ) : 1;
				$render_instance->query_vars['pages']   = $posts_query->max_num_pages;
				$render_instance->query_vars['request'] = $query;

				$render_instance->posts_template( $posts, $widget_settings );

			} else {
				$render_instance->render();
			}

			$response['html'] = ob_get_clean();

			self::maybe_add_enqueue_assets_data( $response );

			$response = apply_filters( 'jet-engine/ajax/get_listing/response', $response, $_widget_settings );

			wp_send_json_success( $response );

		}

		public function find_element_recursive( $elements, $element_id ) {

			foreach ( $elements as $element ) {

				if ( $element_id === $element['id'] ) {
					return $element;
				}

				if ( ! empty( $element['elements'] ) ) {

					$element = $this->find_element_recursive( $element['elements'], $element_id );

					if ( $element ) {
						return $element;
					}
				}
			}

			return false;
		}

		public static function maybe_add_enqueue_assets_data( &$response ) {

			if ( isset( $_REQUEST['isEditMode'] ) && filter_var( $_REQUEST['isEditMode'], FILTER_VALIDATE_BOOLEAN ) ) {
				return;
			}

			wp_scripts()->done[] = 'jquery';

			$scripts = wp_scripts()->queue;
			$styles  = wp_styles()->queue;

			if ( ! empty( $scripts ) ) {
				$response['scripts'] = array();

				foreach ( (array) $scripts as $script ) {

					ob_start();
					wp_scripts()->do_items( $script );
					$script_html = ob_get_clean();

					$response['scripts'][ $script ] = $script_html;
				}
			}

			if ( ! empty( $styles ) ) {
				$response['styles'] = array();

				foreach ( (array) $styles as $style ) {

					ob_start();
					wp_styles()->do_items( $style );
					$style_html = ob_get_clean();

					$response['styles'][ $style ] = $style_html;
				}
			}

		}

	}

}
