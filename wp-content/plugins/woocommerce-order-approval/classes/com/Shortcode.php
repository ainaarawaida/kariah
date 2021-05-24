<?php 
namespace WCOA\classes\com;

class Shortcode
{
	public function __construct()
	{
		
	}
	public function replace_shortcodes_with_order_data($message, $order)
	{
		global $wcoa_order_model,  $wcoa_time_model;
		$order = is_numeric($order) ? wc_get_order($order) : $order;
		if(!is_object($order))
			return $message;
		
		$billing_fields = array('order_id', 'order_date_created', 'billing_first_name', 'billing_last_name', 'billing_company', 'billing_address_1', 'billing_address_2', 'billing_city', 'billing_state', 'billing_postcode', 'billing_country', 'billing_email', 'billing_phone');
		$shipping_fields = array('shipping_first_name', 'shipping_last_name', 'shipping_company', 'shipping_address_1', 'shipping_address_2', 'shipping_city', 'shipping_state', 'shipping_postcode', 'shipping_country');
		$special_fields = array('order_page_url', 'payment_page_url', 'custom_approval_message');
		$payment_url = $order->get_checkout_payment_url(); //woocommerce_checkout_pay_endpoint
		/* $order_url = $order->get_customer_id() ? $order->get_view_order_url(): 
												 $order->get_checkout_order_received_url( );  *///woocommerce_myaccount_view_order_endpoint || woocommerce_checkout_order_received_endpoint
		$order_url =  $wcoa_order_model->get_order_details_page_url($order);
		
		//Custom order approval message 
		$specific_approval_data = $wcoa_order_model->get_approval_data($order->get_id(), true);
		$approval_date_time =  wcoa_get_value_if_set($specific_approval_data , 'datetime', "");
		$approval_message = $specific_approval_data['custom_message'] != "" ? $specific_approval_data['custom_message'] : "";
		$approval_message = str_replace('[datetime]', $wcoa_time_model->format_datetime($approval_date_time), $approval_message);
		
		foreach ( $billing_fields as $key) 
		{
			$key_method = str_replace("order_", "", $key);
			if ( is_callable( array( $order, "get_{$key_method}" ) )  ) 
			{
				$method_name = "get_".$key_method;
				$key_value = $key == 'order_id' ? apply_filters('wcam_get_visual_order_id', $order->get_order_number()) : $order->$method_name();
				$message = str_replace("[{$key}]", $key_value, $message );

			// Store custom fields prefixed with wither shipping_ or billing_. This is for backwards compatibility with 2.6.x.
			// TODO: Fix conditional to only include shipping/billing address fields in a smarter way without str(i)pos.
			} /* elseif ( ( 0 === stripos( $key, 'billing_' ) || 0 === stripos( $key, 'shipping_' ) )
				&& ! in_array( $key, array( 'shipping_method', 'shipping_total', 'shipping_tax' ) ) ) {
				$order->update_meta_data( '_' . $key, $value );
			} */
		}
	
		foreach ( $shipping_fields as $key ) 
		{
			if ( is_callable( array( $order, "set_{$key}" ) ) && is_callable( array( $order, "get_{$key}" ) ) ) 
			{
				$method_name = "get_".$key;
				$message = str_replace("[{$key}]", $order->$method_name(), $message );
			}
		}
		foreach ( $special_fields as $key ) 
		{
			switch($key)
			{
				case 'order_page_url': $message = str_replace("[{$key}]", $order_url, $message ); break;
				case 'payment_page_url': $message = str_replace("[{$key}]", $payment_url, $message ); break;
				case 'custom_approval_message': $message = str_replace("[{$key}]", $approval_message, $message ); break;
			}
		}
				
		return $message;
	}
}
?>