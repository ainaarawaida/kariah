<?php 
namespace WCOA\classes\admin;

class OrderPage
{
	//rplc: woocommerce-order-approval, wcoa, WCOA
	public function __construct()
	{
		add_action( 'add_meta_boxes', array( &$this, 'woocommerce_metaboxes' ));
		//add_action( 'woocommerce_admin_order_data_after_order_details', array( &$this, 'render_approval_area' ));
		add_action( 'woocommerce_process_shop_order_meta', array( &$this, 'process_shop_ordermeta' ), 5, 2 );
		add_filter( 'woocommerce_admin_billing_fields',  array( &$this,'display_order_custom_fields' )); //admin order details page
		//add_filter( 'admin_notices',  array( &$this,'display_notices' ));
	}
	function woocommerce_metaboxes() 
	{
		add_meta_box( 'woocommerce-order-approval', esc_html__('Approval - Custom data', 'woocommerce-order-approval'), array( &$this, 'render_approval_area' ), 'shop_order', 'normal', 'default');
	}
	function display_notices()
	{
		
	}
	function render_approval_area($post) 
	{
		wp_enqueue_style('wcoa-order-page',  WCOA_PLUGIN_PATH.'/css/admin-order-page.css');
		wp_enqueue_style('wcoa-order-page-timepickeraddon',  WCOA_PLUGIN_PATH.'/css/vendor/timepickeraddon/jquery-ui-timepicker-addon.css');
		wp_enqueue_style( 'jquery-ui-timepicker' );
		wp_enqueue_style( 'jquery-ui-datepicker' );
		
		wp_enqueue_script( 'wcoa-order-page', WCOA_PLUGIN_PATH.'/js/admin-order-page.js', array('jquery'));
		wp_enqueue_script( 'wcoa-order-page-timepickeraddon', WCOA_PLUGIN_PATH.'/js/vendor/timepickeraddon/jquery-ui-timepicker-addon.js', array('jquery'));
		wp_enqueue_script('jquery-ui-timepicker');
		wp_enqueue_script('jquery-ui-datepicker');

		global $wcoa_order_model;
		$approval_data = $wcoa_order_model->get_approval_data($post->ID);
		?>
		<div id="wcoa_approval_area">
			<label><?php esc_html_e('Date and time', 'woocommerce-order-approval') ?></label> 
			<p><?php _e('You can optionally specify a date and time that can be displayed inside the approval message through the <strong>[datetime]</strong> shortcode.', 'woocommerce-order-approval') ?></p> 
			<?php $content = wcoa_get_value_if_set($approval_data , 'datetime', ""); ?>
			<input id="wcoa_datetime_picker" name="wcoa_datetime" type="text" value="<?php echo esc_attr($content); ?>"></input>
			
			<label><?php esc_html_e('Custom approval message', 'woocommerce-order-approval') ?></label> 
			<p><?php _e('This message will override the Approved message and Rejected message message configured through the WooCommerce Order Approval -> Text menu. You can use the special <strong>[datetime]</strong> shortcode to display the configured date and time.', 'woocommerce-order-approval') ?></p>
			<?php 
			$content = wcoa_get_value_if_set($approval_data , 'custom_message', "");
			wp_editor( $content, "wcoa_custom_message", array( 'media_buttons' => false,
																	   'textarea_rows' => 6,
																	   'tinymce' => true,
																	   "wpautop" => false,
																	   'textarea_name'=>"wcoa_custom_message"));
			?>
		</div>
		<?php 
	}
	function process_shop_ordermeta( $order_id, $post_obj ) 
	{
		global $wcoa_order_model;
		$wcoa_order_model->save_approval_data($_POST, $order_id);
	}
	public function display_order_custom_fields($fields, $type = 'billing')
	{
		global $wcoa_order_model, $post;
		
		if(!isset($post))
			return $fields;
		
		$order = wc_get_order($post->ID);
		$time = $wcoa_order_model->get_time_field($order);
		if($time)
		{
			$form_field = array();
			$form_field['type'] = $time == 'as_soon_as_possible' ? 'text' : 'time';
			//$form_field['name'] = '_billing_wcoa_time';
			$form_field['label'] = esc_html__('Time','woocommerce-order-approval');
			$form_field['value'] = $time == 'as_soon_as_possible' ? esc_html__('As soon as possible','woocommerce-order-approval') : $time;
			//add_filter( 'woocommerce_order_get__billing_wcoa_time', array(&$this, 'render_time_field' ), 1, 2);
			$fields["wcoa_time"] = $form_field;
		}	
		
		
		return $fields;
	}
	
	public function render_time_field($value, $order)
	{
		return $value;
	}
}
?>