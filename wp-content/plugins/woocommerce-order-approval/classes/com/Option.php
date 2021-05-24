<?php 
namespace WCOA\classes\com;

class Option
{
	//rplc: woocommerce-order-approval, wcoa, WCOA
	
	var $options_cache;
	var $text_cache;
	public function __construct()
	{
		
	}
	public function save_options($data)
	{
		$this->options_cache = null;
		update_option('wcoa_options', $data);
	}
	public function save_text_options($data) 
	{
		if(isset($data))
			$data = $this->escape_text($data);
		
		$this->text_cache = null;
		update_option('wcoa_text_options', $data);
	}
	private function escape_text($data)
	{
		foreach($data as $index => $content)
		{
			if(is_string($content))
				$data[$index] = stripcslashes($content);
			else if(is_array($content))
				$data[$index] = $this->escape_text($content);
		}
		return $data;
	}
	
	public function get_options($option_name = null, $default_value = null)
	{
		$result = null;
		
		$options = isset($this->options_cache ) ? $this->options_cache  : get_option('wcoa_options');
		$this->options_cache = $options;
		if($option_name != null)
		{
			$result = wcoa_get_value_if_set($options, $option_name ,$default_value);
		}
		else 
			$result = $options;
		
		return $result;
	}
	public function get_default_texts()
	{
		$texts = array();
		$texts['approved_subject'] = __('Order #[order_id] has been approved!', 'woocommerce-order-approval');
			/* $default_template = esc_html__('Hello [billing_first_name] [billing_first_name],<br>'.
							   'The order number [order_id] will be shipped to the following address: [formatted_shipping_address]<br><br>'.
							   '[tracking_message]<br><br>'.
							   '<a href="[order_url]" target="_blank">Click here</a> to monitor your order status.', 'woocommerce-order-approval'); */
		$texts['approved_heading'] = __('Order approved', 'woocommerce-order-approval');
		$texts['approved_message'] = __('Your order has been approved! Please go to the <a href="[payment_page_url]">payment page</a> to complete the payment!', 'woocommerce-order-approval');
			
		$texts['rejected_subject'] = __('Order #[order_id] has been rejected', 'woocommerce-order-approval');
		$texts['rejected_heading'] = __('Order rejected', 'woocommerce-order-approval');
		$texts['rejected_message'] = __('Your order has been rejected. For more details visit the <a href="[order_page_url]">order page</a>.', 'woocommerce-order-approval');
		$texts['text_to_embed_on_new_order_email_admin'] = __('You can approve or rejecte the order by directly click on the followin links <ol><li><a href="[approve_order_link]">Click here to approve</a></li><li><a href="[reject_order_link]">Click here to reject</a></li></ol>', 'woocommerce-order-approval');
			
		$texts['approved_message'] = __('Good news! Your order has been approved!', 'woocommerce-order-approval');
		$texts['rejected_message'] = __('Your order has been rejected.', 'woocommerce-order-approval');
		$texts['area_title']= __('Approval status', 'woocommerce-order-approval');
		$texts['timeline_step_1'] = __('Waiting for approval...', 'woocommerce-order-approval');
		$texts['timeline_step_2'] = __('The order has been approved', 'woocommerce-order-approval');
		$texts['timeline_step_3'] = __('The order has been rejected', 'woocommerce-order-approval');
			
		return $texts;
	}
	public function get_text_options($option_name = null, $default_value = null)
	{
		global  $wcoa_wpml_model;
		$result = null;
		
		//delete_option('wcoa_text_options');
		$options = isset($this->text_cache) ? $this->text_cache : $options = get_option('wcoa_text_options');
		$this->text_cache = $options;
		
		//default values 
		$is_first_run = $options == false || !isset($options) || empty($options);
		$options = $is_first_run ? array() : $options;
		if($is_first_run)
		{
			$langs =  $wcoa_wpml_model->get_langauges_list();
			$defaults = $this->get_default_texts();
					
			// -- Email message
			$approved_subject = $defaults['approved_subject'];
			$approved_heading = $defaults['approved_heading'];
			$approved_message = $defaults['approved_message'];
			
			$rejected_subject = $defaults['rejected_subject'];
			$rejected_heading = $defaults['rejected_heading'];
			$rejected_message = $defaults['rejected_message'];
			$text_to_embed_on_new_order_email_admin = $defaults['text_to_embed_on_new_order_email_admin'];
			
			$options['email'] = array('approved' => array('subject' => array(), 'heading'=> array(), 'body'=> array()),
									  'rejected' => array('subject' => array(), 'heading'=> array(), 'body'=> array()));
			foreach($langs as $lang_data)
			{					
				$options['email']['approved']['subject'][$lang_data['language_code']] = $approved_subject;
				$options['email']['approved']['heading'][$lang_data['language_code']] = $approved_heading;
				$options['email']['approved']['body'][$lang_data['language_code']] = $approved_message;
				
				$options['email']['rejected']['subject'][$lang_data['language_code']] = $rejected_subject;
				$options['email']['rejected']['heading'][$lang_data['language_code']] = $rejected_heading;
				$options['email']['rejected']['body'][$lang_data['language_code']] = $rejected_message;
				
				$options['email']['new_order_admin']['text_to_embed'][$lang_data['language_code']] = $text_to_embed_on_new_order_email_admin;
			}
			
			// -- Position
			//$options['position'] = 'woocommerce_email_before_order_table';
			
			// -- Approval page
			$approved_message = $defaults['approved_message'];
			$rejected_message = $defaults['rejected_message'];
			$area_title = $defaults['area_title'];
			$timeline_step_1 = $defaults['timeline_step_1'];
			$timeline_step_2 = $defaults['timeline_step_2'];
			$timeline_step_3 = $defaults['timeline_step_3'];
			
			foreach($langs as $lang_data)
			{					
				$options['approval_page']['area_title'][$lang_data['language_code']] = $area_title;
				$options['approval_page']['message']['approved'][$lang_data['language_code']] = $approved_message;
				$options['approval_page']['message']['rejected'][$lang_data['language_code']] = $rejected_message;
				$options['approval_page']['timeline']['approval-waiting'][$lang_data['language_code']] = $timeline_step_1;
				$options['approval_page']['timeline']['approved'][$lang_data['language_code']] = $timeline_step_2;
				$options['approval_page']['timeline']['rejected'][$lang_data['language_code']] = $timeline_step_3;
			}
			
		}
		//end default values
		
		//load values
		if($option_name != null)
		{
			$result = wcoa_get_value_if_set($options, $option_name ,$default_value);
		}
		else 
			$result = $options;
		
		return $result;
	} 
}
?>