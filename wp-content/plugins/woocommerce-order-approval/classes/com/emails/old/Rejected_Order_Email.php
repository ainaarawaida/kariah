<?php 
namespace WCOA\classes\com\emails;

class Rejected_Order_Email extends \WC_Email
{
	 function __construct() 
	 {
        
       // set ID, this simply needs to be a unique name
        $this->id = 'wcoa_rejected_order';

        // this is the title in WooCommerce Email settings
        $this->title = esc_html__( 'Rejected order', 'woocommerce-order-approval' );

        // this is the description in WooCommerce email settings
        $this->description = esc_html__( 'Rejected order', 'woocommerce-order-approval' );

        // these are the default heading and subject lines that can be overridden using the settings
        $this->heading = 'Rejected Order';
        $this->subject = 'Rejected Order';
		
		$this->placeholders   = array(
				'{order_date}'   => '',
				'{order_number}' => '',
			);

        // these define the locations of the templates that this email should use, we'll just use the new order template since this email is similar
		$this->template_html  ='emails/customer-rejected-order.php';
		$this->template_plain = 'emails/plain/customer-rejected-order.php';
		$this->template_base  = WCOA_PLUGIN_ABS_PATH . '/templates/';
			
        // Trigger on new paid orders
		add_action( 'woocommerce_order_status_rejected_notification', array( $this, 'trigger' ), 10, 2 );

        // Call parent constructor to load any other defaults not explicity defined here
        parent::__construct();
	
		$this->customer_email = true;	
        
    }
    
   public function trigger( $order_id, $order = false ) 
   {
			$this->setup_locale();

			//if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
				$order = wc_get_order( $order_id );
			//}

			if (isset($order) && !is_bool($order) ) 
			{
				$this->object                         = $order;
				$this->recipient                      = $this->object->get_billing_email();
				$this->placeholders['{order_date}']   = wc_format_datetime( $this->object->get_date_created() );
				$this->placeholders['{order_number}'] = $this->object->get_order_number();
			}

			if ( $this->is_enabled() && $this->get_recipient() ) {
				$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
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
			return esc_html__( 'Your {site_title} order is now complete', 'woocommerce' );
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
	 public function get_content_html() {
        ob_start();
        wc_get_template_html(
				$this->template_html,
				array(
					'order'              => $this->object,
					'email_heading'      => $this->get_heading(),
					'additional_content' => $this->get_additional_content(),
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