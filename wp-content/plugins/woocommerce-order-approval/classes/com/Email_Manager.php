<?php 
namespace WCOA\classes\com;

class Email_Manager
{
	//rplc: woocommerce-order-approval, wcoa, WCOA
	var $allowed_statuses  = array('approved', 'rejected');
	
	public function __construct()
	{
		
	 //add_filter( 'woocommerce_email_classes', array( &$this, 'init_emails' ) );
	  
	  /* add_action( 'woocommerce_order_status_approved_notification', array( $this, 'trigger' ), 10, 2 );
	  add_action( 'woocommerce_order_status_rejected_notification', array( $this, 'trigger' ), 10, 2 ); */
		
		add_action( 'woocommerce_order_status_changed', array( &$this,'order_status_changed'), 10, 4 ); 
		
		add_action('woocommerce_email_before_order_table', array(&$this, 'embed_approval_links'), 10, 4); 
	}
	public function init_emails( $emails ) 
	{
	    if(!class_exists('WCOA\classes\com\emails\Approved_Order_Email'))
		{
			require_once('emails/Approved_Order_Email.php');
		}
		if(!class_exists('WCOA\classes\com\emails\Rejected_Order_Email'))
		{
			require_once('emails/Rejected_Order_Email.php');
		}
	
		new emails\Approved_Order_Email();
		new emails\Rejected_Order_Email();
	
	    return $emails;
	}
	
	public function order_status_changed( $this_get_id, $this_status_transition_from, $this_status_transition_to, $order )
	{
		if(in_array($this_status_transition_to, $this->allowed_statuses) )
		{
			$this->trigger($this_get_id, $order);
		}
	}
	
	public function trigger( $order_id, $order = false ) 
	{
		global  $wcoa_order_model, $wcoa_option_model, $wcoa_shortcode_model;
		
		$order = wc_get_order( $order_id );
		$order_status = $order->get_status();
		$mailer = WC()->mailer(); //WC_Emails
		$recipients = $order->get_billing_email();
		
		$texts = $wcoa_option_model->get_text_options();
		$settings = $wcoa_option_model->get_options();
		$lang = $wcoa_order_model->get_lang($order);
		$subject = $wcoa_shortcode_model->replace_shortcodes_with_order_data(wcoa_get_value_if_set($texts , array('email', $order_status, 'subject', $lang), ""),$order);
		$disable_notification = wcoa_get_value_if_set($settings, array('notification', 'disable_all'), false);
		
		$this->template_html  = "emails/customer-{$order_status}-order.php";
		//$this->template_plain = 'emails/plain/customer-rejected-order.php';
		$this->template_base  = WCOA_PLUGIN_ABS_PATH . '/templates/';
		
		if (!$disable_notification && isset($order) && !is_bool($order) && in_array($order_status, $this->allowed_statuses)) 
		{
			//disable notification for automatically approved orders
			$send_notification_to_automatically_approved_order = wcoa_get_value_if_set($settings, array('notification', 'send_to_automatically_approved_order'), false);
			$skip_email_notiication = $order->get_meta('wcoa_skip_email_notification');
			if(!$send_notification_to_automatically_approved_order && $wcoa_order_model->can_be_automatically_approved($order))
			{
				if($skip_email_notiication)
				{
					$order->update_meta_data('wcoa_skip_email_notification',  false);
					$order->save();
					return;
				}
			}
			
			$this->object                         = $order;
			$this->recipient                      = $this->object->get_billing_email();
		
			ob_start();
			//$mailer->email_header(get_bloginfo('name'));
			echo $this->get_content_html();
			//$mailer->email_footer();
			$message =  ob_get_contents();
			ob_end_clean();
			
			add_filter('wp_mail_from_name',array(&$this, 'wp_mail_from_name'), 99, 1);
			add_filter('wp_mail_from', array(&$this, 'wp_mail_from')/* , 99, 1 */);
			$attachments =  array();
			if(!$mailer->send( $recipients, $subject, $message, "Content-Type: text/html\r\n", $attachments)) //$mail->send || wp_mail
				wp_mail( $recipients, $subject, $message, "Content-Type: text/html\r\n", $attachments);
			remove_filter('wp_mail_from_name',array(&$this, 'wp_mail_from_name'));
			remove_filter('wp_mail_from',array(&$this, 'wp_mail_from'));
		
		}
		
	}
	public function wp_mail_from_name($name) 
	{
		return get_bloginfo('name');
	}
	public function wp_mail_from($content_type) 
	{
		$server_headers = function_exists('apache_request_headers') ? apache_request_headers() : wcoa_apache_request_headers();
		$domain = isset($server_headers['Host']) ? $server_headers['Host'] : null ;
		if(!isset($domain) && isset($_SERVER['HTTP_HOST']))
			$domain = str_replace("www.", "", $_SERVER['HTTP_HOST'] );
		
		return isset($domain) ? 'noprely@'.$domain : $content_type;
	}
	
	//To override and edit this email template copy woocommerce-order-approval/templates/emails/customer-approved-order.php 
	// 		to your theme folder: {theme_folder}/woocommerce/emails/customer-approved-order.php
	 public function get_content_html() 
	 {
		global $wcoa_option_model, $wcoa_order_model, $wcoa_shortcode_model;
		 
		$options = $wcoa_option_model->get_text_options();
		$order_status = $this->object->get_status();
		$lang = $wcoa_order_model->get_lang($this->object);
		
		//ob_start();
       return wc_get_template_html(
				$this->template_html,
				array(
					'order'              => $this->object,
					'email_heading'      => $wcoa_shortcode_model->replace_shortcodes_with_order_data(wcoa_get_value_if_set($options , array('email', $order_status,'heading', $lang), ""),$this->object), //head option
					'body_content' 		 => $wcoa_shortcode_model->replace_shortcodes_with_order_data(wcoa_get_value_if_set($options , array('email', $order_status,'body', $lang), ""),$this->object), //body option
					'sent_to_admin'      => false,
					'plain_text'         => false,
					'email'              => '',
				),
				 '',
				 $this->template_base
				
			);
       // return ob_get_clean();
    } 
	public function embed_approval_links($order, $sent_to_admin, $plain_text, $email = null)
	{
		if(!$sent_to_admin || !isset($email) || get_class($email) != 'WC_Email_New_Order')
			return; 
		
		global $wcoa_option_model,$wcoa_wpml_model;
		
		$options = $wcoa_option_model->get_text_options();
		$default_texts = $wcoa_option_model->get_default_texts();
		$lang = $wcoa_wpml_model->get_current_language();
		$general_options = $wcoa_option_model->get_options();
		$automatic_approval = wcoa_get_value_if_set($general_options, array('automatic_approval','payment_gateways', $order->get_payment_method()), false); 
		$content = wcoa_get_value_if_set($options , array('email', 'new_order_admin','text_to_embed',  $lang), $default_texts['text_to_embed_on_new_order_email_admin']);
		if($automatic_approval)
			return;
		
		$order_key = $order->get_order_key();
		/* $approval_link = get_edit_post_link($order->get_id())."&wcoa_action=approve&order_key=".$order_key;
		$rejection_link = get_edit_post_link($order->get_id())."&wcoa_action=reject&order_key=".$order_key; */
		$approval_link = get_admin_url()."post.php?post=".$order->get_id()."&action=edit&wcoa_action=approve&order_key=".$order_key;
		$rejection_link = get_admin_url()."post.php?post=".$order->get_id()."&action=edit&wcoa_action=reject&order_key=".$order_key;
		
		$content = str_replace('[approve_order_link]', $approval_link, $content);
		$content = str_replace('[reject_order_link]', $rejection_link, $content);
		
		echo $content;
	}
	
}
?>