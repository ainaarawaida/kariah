<?php 
namespace WCOA\classes\frontend;

class ApprovalArea
{
	//rplc: woocommerce-order-approval, wcoa, WCOA
	public function __construct()
	{
		add_action( 'woocommerce_order_details_before_order_table',  array(&$this,'render_approval_area') );
		
		add_action( 'wp_ajax_nopriv_wcoa_reload_approval_area', array( &$this,'ajax_reload_approval_area')); 
		add_action( 'wp_ajax_wcoa_reload_approval_area', array( &$this,'ajax_reload_approval_area')); 
		
	}	
	public function render_approval_page()
	{
		echo "here";
	}
	function ajax_reload_approval_area()
	{
		$order_id = wcoa_get_value_if_set($_POST, 'order_id', false);
		if($order_id)
		{
			$order = wc_get_order($order_id);
			if($order)
			{
				echo $this->render_approval_area($order, true);
			}
			else echo "error";
		}
		else echo "error";
		wp_die();
	}
	public function render_approval_area($order, $is_ajax = false)
	{
		global $wcoa_wpml_model, $wcoa_option_model, $wcoa_order_model, $wcoa_time_model;
		$lang = $wcoa_wpml_model->get_current_language();
		$settings = $wcoa_option_model->get_text_options();
		$approval_status = $wcoa_order_model->get_approval_status($order);
		$specific_approval_data = $wcoa_order_model->get_approval_data($order->get_id(), true);
		$order_url = $wcoa_order_model->get_order_details_page_url($order);
		$wcoa_order_model->has_to_be_automatically_cancelled($order);
		$options = $wcoa_option_model->get_options();
		$approval_workflow = wcoa_get_value_if_set($options, array('approval_workflow','type'), 'pay_and_wait');
		
		if(isset($_GET['wcoa_order_paid_redirection']) || ($approval_workflow == 'wait_and_pay' && isset($_GET['utm_nooverride'])) || wcoa_get_value_if_set($_GET, 'cancel_order', 'false') == 'true'  )
			return;
		
		if(!$is_ajax)
		{
			wp_enqueue_style('wcoa-progress-tracker', WCOA_PLUGIN_PATH.'/css/vendor/progress-tracker/progress-tracker.css');
			wp_enqueue_style('wcoa-approval-area', WCOA_PLUGIN_PATH.'/css/frontend-approval-area.css');
			
			wp_register_script( 'wcoa-approval-area', WCOA_PLUGIN_PATH.'/js/frontend-approval-area.js' );
			$js_options = array(
					'confirm_text' => esc_html__( 'are you sure?', 'woocommerce-order-approval' ),
					'order_status' => $order->get_status(),
					'order_id' 	   => $order->get_id(),
					'is_order_received_page' 	   => is_order_received_page() ? 'yes' : 'no',
					'ajaxurl' 	   => admin_url( 'admin-ajax.php' )
				);
			wp_localize_script( 'wcoa-approval-area', 'wcoa', $js_options );
			wp_enqueue_script( 'wcoa-approval-area' );
			echo '<div id="wcoa_status_timeline_container">';
		}
		if(file_exists ( get_theme_file_path()."/woocommerce-order-approval/frontend/approval_area.php" ))
			include get_theme_file_path()."/woocommerce-order-approval/frontend/approval_area.php";
		else
			include WCOA_PLUGIN_ABS_PATH.'/templates/frontend/approval_area.php';
		if(!$is_ajax)
			echo '</div>';
	}
}
?>