<?php 
namespace WCOA\classes\com;

class Order
{
	//rplc: woocommerce-order-approval, wcoa, WCOA
	var $notice_to_show = 'none';
	var $is_checkout_order_review = false;
	public function __construct()
	{	
		//Payment
		add_filter( 'woocommerce_order_needs_payment', array(&$this, 'order_needs_payment') );  //Added in WooCommerce 4.7: it is triggered when the order is placed, and if the returned value is not the same of 'woocommerce_cart_needs_payment', the checkout fails
		add_action('woocommerce_checkout_process', array(&$this, 'checkout_update_order_review'));
		add_filter( 'woocommerce_cart_needs_payment', array(&$this, 'cart_needs_payment'), 10, 2 ); //class-wc-cart.php: checks if the order needs to be payed after it has been placed 
		//
		add_filter( 'wc_order_is_editable', array(&$this, 'is_editable'), 10, 2 ); 
		////add_filter( 'woocommerce_payment_complete_order_status', array(&$this, 'set_order_status_after_payment'), 10, 2 );  
		add_action( 'woocommerce_checkout_create_order', array( &$this,'on_checkout_order_created'), 10, 2 );
		////add_action('woocommerce_checkout_order_processed', array( &$this, 'set_order_status_after_checkout' ), 10, 1); //After checkout
		////add_action( 'woocommerce_thankyou', array( &$this,'set_order_status_after_checkout') );
		add_filter('woocommerce_payment_successful_result',array(&$this, 'on_checkout_payment_successful_result') ,10,2); 				//class-wc-checkout.php: triggered after a succesful payment (both on creation or triggered in a second time)
		add_filter('woocommerce_checkout_no_payment_needed_redirect',array(&$this, 'on_checkout_no_payment_needed_redirect') ,10,2); 	//class-wc-checkout.php: triggered after the order has been created if it doesn't need a payment
		add_filter('woocommerce_pay_order_product_in_stock',array(&$this, 'on_pay_order_product_in_stock') ,10,3); 						//class-wc-shortcode-checkout.php
		add_filter('woocommerce_pay_order_product_has_enough_stock',array(&$this, 'on_pay_order_product_has_enough_stock') ,10,3);		//class-wc-shortcode-checkout.php
		
		//Stock 
		add_action( 'woocommerce_order_status_approved', 'wc_maybe_reduce_stock_levels' );
		add_action( 'woocommerce_order_status_rejected', 'wc_maybe_increase_stock_levels' );
		
		//Statuses considered valid to pay the order
		add_filter('woocommerce_valid_order_statuses_for_payment',array(&$this, 'valid_order_statuses_for_payment') ,10,2);
		
		//Custom status
		//add_action( 'init', array( &$this, 'add_order_status' ));
		add_filter( 'woocommerce_register_shop_order_post_statuses', array( &$this, 'add_order_status' ) );
		add_filter( 'wc_order_statuses', array( &$this, 'add_custom_order_status' ) );
		
		//Order status change
		add_action( 'woocommerce_order_status_changed', array( &$this,'order_status_changed'), 10, 4 ); 
		
		add_filter( 'woocommerce_valid_order_statuses_for_cancel', array( &$this,'set_order_statuses_for_cancel'), 10, 2 );
		
		//Order status check
		add_action( 'wp_ajax_nopriv_wcoa_check_order_status', array( &$this,'ajax_check_order_status')); 
		add_action( 'wp_ajax_wcoa_check_order_status', array( &$this,'ajax_check_order_status')); 
		
		//Allows downloadable products to be included in Approval notification email
		add_filter('woocommerce_order_is_download_permitted', array( &$this,'is_download_permitted'), 10, 2);
		add_filter('woocommerce_order_is_paid_statuses', array( &$this,'manage_paid_statuses'));
		
		add_action('init', array( &$this,'check_direct_approval_request_and_other'));
		add_action('wp_print_scripts', array( &$this,'show_notice'));
	}
	function show_notice()
	{
		switch($this->notice_to_show)
			{
				case 'approved':
				?>
				<script>
					window.alert("<?php esc_html_e('The order has been successfully approved!', 'woocommerce-order-approval'); ?>");
				</script>
				<?php
				break;
				case 'rejected':
				?>
				<script>
					window.alert("<?php esc_html_e('The order has been successfully rejected!', 'woocommerce-order-approval'); ?>");
				</script>
				<?php
				break;
			}
		$this->notice_to_show = 'none';
	}
	function check_direct_approval_request_and_other()
	{
		global $wcoa_option_model, $post;
		$options = $wcoa_option_model->get_options();
		$approval_workflow = wcoa_get_value_if_set($options, array('approval_workflow','type'), 'pay_and_wait');
		
		if(isset($_GET['wcoa_action']) && isset($_GET['post']) && isset($_GET['order_key']))
		{
			$action = $_GET['wcoa_action'];
			$order_id = $_GET['post'];
			$order_key = $_GET['order_key'];
			$order = wc_get_order($order_id );
			
			if(!$order || $order_key != $order->get_order_key())
				return;
			
			
			switch($_GET['wcoa_action'])
			{
				case 'approve':
				/* ?>
					<div style="margin-top:100px" class="notice notice-success is-dismissible">
						<p><?php  esc_html_e('The order has been successfully approved!', 'woocommerce-order-approval'); ?></p>
					</div>
				<?php */
					$this->notice_to_show = 'approved';
					
					$order->set_status('approved', '' , true);
					$order->save();
				break;
				case 'reject':
					$this->notice_to_show = 'rejected';
					
					$order->set_status('rejected', '' , true);
					$order->save();
					/* ?>
						<div style="margin-top:100px" class="notice notice-success is-dismissible">
							<p><?php  esc_html_e('The order has been successfully rejected!', 'woocommerce-order-approval'); ?></p>
						</div>
					<?php */
				break;
			}
		}
		else if(wcoa_get_value_if_set($_GET, 'cancel_order', 'false') == 'true' && wcoa_get_value_if_set($_GET, 'order', false) &&  wcoa_get_value_if_set($_GET, 'order_id', false))
		{
			$order = wc_get_order($_GET['order_id']);
			if($order && $_GET['order'] == $order->get_order_key())
			{
				$order->set_status('cancelled');
				$order->save();
			}
		}
		//Force status after returning from a payment (PayPal)
		else if($approval_workflow == 'wait_and_pay' && isset($_GET['utm_nooverride'])  && isset($_GET['key']))
		{
			$order_id = wc_get_order_id_by_order_key($_GET['key']);
			$order_key = $_GET['key'];
			$order = wc_get_order($order_id );
			
			if(!$order)
				return;
			
			if($order->get_status() == 'approved')
			{
				$order->set_status('processing');
				$order->save();
			}
		}
		
	}
	
	function ajax_check_order_status()
	{
		$order_id = wcoa_get_value_if_set($_POST, 'order_id', false);
		if($order_id)
		{
			$order = wc_get_order($order_id);
			if($order)
			{
				$this->has_to_be_automatically_cancelled($order);
				echo $order->get_status();
			}
			else echo "error";
		}
		else echo "error";
		wp_die();
	}
	function manage_paid_statuses($statuses)
	{
		global $wcoa_option_model;
		$options = $wcoa_option_model->get_options();
		
		if(wcoa_get_value_if_set($options, array('approval_workflow','type'), 'pay_and_wait') == 'pay_and_wait')
		{
			$statuses[] = 'approved';
			$statuses[] = 'rejected';
			$statuses[] = 'approval-waiting';
		}
		return $statuses;
	}
	function is_download_permitted($can_be_downloaded, $wc_order)
	{
		global $wcoa_option_model;
		$options = $wcoa_option_model->get_options();
		
		if(wcoa_get_value_if_set($options, array('approval_workflow','type'), 'pay_and_wait') == 'pay_and_wait')
		{
			if($wc_order->has_status( 'approved' ))
				$can_be_downloaded = true;
		}	
		
		return $can_be_downloaded;
	}
	function set_order_statuses_for_cancel( $statuses, $order )
	{
		if(isset($_GET['wcoa_cancel']))
			$statuses[] = $order->get_status();
		
		return $statuses;
	}
	public function get_lang($order)
	{
		global $wcoa_wpml_model;
		
		$wc_order = is_object($order) ? $order : wc_get_order($order);
		$default = $wcoa_wpml_model->get_current_language(); //exameple en;

		if(!isset($order) || $order == false)
			return $default;
		
		$result = $wc_order->get_meta('wpml_language');
		$result = !$result || !is_string($result) || $result == "" ? $default : $result;
		
		return strtolower($result);
	}
	function add_order_status( $order_statuses )
	{
    
	    // Status must start with "wc-"
	   $order_statuses['wc-approval-waiting'] = array(                                 
	   'label'                     => esc_html__( 'Waiting for approval', 'Order status', 'woocommerce-mark-order-status' ),
	   'public'                    => true,                                 
	   'exclude_from_search'       => false,                                 
	   'show_in_admin_all_list'    => true,                                 
	   'show_in_admin_status_list' => true,                                 
	   'label_count'               => _n_noop( 'Waiting for approval <span class="count">(%s)</span>', 'waiting for approval <span class="count">(%s)</span>', 'woocommerce-mark-order-status' ),                              
	   );      
	   $order_statuses['wc-approved'] = array(                                 
	   'label'                     => esc_html__( 'Approved', 'Order status', 'woocommerce' ),
	   'public'                    => true,                                 
	   'exclude_from_search'       => false,                                 
	   'show_in_admin_all_list'    => true,                                 
	   'show_in_admin_status_list' => true,                                 
	   'label_count'               => _n_noop( 'Approved <span class="count">(%s)</span>', 'Approved <span class="count">(%s)</span>', 'woocommerce-mark-order-status' ),                              
	   ); 
		$order_statuses['wc-rejected'] = array(                                 
	   'label'                     => esc_html__( 'Rejected', 'Order status', 'woocommerce' ),
	   'public'                    => true,                                 
	   'exclude_from_search'       => false,                                 
	   'show_in_admin_all_list'    => true,                                 
	   'show_in_admin_status_list' => true,                                 
	   'label_count'               => _n_noop( 'Rejected <span class="count">(%s)</span>', 'Rejected <span class="count">(%s)</span>', 'woocommerce-mark-order-status' ),                              
	   );	   
	   return $order_statuses; 
	
	}
	
	function add_custom_order_status( $order_statuses ) 
	{      
	   $order_statuses['wc-approval-waiting'] = esc_html__( 'Waiting for approval', 'woocommerce-order-approval' );       
	   $order_statuses['wc-approved'] = esc_html__( 'Approved', 'woocommerce-order-approval' );       
	   $order_statuses['wc-rejected'] = esc_html__( 'Rejected', 'woocommerce-order-approval' );       
	   return $order_statuses;
	}
	//End custom status

	
	public function save_approval_data($posted_data, $order_id)
	{
		$order = wc_get_order($order_id);
		if(!isset($order) || $order == false)
			return;
		
		$order->update_meta_data('wcoa_custom_message', stripslashes(wcoa_get_value_if_set($posted_data, 'wcoa_custom_message', "")));
		$order->update_meta_data('wcoa_datetime', wcoa_get_value_if_set($posted_data, 'wcoa_datetime', ""));
		$order->save();
	}
	public function get_approval_data($order_id, $replace_shortcode = false)
	{
		$order = wc_get_order($order_id);
		$result = array('wcoa_custom_message' => "", 'wcoa_datetime' =>"");
		if(!isset($order) || $order == false)
			return $result;
		
		$result['custom_message'] = $order->get_meta('wcoa_custom_message');	
		$result['datetime'] = $order->get_meta('wcoa_datetime');	
		if($replace_shortcode)
		{
			$date = date_create($result['datetime']);
			$date_time = date_format($date,get_option('date_format')." ".get_option('time_format'));
			$result['custom_message'] = str_replace('[datetime]', $date_time, $result['custom_message']);
		}
		return $result;
	}
	public function is_editable($is_editable, $order)
	{
		return $order->get_status() == 'approval-waiting' ? true : $is_editable;
	}
	public function checkout_update_order_review($posted_data)
	{
		$this->is_checkout_order_review = true;
	}
	public function order_needs_payment($order_needs_payment)
	{
		global $wcoa_option_model;
		$options = $wcoa_option_model->get_options();
		if($this->is_checkout_order_review)
		{
			return wcoa_get_value_if_set($options, array('approval_workflow','type'), 'pay_and_wait') == 'pay_and_wait';
		}
		return $order_needs_payment;
	}
	public function cart_needs_payment($cart_needs_to_be_payment, $cart = null ) //$istance is WC_Cart obj?
	{
		global $wcoa_option_model;
		$options = $wcoa_option_model->get_options();
		return wcoa_get_value_if_set($options, array('approval_workflow','type'), 'pay_and_wait') == 'pay_and_wait';
	}
	public function valid_order_statuses_for_payment($statuses, $order = null)
	{
		global $wcoa_option_model;
		$options = $wcoa_option_model->get_options();
		if(wcoa_get_value_if_set($options, array('approval_workflow','type'), 'pay_and_wait') == 'wait_and_pay')
			$statuses[] = 'approved';
		
		return $statuses;
	}
	public function on_pay_order_product_has_enough_stock($stock_quantity, $product, $order)
	{
		global $wcoa_option_model;
		
		//Stock check
		$hold_stock_minutes = (int) get_option( 'woocommerce_hold_stock_minutes', 0 );
		$quantities = array();
		foreach ( $order->get_items() as $item_key => $item ) 
		{
			if ( $item && is_callable( array( $item, 'get_product' ) ) ) 
			{
				$product = $item->get_product();

				if ( ! $product ) {
					continue;
				}

				$quantities[ $product->get_stock_managed_by_id() ] = isset( $quantities[ $product->get_stock_managed_by_id() ] ) ? $quantities[ $product->get_stock_managed_by_id() ] + $item->get_quantity() : $item->get_quantity();
			}
		}
		$held_stock     = ( $hold_stock_minutes > 0 ) ? wc_get_held_stock_quantity( $product, $order->get_id() ) : 0;
		$required_stock = $quantities[ $product->get_stock_managed_by_id() ];
		//
					
		$options = $wcoa_option_model->get_options();
		$has_to_be_approved = wcoa_get_value_if_set($options, array('approval_workflow','type'), 'pay_and_wait') == 'wait_and_pay';
		$stock_quantity = $has_to_be_approved == 'wait_and_pay' ? $product->get_stock_quantity() >= 0 /* $product->get_stock_quantity() >= ( $held_stock + $required_stock ) */  : $stock_quantity;
		
		return $stock_quantity;
	}
	public function on_pay_order_product_in_stock($is_in_stock, $product, $order)
	{
		global $wcoa_option_model;
		$options = $wcoa_option_model->get_options();
		$has_to_be_approved = wcoa_get_value_if_set($options, array('approval_workflow','type'), 'pay_and_wait') == 'wait_and_pay';
		$is_in_stock = $has_to_be_approved == 'wait_and_pay' ? true : $is_in_stock;
			
		return $is_in_stock;
	}
	public function on_checkout_order_created($order, $data)
	{
		global $wcoa_option_model;
		$options = $wcoa_option_model->get_options();
		$has_to_be_approved = wcoa_get_value_if_set($options, array('approval_workflow','type'), 'pay_and_wait') == 'wait_and_pay';
		$order->update_meta_data('wcoa_workflow_type', wcoa_get_value_if_set($options, array('approval_workflow','type'), 'pay_and_wait'));
		$order->save();
		
		/*if($has_to_be_approved) //It has to be approved first, so no stock reduction
			$this->restore_order_stock($order->get_id(), $order, 'increase'); //avoid unnecessary stock reduction*/
	}
	public function can_be_automatically_approved($order)
	{
		global $wcoa_option_model, $wcoa_user_model;
		$options = $wcoa_option_model->get_options();
		
		//automatic approval per payment gateway
		$automatic_approval_payment_gateway = wcoa_get_value_if_set($options, array('automatic_approval','payment_gateways', $order->get_payment_method()), false); 
	
		//automatic approval per shipping
		$automatic_approval_shipping_method  = false;
		if(count($order->get_shipping_methods()) > 0)
		{
			$first_method = array_values($order->get_shipping_methods())[0]; //NOTE: there could be more than 1 shipping
			$shipping_method_id = $first_method ->get_method_id().":".$first_method ->get_instance_id(); 
			$automatic_approval_shipping_method = wcoa_get_value_if_set($options, array('automatic_approval','shipping_method', $shipping_method_id), false);  	
		}
		//automatic approval per product  
		$automatic_approval_by_products = $this->automatic_approval_according_products($order, $options);
		
		//roles 
		$selected_roles = wcoa_get_value_if_set($options, array('automatic_approval','user_role'), array());
		$automatic_approva_user_roles = $wcoa_user_model->belogs_to_roles($selected_roles);
		
		//order subtotal
		$value = wcoa_get_value_if_set($options, array('automatic_approval','order_total'), "");
		$automatic_approval_order_subtotal = false;
		if($value != 0 && $value  != "")
			$automatic_approval_order_subtotal = $value < $order->get_subtotal(); //sum of the products excluding taxes
		
		//coupon 
		$coupons = $order->get_coupon_codes();
		$automatic_approval_coupon = false;
		foreach((array)$coupons as $coupon_id)
		{
			if($automatic_approval_coupon)
				break;
			$coupon    = new \WC_Coupon( $coupon_id );
			$automatic_approval_coupon = $coupon->get_meta( 'wcoa_automatic_approval' ) ? $coupon->get_meta( 'wcoa_automatic_approval' ) : false;
		}
		
		if($automatic_approval_coupon || $automatic_approval_payment_gateway || $automatic_approval_shipping_method || $automatic_approval_by_products || $automatic_approval_order_subtotal || $automatic_approva_user_roles)
		{
			$order->update_meta_data('wcoa_skip_email_notification',  true);
			$order->save();
			return true;
		}
		$order->update_meta_data('wcoa_skip_email_notification',  false);
		return false;
	}
	public function on_checkout_payment_successful_result($result, $order_id)
	{
		
		global $wcoa_option_model;
		$order = wc_get_order($order_id);
		$options = $wcoa_option_model->get_options();
		
		//wait and pay: if the payment has been performed, this code is executed
		if(did_action('woocommerce_checkout_create_order')  == 0 ) //Action triggred when the order is created after checkout
		{
			//$had_to_be_approved = $order->get_meta('wcoa_status_chage_date') == 'wait_and_pay';
			//$had_to_be_approved = wcoa_get_value_if_set($options, array('approval_workflow','type'), 'pay_and_wait');
			/* if($had_to_be_approved) 
				$this->restore_order_stock($order->get_id(), $order, 'decrease'); //decrease quantity after pay */
			
			$result['redirect'] = add_query_arg('wcoa_order_paid_redirection', true, $result['redirect']);
			return $result;
		}
	
		
		if(!isset($order) || is_bool($order))
			return $result;
		
		$can_be_automatically_approved = $this->can_be_automatically_approved($order);
		
		$order->update_status(!$can_be_automatically_approved ? 'approval-waiting' : 'approved', '', true);
		return $result;
	}	
	public function on_checkout_no_payment_needed_redirect($url, $order)
	{
		
		if(did_action('woocommerce_checkout_create_order') == 0) //Action triggred when the order is created after checkout
			return $url;
		
		global $wcoa_option_model;
		$options = $wcoa_option_model->get_options();
		
		$can_be_automatically_approved = $this->can_be_automatically_approved($order);
		
		$order->update_status(!$can_be_automatically_approved ? 'approval-waiting' : 'approved', '', true);
		return $url;
	}
	public function order_status_changed( $this_get_id, $this_status_transition_from, $this_status_transition_to, $order )
	{
	
		if( $this_status_transition_from == 'approval-waiting' && ($this_status_transition_to == 'rejected' || $this_status_transition_to == 'approved'))
		{
			$order->update_meta_data('wcoa_status_chage_date',  current_time( 'mysql' ));
			$order->save();
		}
		else if($this_status_transition_to == 'approval-waiting' )
		{
			$order->update_meta_data('wcoa_status_chage_date',  "");
			$order->save();
		}
	}
	//approval-waiting || approved || rejected
	public function get_approval_status($order)
	{
		if($this->is_approval_waiting($order))
		{
			return 'approval-waiting';
		}
		
		return $this->is_approved($order) ? 'approved' : 'rejected';
	}
	public function is_approval_waiting($order)
	{
		global $wcoa_option_model;
		$options = $wcoa_option_model->get_options();
		$workflow_type = wcoa_get_value_if_set($options, array('approval_workflow','type'), 'pay_and_wait');
		return $order->get_status() == 'approval-waiting' || ( $workflow_type == 'pay_and_wait' && $order->get_status() == 'pending');
	}
	public function is_rejected($order)
	{
		$rejected_statuses = array('rejected', 'failed', 'cancelled', 'refunded');
		return in_array($order->get_status(),$rejected_statuses );
	}
	public function is_approved($order)
	{
		return !$this->is_rejected($order) && $order->get_status() != 'approval-waiting';
	}
	public function get_creation_date($order)
	{
		return $order->get_date_created()->format(get_option('date_format')." ".get_option('time_format'));
	}
	public function get_approval_date($order, $raw = false)
	{
		$date = $order->get_meta('wcoa_status_chage_date');
		if($raw)
			return $date;
		
		if($date)
		{
			$date = date_create($date);
			$date = date_format($date, get_option('date_format')." ".get_option('time_format'));
		}
		else 
			$date = "";
		return $date;
	}
	function get_time_field($order)
	{
		global $wp;
		
		if($wp->query_vars['pagename'] == 'kariah-manager' && isset($wp->query_vars['orders-details'])){
			$order = wc_get_order($wp->query_vars['orders-details'] );
		}
		if($wp->query_vars['pagename'] == 'kariah-manager' && isset($wp->query_vars['subscriptions-manage'])){
			$order = wc_get_order($wp->query_vars['subscriptions-manage'] );
		}
		
		return $order->get_meta('_billing_wcoa_time');
	}
	function has_to_be_automatically_cancelled($order, $cancel_if_needed = true)
	{
		global $wcoa_time_model, $wcoa_option_model;
		$options = $wcoa_option_model->get_options();
		$date = $this->get_approval_date($order, true);
		$offset = wcoa_get_value_if_set($options, array('approval_workflow','automatic_cancellation_time'), 0);
		$result = $offset != 0;
		if($offset > 0 && $cancel_if_needed && ($order->get_status() == 'approved' || $order->needs_payment()) && 
		   $this->is_approved($order) && $date && $wcoa_time_model->time_compare($date, $offset) == 1)
		{
			$order->set_status('cancelled', '' , true);
			$order->save();
		}
		return $result;
	}
	function can_be_cancelled($order)
	{
		global $wcoa_time_model, $wcoa_option_model;
		$date = $this->get_approval_date($order, true);
		$options = $wcoa_option_model->get_options();
		$offset = wcoa_get_value_if_set($options, array('approval_workflow','cancellation_time'), 0);
		
		return $order->get_status() != 'cancelled' && $this->is_approved($order) && $date && $offset > 0 && $wcoa_time_model->time_compare($date, $offset) != 1;
	}
	function get_order_details_page_url($order)
	{
		$order_url = $order->get_customer_id() ? $order->get_view_order_url(): 
												 $order->get_checkout_order_received_url( ); 
												 
		return $order_url;
	}
	 public function restore_order_stock( $order_id, $order, $operation = 'increase' ) //Type of opertion, allows 'set', 'increase' and 'decrease'.
	 {
		$items = $order->get_items();

		if ( /* ! get_option('woocommerce_manage_stock') == 'yes' &&  */! count( $items ) > 0 )
				return; // We exit 
		
		foreach ( $order->get_items() as $item ) 
		{
			$product_id = $item->get_product_id();

			if ( $product_id > 0 ) {
				$product = $item->get_product();

				if ( $product && $product->exists() && $product->managing_stock() ) 
				{

					// Get the product initial stock quantity (before update)
					$initial_stock = $product->get_stock_quantity();

					$item_qty = apply_filters( 'woocommerce_order_item_quantity', $item->get_quantity(), $this, $item );

					// Update the product stock quantity
					// Replace DEPRECATED methods: increase_stock() & discrease_stock()
					wc_update_product_stock( $product, $item_qty, $operation );

					// Get the product updated stock quantity
					$updated_stock = $initial_stock + $item_qty;

					//do_action( 'woocommerce_auto_stock_restored', $product, $item );

					// A unique Order note: Store each order note in an array…
					$order_note[] = sprintf( __( 'Product ID #%s stock incremented from %s to %s.', 'woocommerce' ), $product_id, $initial_stock, $updated_stock);

					// DEPRECATED & NO LONGER NEEDED - can be removed
					//$order->send_stock_notifications( $product, $updated_stock, $item_qty );

				}
			}
		}
		// Adding a unique composite order note (for multiple items)
		/*$order_notes = count($order_note) > 1 ? implode(' | ', $order_note) : $order_note[0];
		$order->add_order_note( $order_notes );*/
	}
	
	public function automatic_approval_according_products($order, $options)
	{
		global $wcoa_wpml_model;
		$workflow_type = wcoa_get_value_if_set($options, array('automatic_approval','by_product', 'workflow_type'), 'manual_approval'); // 'manual_approval' || 'automatic_approval'
		$selected_products = wcoa_get_value_if_set($options, array('automatic_approval','by_product', 'product'), array());
		$selected_categories = wcoa_get_value_if_set($options, array('automatic_approval','by_product', 'category'), array());
		$selected_tags = wcoa_get_value_if_set($options, array('automatic_approval','by_product', 'tag'), array());
		$consider_subcategories = wcoa_get_value_if_set($options, array('automatic_approval','by_product', 'consider_subcategories'), true);
		$items = $order->get_items();
		$is_present = false;
		$approve = false;
		
		if(empty($selected_products) && empty($selected_categories) && empty($selected_tags))
			return false;
		
		foreach ( $items as $item ) 
		{
			if($is_present)
				break;
			
			$product_name = $item->get_name();
			$product_id = $item->get_product_id();
			$product_variation_id = $item->get_variation_id();
			$product_item = wc_get_product($product_id);
			$category_ids = $product_item->get_category_ids();
			$tags_ids = $product_item->get_tag_ids();
			
			//translations
			$main_id = $wcoa_wpml_model->get_original_id($product_id);
			$main_variation_id = $product_variation_id != 0 ?  $wcoa_wpml_model->get_original_id($product_variation_id) : $product_variation_id;
			
			$main_category_ids = array();
			foreach($category_ids as $cat_id)
				$main_category_ids[] = $wcoa_wpml_model->get_main_language_id($cat_id, 'product_cat');
			
			$main_tags_ids = array();
			foreach($tags_ids as $tag_id)
				$main_tags_ids[] = $wcoa_wpml_model->get_main_language_id($tag_id, 'product_tag');			
				
			if($consider_subcategories && !empty($main_category_ids))
			{
				$sub_categories = array();
				foreach($selected_categories as $cat_id)
				{
					$args = array(
						'type'                     => 'product',
						'child_of'                 => $cat_id,
						'parent'                   => '',
						'orderby'                  => 'name',
						'order'                    => 'ASC',
						'hide_empty'               => 1,
						'hierarchical'             => 1,
						'exclude'                  => '',
						'include'                  => '',
						'number'                   => '',
						'taxonomy'                 => "product_cat",
						'pad_counts'               => false

					); 
					$result = get_categories( $args );
					
					foreach((array)$result as $sub_cat)
					{
						$sub_categories[] =  $wcoa_wpml_model->get_main_language_id($sub_cat->term_id, 'product_cat') ;
					}
				}
				
				$selected_categories = !empty($sub_categories) ? array_merge($selected_categories, $sub_categories) : $selected_categories;
			}
			
			$is_present = in_array($product_id, $selected_products) || ($product_variation_id != 0 && in_array($product_variation_id, $selected_products));
			if(!$is_present && !empty($selected_categories) && !empty($main_category_ids))
			{
				$result = array_intersect($main_category_ids, $selected_categories);
				$is_present = !empty($result);
			}
			if(!$is_present && !empty($selected_tags) && !empty($main_tags_ids))
			{
				$result = array_intersect($main_tags_ids, $selected_tags);
				$is_present = !empty($result);
			}
		}
		
		if($workflow_type == 'manual_approval')
			$approve = $is_present ? false : true;
		else if($workflow_type == 'automatic_approval')
			$approve = $is_present ? true : false;
		
		
		return $approve;
		
	}
}
?>