<?php
/*
Plugin Name: WooCommerce Order Approval
Description: Order approval system.
Author: Lagudi Domenico
Version: 4.7
*/


define('WCOA_PLUGIN_PATH', rtrim(plugin_dir_url(__FILE__), "/") ) ;
define('WCOA_PLUGIN_LANG_PATH', basename( dirname( __FILE__ ) ) . '/languages' ) ;
define('WCOA_PLUGIN_ABS_PATH', dirname( __FILE__ ) );


if ( !defined('WP_CLI') && ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ||
					   (is_multisite() && array_key_exists( 'woocommerce/woocommerce.php', get_site_option('active_sitewide_plugins') ))
					 )	
	)
{
	$wcoa_id = "";
	$wcoa_name = "WooCommerce Order Approval";
	$wcoa_activator_slug = "wcoa-activator";
	
	include 'classes/com/Globals.php';
	require_once('classes/admin/ActivationPage.php');
	
	add_action('init', 'wcoa_init');
	add_action('admin_menu', 'wcoa_init_act');
	if(defined('DOING_AJAX') && DOING_AJAX)
			wcoa_init_act();
	add_action('admin_notices', 'wcoa_admin_notices' );
}
function wcoa_admin_notices()
{
	global $wcoa_notice, $wcoa_name, $wcoa_activator_slug;
	if($wcoa_notice && (!isset($_GET['page']) || $_GET['page'] != $wcoa_activator_slug))
	{
		 ?>
		<div class="notice notice-success">
			<p><?php echo sprintf(__( 'To complete the <span style="color:#96588a; font-weight:bold;">%s</span> plugin activation, you must verify your purchase license. Click <a href="%s">here</a> to verify it.', 'woocommerce-order-approval' ), $wcoa_name, get_admin_url()."admin.php?page=".$wcoa_activator_slug); ?></p>
		</div>
		<?php
	}
}
function wcoa_init_act()
{
	global $wcoa_activator_slug, $wcoa_name, $wcoa_id;
	new WCOA\classes\admin\ActivationPage($wcoa_activator_slug, $wcoa_name, 'woocommerce-order-approval', $wcoa_id, WCOA_PLUGIN_PATH);
}

function wcoa_init()
{
	load_plugin_textdomain('woocommerce-order-approval', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
function wcoa_setup()
{
	global $wcoa_option_model, $wcoa_wpml_model, $wcoa_order_model, $wcoa_shortcode_model, $wcoa_time_model, $wcoa_product_model, $wcoa_user_model, $wcoa_cron_model;
	
	//com
	if(!class_exists('WCOA\classes\com\Order'))
	{
		require_once('classes/com/Order.php');
		$wcoa_order_model = new WCOA\classes\com\Order();
	}
	if(!class_exists('WCOA\classes\com\Option'))
	{
		require_once('classes/com/Option.php');
		$wcoa_option_model = new WCOA\classes\com\Option();
	}
	if(!class_exists('WCOA\classes\com\Wpml'))
	{
		require_once('classes/com/Wpml.php');
		$wcoa_wpml_model = new WCOA\classes\com\Wpml();
	}
	if(!class_exists('WCOA\classes\com\Email_Manager'))
	{
		require_once('classes/com/Email_Manager.php');
		new WCOA\classes\com\Email_Manager();
	}
	if(!class_exists('WCOA\classes\com\Shortcode'))
	{
		require_once('classes/com/Shortcode.php');
		$wcoa_shortcode_model = new WCOA\classes\com\Shortcode();
	}
	if(!class_exists('WCOA\classes\com\Time'))
	{
		require_once('classes/com/Time.php');
		$wcoa_time_model = new WCOA\classes\com\Time();
	}
	if(!class_exists('WCOA\classes\com\Product'))
	{
		require_once('classes/com/Product.php');
		$wcoa_product_model = new WCOA\classes\com\Product();
	}
	if(!class_exists('WCOA\classes\com\User'))
	{
		require_once('classes/com/User.php');
		$wcoa_user_model = new WCOA\classes\com\User();
	}
	if(!class_exists('WCOA\classes\com\Cron'))
	{
		require_once('classes/com/Cron.php');
		//$wcoa_cron_model = new WCOA\classes\com\Cron();
	}
		
	//admin 
	if(!class_exists('WCOA\classes\admin\SettingsPage'))
	{
		require_once('classes/admin/SettingsPage.php');
		new WCOA\classes\admin\SettingsPage();
	}
	if(!class_exists('WCOA\classes\admin\TextsPage'))
	{
		require_once('classes/admin/TextsPage.php');
		new WCOA\classes\admin\TextsPage();
	}
	if(!class_exists('WCOA\classes\admin\OrdersListPage'))
	{
		require_once('classes/admin/OrdersListPage.php');
		new WCOA\classes\admin\OrdersListPage();
	}
	if(!class_exists('WCOA\classes\admin\OrderPage'))
	{
		require_once('classes/admin/OrderPage.php');
		new WCOA\classes\admin\OrderPage();
	}
	if(!class_exists('WCOA\classes\admin\CouponSettingsPage'))
	{
		require_once('classes/admin/CouponSettingsPage.php');
		new WCOA\classes\admin\CouponSettingsPage();
	}
	
	//frontend
	if(!class_exists('WCOA\classes\frontend\ApprovalArea'))
	{
		require_once('classes/frontend/ApprovalArea.php');
		new WCOA\classes\frontend\ApprovalArea();
	}
	if(!class_exists('WCOA\classes\frontend\FieldDisplayManagement'))
	{
		require_once('classes/frontend/FieldDisplayManagement.php');
		new WCOA\classes\frontend\FieldDisplayManagement();
	}
	
	add_action('admin_menu', 'wcoa_init_admin_panel');
}
function wcoa_init_admin_panel()
{
	if(!current_user_can('manage_woocommerce'))
		return;
	
	$place = wcoa_get_free_menu_position(59 , .1);
	$cap = 'manage_woocommerce';
	
	add_menu_page( 'WooCommerce Order Approval', __('WooCommerce Order Approval', 'woocommerce-order-approval'), $cap, 'wcoa-woocommerce-order-approval', null,  "dashicons-yes" , (string)$place);
	add_submenu_page( 'wcoa-woocommerce-order-approval', __('WooCommerce Order Approval - Settings', 'woocommerce-order-approval'),  __('Settings', 'woocommerce-order-approval'), $cap, 'woocommerce-order-approval-settings-page', 'wcoa_render_admin_page' );
	add_submenu_page( 'wcoa-woocommerce-order-approval', __('WooCommerce Order Approval - Texts', 'woocommerce-order-approval'),  __('Texts', 'woocommerce-order-approval'), $cap, 'woocommerce-order-approval-texts-page', 'wcoa_render_admin_page' );
	remove_submenu_page( 'wcoa-woocommerce-order-approval', 'wcoa-woocommerce-order-approval'); 
}
function wcoa_render_admin_page()
{
	if(!isset($_REQUEST['page']))
		return;
	switch($_REQUEST['page'])
	{
		case 'woocommerce-order-approval-settings-page':
			$settings_page = new WCOA\classes\admin\SettingsPage();
			$settings_page->render_page();
		break;
		case 'woocommerce-order-approval-texts-page':
			$settings_page = new WCOA\classes\admin\TextsPage();
			$settings_page->render_page();
		break;
	}
}
function wcoa_get_free_menu_position($start, $increment = 0.1)
{
	foreach ($GLOBALS['menu'] as $key => $menu) {
		$menus_positions[] = $key;
	}
	
	if (!in_array($start, $menus_positions)) return $start;

	/* the position is already reserved find the closet one */
	while (in_array($start, $menus_positions)) {
		$start += $increment;
	}
	return $start;
}