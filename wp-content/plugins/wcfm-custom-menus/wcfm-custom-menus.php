<?php
/**
 * Plugin Name: WCFM - Custom Menus
 * Plugin URI: http://wclovers.com
 * Description: WCFM Custom Menus.
 * Author: WC Lovers
 * Version: 1.0.0
 * Author URI: http://wclovers.com
 *
 * Text Domain: wcfm-custom-menus
 * Domain Path: /lang/
 *
 * WC requires at least: 3.0.0
 * WC tested up to: 3.2.0
 *
 */

if(!defined('ABSPATH')) exit; // Exit if accessed directly

if(!class_exists('WCFM')) return; // Exit if WCFM not installed

/**
 * WCFM - Custom Menus Query Var
 */
function wcfmcsm_query_vars( $query_vars ) {
	$wcfm_modified_endpoints = (array) get_option( 'wcfm_endpoints' );
	
	$query_custom_menus_vars = array(
		'wcfm-ahli'               => ! empty( $wcfm_modified_endpoints['wcfm-ahli'] ) ? $wcfm_modified_endpoints['wcfm-ahli'] : 'ahli',
		'wcfm-ahli_manage'          => ! empty( $wcfm_modified_endpoints['wcfm-ahli_manage'] ) ? $wcfm_modified_endpoints['wcfm-ahli_manage'] : 'ahli_manage',
		'wcfm-service'             => ! empty( $wcfm_modified_endpoints['wcfm-service'] ) ? $wcfm_modified_endpoints['wcfm-service'] : 'service',
		'wcfm-upgrade'             => ! empty( $wcfm_modified_endpoints['wcfm-upgrade'] ) ? $wcfm_modified_endpoints['wcfm-upgrade'] : 'upgrade',
		'wcfm-contact'             => ! empty( $wcfm_modified_endpoints['wcfm-contact'] ) ? $wcfm_modified_endpoints['wcfm-contact'] : 'contact',
		'wcfm-cubaan'         => ! empty( $wcfm_modified_endpoints['wcfm-cubaan'] ) ? $wcfm_modified_endpoints['wcfm-cubaan'] : 'cubaan',
		'wcfm-claim'         => ! empty( $wcfm_modified_endpoints['wcfm-claim'] ) ? $wcfm_modified_endpoints['wcfm-claim'] : 'claim',
	);
	
	$query_vars = array_merge( $query_vars, $query_custom_menus_vars );
	
	return $query_vars;
}
add_filter( 'wcfm_query_vars', 'wcfmcsm_query_vars', 50 );

/**
 * WCFM - Custom Menus End Point Title
 */
function wcfmcsm_endpoint_title( $title, $endpoint ) {
	global $wp;
	switch ( $endpoint ) {
		case 'wcfm-build' :
			$title = __( 'Build', 'wcfm-custom-menus' );
		break;
		
		case 'wcfm-service' :
			$title = __( 'Service', 'wcfm-custom-menus' );
		break;
		
		case 'wcfm-upgrade' :
			$title = __( 'Upgrade', 'wcfm-custom-menus' );
		break;
		
		case 'wcfm-contact' :
			$title = __( 'Contact', 'wcfm-custom-menus' );
		break;
		
		case 'wcfm-appointment' :
			$title = __( 'Appointment', 'wcfm-custom-menus' );

		case 'wcfm-claim' :
			$title = __( 'Claim', 'wcfm-custom-menus' );
		break;
	}
	
	return $title;
}
add_filter( 'wcfm_endpoint_title', 'wcfmcsm_endpoint_title', 50, 2 );

/**
 * WCFM - Custom Menus Endpoint Intialize
 */
function wcfmcsm_init() {
	global $WCFM_Query;

	// Intialize WCFM End points
	$WCFM_Query->init_query_vars();
	$WCFM_Query->add_endpoints();
	
	if( !get_option( 'wcfm_updated_end_point_cms' ) ) {
		// Flush rules after endpoint update
		flush_rewrite_rules();
		update_option( 'wcfm_updated_end_point_cms', 1 );
	}
}
add_action( 'init', 'wcfmcsm_init', 50 );

/**
 * WCFM - Custom Menus Endpoiint Edit
 */
function wcfm_custom_menus_endpoints_slug( $endpoints ) {
	
	$custom_menus_endpoints = array(
												'wcfm-build'        => 'build',
												'wcfm-service'      => 'service',
												'wcfm-upgrade'      => 'upgrade',
												'wcfm-contact'      => 'contact',
												'wcfm-appointment'  => 'appointment',
												'wcfm-claim'  => 'claim',
												);
	
	$endpoints = array_merge( $endpoints, $custom_menus_endpoints );
	
	return $endpoints;
}
add_filter( 'wcfm_endpoints_slug', 'wcfm_custom_menus_endpoints_slug' );

if(!function_exists('get_wcfm_custom_menus_url')) {
	function get_wcfm_custom_menus_url( $endpoint ) {
		global $WCFM;
		$wcfm_page = get_wcfm_page();
		$wcfm_custom_menus_url = wcfm_get_endpoint_url( $endpoint, '', $wcfm_page );
		return $wcfm_custom_menus_url;
	}
}

/**
 * WCFM - Custom Menus
 */
function wcfmcsm_wcfm_menus( $menus ) {
	global $WCFM;
	
	unset($menus['wcfm-products']['has_new']) ; 
	unset($menus['wcfm-orders']['has_new']) ;
	
	
	$settingmenu['wcfm-products'] = $menus['wcfm-products'] ;  
	$settingmenu['wcfm-orders'] = $menus['wcfm-orders'] ;
	$settingmenu['wcfm-settings'] = $menus['wcfm-settings'] ; 
	
	
	$menus = array();
	//deb($menus);exit();
	$menus = $settingmenu ; 
	
	$custom_menus = array( 'wcfm-ahli' => array(   'label'  => __( 'Members', 'wcfm-custom-menus'),
																									 'url'       => get_wcfm_custom_menus_url( 'wcfm-ahli' ),
																									 'icon'      => 'cubes',
																									 'priority'  => 6
																									) ,
																									
													'wcfm-profile' => array(   'label'  => __( 'Profile', 'wcfm-custom-menus'),
																										 'url'       => get_wcfm_custom_menus_url( 'wcfm-profile' ),
																										 'icon'      => 'cubes',
																										 'priority'  => 7
																										),
													'wcfm-claim' => array(   'label'  => __( 'Claim', 'wcfm-custom-menus'),
														'url'       => get_wcfm_custom_menus_url( 'wcfm-claim' ),
														'icon'      => 'cubes',
														'priority'  => 6.5
													), /* 
													'wcfm-upgrade' => array(   'label'  => __( 'Upgrade', 'wcfm-custom-menus'),
																										 'url'       => get_wcfm_custom_menus_url( 'wcfm-upgrade' ),
																										 'icon'      => 'cubes',
																										 'priority'  => 5.3
																										),
													'wcfm-contact' => array(   'label'  => __( 'Contact', 'wcfm-custom-menus'),
																										 'url'       => get_wcfm_custom_menus_url( 'wcfm-contact' ),
																										 'icon'      => 'cubes',
																										 'priority'  => 5.4
																										),
																										*/
													'wcfm-cubaan' => array(   'label'  => __( 'Cubaan', 'wcfm-custom-menus'),
																												 'url'       => get_wcfm_custom_menus_url( 'wcfm-cubaan' ),
																												 'icon'      => 'cubes',
																												 'priority'  => 8
																												)
																										
											);
	
	unset($custom_menus['wcfm-cubaan']) ; 
	$menus = array_merge( $menus, $custom_menus );
		
	//deb($menus)	;								
	return $menus;
}
add_filter( 'wcfm_menus', 'wcfmcsm_wcfm_menus', 70 );

/**
 *  WCFM - Custom Menus Views
 */
function wcfm_csm_load_views( $end_point ) {
	global $WCFM, $WCFMu;
	$plugin_path = trailingslashit( dirname( __FILE__  ) );
	//deb($end_point);exit();
	switch( $end_point ) {
		case 'wcfm-ahli':
			require_once( $plugin_path . 'views/wcfm-views-ahli.php' );
		break;

		case 'wcfm-ahli_manage':
			require_once( $plugin_path . 'views/wcfm-views-ahli_manage.php' );
		break;
		
		case 'wcfm-service':
			require_once( $plugin_path . 'views/wcfm-views-service.php' );
		break;
		
		case 'wcfm-upgrade':
			require_once( $plugin_path . 'views/wcfm-views-upgrade.php' );
		break;
		
		case 'wcfm-contact':
			require_once( $plugin_path . 'views/wcfm-views-contact.php' );
		break;
		
		case 'wcfm-cubaan':
			require_once( $plugin_path . 'views/wcfm-views-cubaan.php' );
		break;

		case 'wcfm-products':
			require_once( $plugin_path . 'views/wcfm-views-products.php' );
		break;

		case 'wcfm-products-manage':
			require_once( $plugin_path . 'views/wcfm-views-products-manage.php' );
		break;
		case 'wcfm-orders':
			require_once( $plugin_path . 'views/wcfm-views-orderslist.php' );
		break;

		case 'wcfm-orders-details':
			require_once( $plugin_path . 'views/wcfm-views-orders-details.php' );
		break;

		case 'wcfm-orders-manage':
			require_once( $plugin_path . 'views/wcfm-views-orders-manage.php' );
		break;

		case 'wcfm-claim':
			require_once( $plugin_path . 'views/wcfm-views-claim.php' );
		break;

		
		
	}
}
add_action( 'wcfm_load_views', 'wcfm_csm_load_views', 50 );
add_action( 'before_wcfm_load_views', 'wcfm_csm_load_views', 50 );

// Custom Load WCFM Scripts
function wcfm_csm_load_scripts( $end_point ) {
	global $WCFM;
	$plugin_url = trailingslashit( plugins_url( '', __FILE__ ) );
	
	switch( $end_point ) {
		case 'wcfm-ahli':
			$WCFM->library->load_datatable_lib();
			$WCFM->library->load_datatable_scroll_lib();
			$WCFM->library->load_datatable_download_lib();
			$WCFM->library->load_select2_lib();
			$WCFM->library->load_daterangepicker_lib();

			wp_enqueue_script( 'dataTables_colvis_js', $plugin_url . 'js/buttons.colVis.min.js', array('jquery', 'dataTables_js'), $WCFM->version, true );
			wp_enqueue_script( 'dataTables_checkboxes_min_js', $plugin_url . 'js/dataTables.checkboxes.min.js', array('jquery', 'dataTables_js'), $WCFM->version, true );
			wp_enqueue_script( 'wcfm_ahli_js', $plugin_url . 'js/wcfm-script-ahli.js', array( 'jquery' ), $WCFM->version, true );
			
			
			// Order Columns Defs
			$wcfm_datatable_column_defs = '[{ "targets": 0, "orderable" : false, "visible": true}, { "targets": 1, "orderable" : false, "visible": true }, { "targets": 2, "orderable" : false, "visible": true }, { "targets": 3, "orderable" : false, "visible": true }, { "targets": 4, "orderable" : false, "visible": true },{ "targets": 5, "orderable" : false, "visible": true },{ "targets": 6, "orderable" : false, "visible": true },{ "targets": 7, "orderable" : false, "visible": true }]';
															
			$wcfm_datatable_column_defs = apply_filters( 'wcfm_datatable_column_defs', $wcfm_datatable_column_defs, 'order' );

			// Order Columns Priority
			$wcfm_datatable_column_priority = '[{ "responsivePriority": 2 },{ "responsivePriority": 1 },{ "responsivePriority": 4 },{ "responsivePriority": 6 },{ "responsivePriority": 5 },{ "responsivePriority": 7 },{ "responsivePriority": 3 },{ "responsivePriority": 1 }]';
			$wcfm_datatable_column_priority = apply_filters( 'wcfm_datatable_column_priority', $wcfm_datatable_column_priority, 'order' );

			wp_localize_script( 'dataTables_js', 'wcfm_datatable_columns', array( 'defs' => $wcfm_datatable_column_defs, 'priority' => $wcfm_datatable_column_priority, 'bFilter' => apply_filters( 'wcfm_datatable_bfiltery', ( wcfm_is_vendor() ) ? true : true, 'order' ) ) );
			wp_localize_script( 'wcfm_ahli_js', 'wcfm_orders_auto_refresher', array( 'is_allow' => false, 'duration' => apply_filters( 'wcfm_order_auto_refresher_duration', 60000 ) ) );
			$wcfm_screen_manager_data    = array();
			$wcfm_screen_manager_hidden_data    = array();
	    	wp_localize_script( 'wcfm_ahli_js', 'wcfm_orders_screen_manage', $wcfm_screen_manager_data );
	    	wp_localize_script( 'wcfm_ahli_js', 'wcfm_orders_screen_manage_hidden', $wcfm_screen_manager_hidden_data );
	    	
		break;

		case 'wcfm-cubaan':
			$WCFM->library->load_datatable_lib();
			$WCFM->library->load_datatable_scroll_lib();
			$WCFM->library->load_datatable_download_lib();
			$WCFM->library->load_select2_lib();
			$WCFM->library->load_daterangepicker_lib();

			wp_enqueue_script( 'dataTables_colvis_js', $plugin_url . 'js/buttons.colVis.min.js', array('jquery', 'dataTables_js'), $WCFM->version, true );
			wp_enqueue_script( 'dataTables_checkboxes_min_js', $plugin_url . 'js/dataTables.checkboxes.min.js', array('jquery', 'dataTables_js'), $WCFM->version, true );
			wp_enqueue_script( 'wcfm_cubaan_js', $plugin_url . 'js/wcfm-script-cubaan.js', array( 'jquery' ), $WCFM->version, true );
	    	
		break;

	}
}

add_action( 'wcfm_load_scripts', 'wcfm_csm_load_scripts' );
add_action( 'after_wcfm_load_scripts', 'wcfm_csm_load_scripts' );

// Custom Load WCFM Styles
function wcfm_csm_load_styles( $end_point ) {
	global $WCFM, $WCFMu;
	$plugin_url = trailingslashit( plugins_url( '', __FILE__ ) );
	
	switch( $end_point ) {
		case 'wcfm-ahli':
			wp_enqueue_style( 'wcfmu_ahli_css', $plugin_url . 'css/wcfm-style-ahli.css', array(), $WCFM->version );
		break;
	}
}
add_action( 'wcfm_load_styles', 'wcfm_csm_load_styles' );
add_action( 'after_wcfm_load_styles', 'wcfm_csm_load_styles' );

/**
 *  WCFM - Custom Menus Ajax Controllers
 */
function wcfm_csm_ajax_controller() {
	global $WCFM, $WCFMu;
	
	$plugin_path = trailingslashit( dirname( __FILE__  ) );
	//deb($_POST['controller']);exit();
	$controller = '';
	if( isset( $_POST['controller'] ) ) {
		$controller = $_POST['controller'];
		//deb( $controller);exit();
		switch( $controller ) {
			case 'wcfm-ahli':
				
				require_once( $plugin_path . 'controllers/wcfm-controller-ahli.php' );
				new WCFM_ahli_Controller();
			break;
			case 'wcfm-cubaan':
				require_once( $plugin_path . 'controllers/wcfm-controller-cubaan.php' );
				new WCFM_cubaan_Controller();
			break;
		}
	}
}
add_action( 'after_wcfm_ajax_controller', 'wcfm_csm_ajax_controller' );


require_once( trailingslashit( dirname( __FILE__  ) ) . 'global/core.php' );
require_once( trailingslashit( dirname( __FILE__  ) ) . 'global/ahli.php' );
require_once( trailingslashit( dirname( __FILE__  ) ) . 'global/product.php' );
require_once( trailingslashit( dirname( __FILE__  ) ) . 'global/checkout.php' );
require_once( trailingslashit( dirname( __FILE__  ) ) . 'global/ahli_manage.php' );
require_once( trailingslashit( dirname( __FILE__  ) ) . 'global/woo_archive.php' );
require_once( trailingslashit( dirname( __FILE__  ) ) . 'global/woo_my_account.php' );
require_once( trailingslashit( dirname( __FILE__  ) ) . 'global/my-account_memberInfo.php' );
require_once( trailingslashit( dirname( __FILE__  ) ) . 'global/orders-manage.php' );
require_once( trailingslashit( dirname( __FILE__  ) ) . 'global/store-setup.php' );
require_once( trailingslashit( dirname( __FILE__  ) ) . 'global/vendor-register.php' );
require_once( trailingslashit( dirname( __FILE__  ) ) . 'global/orderslist.php' );
require_once( trailingslashit( dirname( __FILE__  ) ) . 'global/store/info_kariah_tab.php' );
require_once( trailingslashit( dirname( __FILE__  ) ) . 'class/class_member.php' );
require_once( trailingslashit( dirname( __FILE__  ) ) . 'class/class_claim.php' );