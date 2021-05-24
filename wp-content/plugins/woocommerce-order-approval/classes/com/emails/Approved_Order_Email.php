<?php 
namespace WCOA\classes\com\emails;

class Approved_Order_Email extends \WC_Email
{
	 function __construct() 
	 {
        
       // set ID, this simply needs to be a unique name
        $this->id = 'wcoa_approved_order';

        // this is the title in WooCommerce Email settings
        $this->title = esc_html__( 'Approved order', 'woocommerce-order-approval' );

        // this is the description in WooCommerce email settings
        $this->description = esc_html__( 'Approved order', 'woocommerce-order-approval' );

        // these are the default heading and subject lines that can be overridden using the settings
        $this->heading = 'Approved Order';
        $this->subject = 'Approved Order';
		
		$this->placeholders   = array(
				'{order_date}'   => '',
				'{order_number}' => '',
			);

        // these define the locations of the templates that this email should use, we'll just use the new order template since this email is similar
		$this->template_html  ='emails/customer-approved-order.php';
		$this->template_plain = 'emails/plain/customer-approved-order.php';
		$this->template_base  = WCOA_PLUGIN_ABS_PATH . '/templates/';
			
        // Trigger on new paid orders
		//add_action( 'woocommerce_order_status_approved_notification', array( $this, 'trigger' ), 10, 2 );
		add_action( 'woocommerce_order_status_changed', array( &$this,'order_status_changed'), 10, 4 ); 
		
        // Call parent constructor to load any other defaults not explicity defined here
        parent::__construct();
	
		$this->customer_email = true;
		
		
    }
	public function wp_mail_from_name($name) 
	{
		/* global $wcsts_text_helper;
		$text = $wcsts_text_helper->get_email_sender_name(); */
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
    public function order_status_changed( $this_get_id, $this_status_transition_from, $this_status_transition_to, $order )
	{
		if($this_status_transition_to == 'approved')
		{
			/* $order->update_meta_data('wcoa_status_chage_date',  current_time( 'mysql' ));
			$order->save(); */
			$this->trigger($this_get_id, $order);
		}
	}
	public function trigger( $order_id, $order = false ) 
	{
		global  $wcoa_order_model, $wcoa_option_model;
		$this->setup_locale();

		//if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
			$order = wc_get_order( $order_id );
		//}

		//$allowed_statuses = array('approved', 'rejected');
		
		 $this->from_address = $this->wp_mail_from("");
		 $this->from_name  = $this->wp_mail_from_name("");
		 $this->email_type   = 'html';
		$order = wc_get_order( $order_id );
		$lang = $wcoa_order_model->get_lang($order);
		$order_status = $order->get_status();
		$mailer = WC()->mailer(); //WC_Emails
		$recipients = $order->get_billing_email();
		$options = $wcoa_option_model->get_text_options();
		$subject = wcoa_get_value_if_set($options , array('email', 'approved', 'subject', $lang), "");
		
		if (isset($order) && !is_bool($order) && ($order_status == 'approved') ) 
		{
			$this->object                         = $order;
			//$this->recipient                      = $this->object->get_billing_email();
			/* $this->placeholders['{order_date}']   = wc_format_datetime( $this->object->get_date_created() );
			$this->placeholders['{order_number}'] = $this->object->get_order_number(); */
		

			//if ( $this->is_enabled() && $this->get_recipient() ) 
			{
				$result = $this->send( $recipients, $subject, $this->get_content(), $this->get_headers(), array() );
			}
		}
		$this->restore_locale();

	}
	
	/**
		 * Get email subject.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_subject() {
			return esc_html__( 'Your {site_title} order is approved', 'woocommerce' );
		}

		/**
		 * Get email heading.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_heading() {
			return esc_html__( 'Thanks for shopping with us', 'woocommerce' );
		}
		
	/**
     * get_content_html function.
     *
     * @since 0.1
     * @return string
     */	 
	 public function get_content_html() 
	 {
		 global $wcoa_option_model, $wcoa_order_model;
		 
		$options = $wcoa_option_model->get_text_options();
		$order_status = $this->object->get_status();
		$lang = $wcoa_order_model->get_lang($this->object);
		
        ob_start();
        wc_get_template_html(
				$this->template_html,
				array(
					'order'              => $this->object,
					'email_heading'      => wcoa_get_value_if_set($options , array('email', $order_status,'heading', $lang), ""), //head option
					'additional_content' => wcoa_get_value_if_set($options , array('email', $order_status,'body', $lang), ""), //body option
					'sent_to_admin'      => false,
					'plain_text'         => false,
					'email'              => $this,
				),
				 '',
				 $this->template_base
				
			);
        return ob_get_clean();
    }

    /**
     * get_content_plain function.
     *
     * @since 0.1
     * @return string
     */
    public function get_content_plain() {
        ob_start();
        wc_get_template_html(
				$this->template_plain,
				array(
					'order'              => $this->object,
					'email_heading'      => $this->get_heading(),
					'additional_content' => $this->get_additional_content(),
					'sent_to_admin'      => false,
					'plain_text'         => true,
					'email'              => $this,
				),
				 '',
				 $this->template_base
			);
        return ob_get_clean();
    }
	
	  /**
     * Initialize Settings Form Fields
     *
     * @since 0.1
     */
    public function init_form_fields() 
	{
		$placeholder_text  = sprintf( esc_html__( 'Available placeholders: %s', 'woocommerce' ), '<code>' . esc_html( implode( '</code>, <code>', array_keys( $this->placeholders ) ) ) . '</code>' );
		
        $this->form_fields = array(
           'enabled'            => array(
				'title'   => esc_html__( 'Enable/Disable', 'woocommerce' ),
				'type'    => 'checkbox',
				'label'   => esc_html__( 'Enable this email notification', 'woocommerce' ),
				'default' => 'yes',
			),
			'subject'            => array(
				'title'       => esc_html__( 'Subject', 'woocommerce' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => $placeholder_text,
				'placeholder' => $this->get_default_subject(),
				'default'     => '',
			),
			'heading'            => array(
				'title'       => esc_html__( 'Email heading', 'woocommerce' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => $placeholder_text,
				'placeholder' => $this->get_default_heading(),
				'default'     => '',
			),
			'additional_content' => array(
				'title'       => esc_html__( 'Additional content', 'woocommerce' ),
				'description' => esc_html__( 'Text to appear below the main email content.', 'woocommerce' ) . ' ' . $placeholder_text,
				'css'         => 'width:400px; height: 75px;',
				'placeholder' => esc_html__( 'N/A', 'woocommerce' ),
				'type'        => 'textarea',
				'default'     => $this->get_default_additional_content(),
				'desc_tip'    => true,
			),
            'email_type'         => array(
				'title'       => esc_html__( 'Email type', 'woocommerce' ),
				'type'        => 'select',
				'description' => esc_html__( 'Choose which format of email to send.', 'woocommerce' ),
				'default'     => 'html',
				'class'       => 'email_type wc-enhanced-select',
				'options'     => $this->get_email_type_options(),
				'desc_tip'    => true,
			),
        );
    }
	
}
?>