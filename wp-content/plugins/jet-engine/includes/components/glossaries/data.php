<?php
namespace Jet_Engine\Glossaries;
/**
 * Glossaries data controller class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Engine_Options_Data class
 */
class Data extends \Jet_Engine_Base_Data {

	/**
	 * Table name
	 *
	 * @var string
	 */
	public $table = 'post_types';

	/**
	 * Query arguments
	 *
	 * @var array
	 */
	public $query_args = array(
		'status' => 'glossary',
	);

	/**
	 * Table format
	 *
	 * @var string
	 */
	public $table_format = array( '%s', '%s', '%s', '%s', '%s' );

	/**
	 * Returns blacklisted post types slugs
	 *
	 * @return array
	 */
	public function items_blacklist() {
		return array();
	}

	/**
	 * Returns blacklisted post types slugs
	 *
	 * @return array
	 */
	public function meta_blacklist() {
		return array();
	}

	/**
	 * Sanitizr post type request
	 *
	 * @return void
	 */
	public function sanitize_item_request() {
		return true;
	}

	/**
	 * Prepare post data from request to write into database
	 *
	 * @return array
	 */
	public function sanitize_item_from_request() {

		$request = $this->request;

		$result = array(
			'slug'        => '',
			'status'      => 'glossary',
			'labels'      => array(),
			'args'        => array(),
			'meta_fields' => array(),
		);

		$name = ! empty( $request['name'] ) ? sanitize_text_field( $request['name'] ) : 'Untitled glossary';

		$labels = array(
			'name' => $name,
		);

		/**
		 * @todo Validate meta fields before saving - ensure that used correct types and all names was set.
		 */
		$meta_fields = ! empty( $request['fields'] ) ? $request['fields'] : array();

		$result['slug']        = null;
		$result['labels']      = $labels;
		$result['meta_fields'] = $this->sanitize_meta_fields( $meta_fields );

		return $result;

	}

	/**
	 * Sanitize meta fields
	 *
	 * @param  [type] $meta_fields [description]
	 * @return [type]              [description]
	 */
	public function sanitize_meta_fields( $meta_fields ) {

		foreach ( $meta_fields as $key => $field ) {

			$sanitized_field = array(
				'value'      => ! empty( $field['value'] ) ? $field['value'] : '',
				'label'      => ! empty( $field['label'] ) ? $field['label'] : '',
				'is_checked' => isset( $field['is_checked'] ) ? filter_var( $field['is_checked'], FILTER_VALIDATE_BOOLEAN ) : false,
			);

			$meta_fields[ $key ] = $sanitized_field;

		}

		return $meta_fields;
	}

	/**
	 * Filter post type for register
	 *
	 * @return array
	 */
	public function filter_item_for_register( $item ) {

		$result         = array();
		$args           = maybe_unserialize( $item['args'] );
		$labels         = maybe_unserialize( $item['labels'] );
		$item['fields'] = maybe_unserialize( $item['meta_fields'] );
		$result         = array_merge( $item, $args, $labels );

		unset( $result['args'] );
		unset( $result['status'] );
		unset( $result['meta_fields'] );

		return $result;

	}

	/**
	 * Filter post type for edit
	 *
	 * @return array
	 */
	public function filter_item_for_edit( $item ) {

		$result         = array();
		$args           = maybe_unserialize( $item['args'] );
		$labels         = maybe_unserialize( $item['labels'] );
		$item['fields'] = maybe_unserialize( $item['meta_fields'] );
		$result         = array_merge( $item, $args, $labels );

		unset( $result['args'] );
		unset( $result['status'] );
		unset( $result['meta_fields'] );

		return $result;
	}

	public function get_item_for_edit( $id ) {

		$item = parent::get_item_for_edit( $id );

		if ( empty( $item ) || empty( $item['fields'] ) ) {
			return $item;
		}

		$item['fields'] = array_map( function ( $field ) {

			if ( ! empty( $field['label'] ) ) {
				$field['label'] = apply_filters( 'jet-engine/compatibility/translate-string', $field['label'] );
			}

			if ( ! empty( $field['value'] ) ) {
				$field['value'] = apply_filters( 'jet-engine/compatibility/translate-string', $field['value'] );
			}

			return $field;
		}, $item['fields'] );

		return $item;
	}

}
