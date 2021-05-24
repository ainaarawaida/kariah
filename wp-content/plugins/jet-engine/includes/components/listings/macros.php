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

if ( ! class_exists( 'Jet_Engine_Listings_Macros' ) ) {

	/**
	 * Define Jet_Engine_Listings_Macros class
	 */
	class Jet_Engine_Listings_Macros {

		/**
		 * Return available macros list
		 *
		 * @return [type] [description]
		 */
		public function get_all() {

			$meta_fields = jet_engine()->meta_boxes->get_fields_for_select( 'plain' );
			unset( $meta_fields[''] );
			$meta_fields = array_values( $meta_fields );

			return apply_filters( 'jet-engine/listings/macros-list', array(
				'title' => array(
					'label' => esc_html__( 'Title', 'jet-engine' ),
					'cb'    => array( $this, 'get_title' ),
				),
				'field_value' => array(
					'label' => esc_html__( 'Field value', 'jet-engine' ),
					'cb'    => array( $this, 'get_field_value' ),
				),
				'current_id' => array(
					'label' => esc_html__( 'Current ID', 'jet-engine' ),
					'cb'    => array( $this, 'get_current_id' ),
				),
				'current_tags' => array(
					'label' => esc_html__( 'Current tags', 'jet-engine' ),
					'cb'    => array( $this, 'get_current_tags' ),
				),
				'current_terms' => array(
					'label' => esc_html__( 'Current terms', 'jet-engine' ),
					'cb'    => array( $this, 'get_current_terms' ),
					'args'  => array(
						'taxonomy' => array(
							'label'   => __( 'Taxonomy', 'jet-engine' ),
							'type'    => 'select',
							'options' => jet_engine()->listings->get_taxonomies_for_options(),
						),
					),
				),
				'current_categories' => array(
					'label' => esc_html__( 'Current categories', 'jet-engine' ),
					'cb'    => array( $this, 'get_current_categories' ),
				),
				'current_meta' => array(
					'label' => esc_html__( 'Current meta', 'jet-engine' ),
					'cb'    => array( $this, 'get_current_meta' ),
					'args'  => array(
						'meta_key' => array(
							'label'   => __( 'Meta field', 'jet-engine' ),
							'type'    => 'text',
							'default' => '',
						),
					),
				),
				'current_meta_string' => array(
					'label' => esc_html__( 'Current meta string', 'jet-engine' ),
					'cb'    => array( $this, 'get_current_meta_string' ),
					'args'  => array(
						'meta_key' => array(
							'label'   => __( 'Meta field', 'jet-engine' ),
							'type'    => 'text',
							'default' => '',
						),
					),
				),
				'related_parents_from' => array(
					'label' => esc_html__( 'Related parents from', 'jet-engine' ),
					'cb'    => array( $this, 'get_related_parents' ),
					'args'  => array(
						'post_type' => array(
							'label'   => __( 'Post type', 'jet-engine' ),
							'type'    => 'select',
							'options' => jet_engine()->listings->get_post_types_for_options(),
						),
					),
				),
				'related_children_from' => array(
					'label' => esc_html__( 'Related children from', 'jet-engine' ),
					'cb'    => array( $this, 'get_related_children' ),
					'args'  => array(
						'post_type' => array(
							'label'   => __( 'Post type', 'jet-engine' ),
							'type'    => 'select',
							'options' => jet_engine()->listings->get_post_types_for_options(),
						),
					),
				),
				'related_children_between' => array(
					'label' => esc_html__( 'Related children between', 'jet-engine' ),
					'cb'    => array( $this, 'get_related_children_between' ),
					'args'  => array(
						'post_type_1' => array(
							'label'   => __( 'Post type 1', 'jet-engine' ),
							'type'    => 'select',
							'options' => jet_engine()->listings->get_post_types_for_options(),
						),
						'post_type_2' => array(
							'label'   => __( 'Post type 2', 'jet-engine' ),
							'type'    => 'select',
							'options' => jet_engine()->listings->get_post_types_for_options(),
						),
					),
				),
				'queried_term' => array(
					'label' => esc_html__( 'Queried term', 'jet-engine' ),
					'cb'    => array( $this, 'get_queried_term' ),
				),
				'author_id' => array(
					'label' => esc_html__( 'Post author ID', 'jet-engine' ),
					'cb'    => array( $this, 'get_post_author_id' ),
				),
				'current_user_id' => array(
					'label' => esc_html__( 'Current user ID', 'jet-engine' ),
					'cb'    => 'get_current_user_id',
				),
				'queried_user_id' => array(
					'label' => esc_html__( 'Queried user ID', 'jet-engine' ),
					'cb'    => array( $this, 'get_queried_user_id' ),
				),
				'query_var' => array(
					'label' => esc_html__( 'Query Variable', 'jet-engine' ),
					'cb'    => array( $this, 'get_query_var' ),
					'args'  => array(
						'var_name' => array(
							'label'   => __( 'Variable Name', 'jet-engine' ),
							'type'    => 'text',
							'default' => '',
						),
					),
				),
				'today' => array(
					'label' => esc_html__( 'Today', 'jet-engine' ),
					'cb'    => array( $this, 'get_today_timestamp' ),
				),
				'str_to_time' => array(
					'label' => esc_html__( 'String to timestamp', 'jet-engine' ),
					'cb'    => array( $this, 'string_to_time' ),
					'args'  => array(
						'str' => array(
							'label'   => __( 'String to convert', 'jet-engine' ),
							'type'    => 'text',
							'default' => '',
						),
					),
				),
				'jet_engine_field_name' => array(
					'label' => esc_html__( 'JetEngine meta field', 'jet-engine' ),
					'cb'    => array( $this, 'field_name' ),
					'args'  => array(
						'str' => array(
							'label'   => __( 'Field', 'jet-engine' ),
							'type'    => 'select',
							'groups'  => $meta_fields,
						),
					),
				),
			) );

		}

		/**
		 * Returns field name passed as second argument.
		 * This macros is need to select JetEngine meta fields visually for the macros editors (dynamic tags, Query builder etc.)
		 *
		 * @param  [type] $field_value [description]
		 * @param  [type] $field_name  [description]
		 * @return [type]              [description]
		 */
		public function field_name( $field_value = null, $field_name = null ) {
			return $field_name;
		}

		/**
		 * Return today timestamp
		 *
		 * @return [type] [description]
		 */
		public function get_today_timestamp() {
			return strtotime( 'Today 00:00' );
		}

		/**
		 * Return timestamp by string
		 *
		 * @return [type] [description]
		 */
		public function string_to_time( $field_value = null, $string = null ) {
			return strtotime( $string );
		}

		/**
		 * Get macros list for options.
		 *
		 * @return array
		 */
		public function get_macros_list_for_options() {
			$all = $this->get_all();
			$result = array();

			foreach ( $all as $key => $data ) {
				if ( is_array( $data ) ) {
					$result[ $key ] = ! empty( $data['label'] ) ? $data['label'] : $key;
				} else {
					$result[ $key ] = $key;
				}
			}

			return $result;
		}

		/**
		 * Return verbosed macros list
		 *
		 * @return [type] [description]
		 */
		public function verbose_macros_list() {

			$macros = $this->get_all();
			$result = '';
			$sep    = '';

			foreach ( $macros as $key => $data ) {
				$result .= $sep . '%' . $key . '%';
				$sep     = ', ';
			}

			return $result;

		}

		/**
		 * Returns queried variable
		 *
		 * @return [type] [description]
		 */
		public function get_query_var( $field_value = null, $variable = null ) {

			global $wp_query;

			if ( ! $variable ) {
				return null;
			}

			if ( isset( $wp_query->query_vars[ $variable ] ) ) {
				return $wp_query->query_vars[ $variable ];
			} elseif ( isset( $_REQUEST[ $variable ] ) ) {
				if ( ! is_array( $_REQUEST[ $variable ] ) ) {
					return esc_attr( $_REQUEST[ $variable ] );
				} else {
					return $_REQUEST[ $variable ];
				}
			}

			return null;
		}

		/**
		 * Returns queried term
		 *
		 * @return [type] [description]
		 */
		public function get_queried_term() {

			$current_object = jet_engine()->listings->data->get_current_object();

			if ( $current_object && 'WP_Term' === get_class( $current_object ) ) {
				return $current_object->term_id;
			} else {
				$queried_object = get_queried_object();

				if ( $queried_object && 'WP_Term' === get_class( $queried_object ) ) {
					return $queried_object->term_id;
				} else {
					return null;
				}

			}

			return null;

		}

		/**
		 * Returns ID of current post author
		 *
		 * @return [type] [description]
		 */
		public function get_post_author_id() {
			return get_the_author_meta( 'ID' );
		}

		/**
		 * Returns ID of the queried user
		 */
		public function get_queried_user_id() {

			$user = jet_engine()->listings->data->get_queried_user_object();

			if ( ! $user ) {
				return false;
			} else {
				return $user->ID;
			}

		}

		/**
		 * Can be used for meta query. Returns values of passed mata key for current post/term.
		 *
		 * @param  mixed  $field_value Field value.
		 * @param  string $meta_key    Metafield to get value from.
		 * @return mixed
		 */
		public function get_current_meta( $field_value = null, $meta_key = null ) {

			if ( ! $meta_key && ! empty( $field_value ) ) {
				$meta_key = $field_value;
			}

			if ( ! $meta_key ) {
				return '';
			}

			$object = jet_engine()->listings->data->get_current_object();

			if ( ! $object ) {
				return '';
			}

			$class  = get_class( $object );
			$result = '';

			switch ( $class ) {

				case 'WP_Post':
					return get_post_meta( $object->ID, $meta_key, true );

				case 'WP_Term':
					return get_term_meta( $object->term_id, $meta_key, true );

			}

		}

		/**
		 * Returns current meta value. For arrays implode it to coma separated string
		 *
		 * @return [type] [description]
		 */
		public function get_current_meta_string( $field_value = null, $meta_key = null ) {
			$meta = $this->get_current_meta( $field_value, $meta_key );
			return is_array( $meta ) ? implode( ', ', $meta ) : $meta;
		}

		/**
		 * Get current object ID
		 *
		 * @param  mixed  $field_value Field value.
		 * @return string
		 */
		public function get_current_id( $field_value = null ) {

			$object = jet_engine()->listings->data->get_current_object();

			if ( ! $object ) {
				return $field_value;
			}

			$class  = get_class( $object );
			$result = '';

			switch ( $class ) {
				case 'WP_Post':
					$result = $object->ID;
					break;

				case 'WP_Term':
					$result = $object->term_id;
					break;

				default:
					$result = apply_filters( 'jet-engine/listings/macros/current-id', $result, $object );
					break;
			}

			return $result;

		}

		/**
		 * Get current object title
		 *
		 * @return string
		 */
		public function get_title( $field_value = null ) {

			$object = jet_engine()->listings->data->get_current_object();

			if ( ! $object ) {
				return '';
			}

			$class  = get_class( $object );
			$result = '';

			switch ( $class ) {
				case 'WP_Post':
					$result = $object->post_title;
					break;

				case 'WP_Term':
					$result = $object->name;
					break;
			}

			return $result;

		}

		/**
		 * Returns comma-separated terms list of passed taxonomy assosiated with current post.
		 *
		 * @param  mixed  $field_value Field value.
		 * @param  string $taxonomy    Taxonomy name.
		 * @return string
		 */
		public function get_current_terms( $field_value, $taxonomy = null ) {

			if ( ! $taxonomy && ! empty( $field_value ) ) {
				$taxonomy = $field_value;
			}

			if ( ! $taxonomy ) {
				return '';
			}

			$object = jet_engine()->listings->data->get_current_object();
			$class  = get_class( $object );

			if ( 'WP_Post' !== $class ) {
				return '';
			}

			$terms = wp_get_post_terms( $object->ID, $taxonomy, array( 'fields' => 'ids' ) );

			if ( empty( $terms ) ) {
				return '';
			}

			return implode( ',', $terms );

		}

		/**
		 * Returns comma-separated tags list assosiated with current post.
		 *
		 * @return string
		 */
		public function get_current_tags() {

			$object = jet_engine()->listings->data->get_current_object();
			$class  = get_class( $object );

			if ( 'WP_Post' !== $class ) {
				return '';
			}

			$tags = wp_get_post_tags( $object->ID, array( 'fields' => 'ids' ) );

			if ( empty( $tags ) ) {
				return '';
			}

			return implode( ',', $tags );

		}

		/**
		 * Returns related post IDs
		 * @return [type] [description]
		 */
		public function get_related_parents( $value, $post_type ) {

			$posts = jet_engine()->relations->get_related_posts( array(
				'post_type_1' => $post_type,
				'post_type_2' => get_post_type(),
				'from'        => $post_type,
			) );

			if ( empty( $posts ) ) {
				return 'not-found';
			}

			if ( is_array( $posts ) ) {
				return implode( ',', $posts );
			} else {
				return $posts;
			}

		}

		/**
		 * Returns related post IDs
		 * @return [type] [description]
		 */
		public function get_related_children( $value, $post_type ) {

			$posts = jet_engine()->relations->get_related_posts( array(
				'post_type_1' => get_post_type(),
				'post_type_2' => $post_type,
				'from'        => $post_type,
			) );

			if ( empty( $posts ) ) {
				return 'not-found';
			}

			if ( is_array( $posts ) ) {
				return implode( ',', $posts );
			} else {
				return $posts;
			}

		}

		/**
		 * Returns related post IDs
		 * @return [type] [description]
		 */
		public function get_related_children_between( $value, $post_types ) {

			$post_types = explode( '|', $post_types );

			$posts = jet_engine()->relations->get_related_posts( array(
				'post_type_1' => $post_types[0],
				'post_type_2' => $post_types[1],
				'from'        => $post_types[1],
			) );

			if ( empty( $posts ) ) {
				return 'not-found';
			}

			if ( is_array( $posts ) ) {
				return implode( ',', $posts );
			} else {
				return $posts;
			}

		}

		/**
		 * Returns comma-separated categories list assosiated with current post.
		 *
		 * @return string
		 */
		public function get_current_categories() {

			$object = jet_engine()->listings->data->get_current_object();
			$class  = get_class( $object );

			if ( 'WP_Post' !== $class ) {
				return '';
			}

			$cats = wp_get_post_categories( $object->ID, array( 'fields' => 'ids' ) );

			if ( empty( $cats ) ) {
				return '';
			}

			return implode( ',', $cats );

		}

		/**
		 * Returns current field value
		 *
		 * @param  [type] $field_value [description]
		 * @return [type]              [description]
		 */
		public function get_field_value( $field_value = null ) {
			return $field_value;
		}

		/**
		 * Do macros inside string
		 *
		 * @param  [type] $string      [description]
		 * @param  [type] $field_value [description]
		 * @return [type]              [description]
		 */
		public function do_macros( $string, $field_value = null ) {

			$macros = $this->get_all();

			return preg_replace_callback(
				'/%([a-z_-]+)(\|[a-zA-Z0-9_\-|]+)?%/',
				function( $matches ) use ( $macros, $field_value ) {

					$found = $matches[1];

					if ( ! isset( $macros[ $found ] ) ) {
						return $matches[0];
					}

					$cb = $macros[ $found ];

					if ( is_array( $cb ) && isset( $cb['cb'] ) ) {
						$cb = ! empty( $cb['cb'] ) ? $cb['cb'] : false;

						if ( ! $cb ) {
							return $matches[0];
						}
					}

					if ( ! is_callable( $cb ) ) {
						return $matches[0];
					}

					$args = isset( $matches[2] ) ? ltrim( $matches[2], '|' ) : false;

					$result = call_user_func( $cb, $field_value, $args );

					if ( is_array( $result ) ) {
						return implode( ',', $result );
					} else {
						return $result;
					}

				}, $string
			);

		}

	}

}
