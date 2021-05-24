<?php
/**

 * toyyibPay Payment Gateway Classs

 */

class toyyibpay extends WC_Payment_Gateway
{

	function __construct()
		
	{

		add_action( 'woocommerce_api_callback', 'check_toyyibpay_callback' );
		add_action('bill_inquiry', 'requery_toyyibpay', 0, 2);

		$this->id = "toyyibPay";
		$this->method_title = __("toyyibPay", 'toyyibPay');
		$this->method_description = __("Enable your customers to make payments securely via toyyibPay.", 'toyyibPay');
		$this->title = __("toyyibPay", 'toyyibPay');
		$this->has_fields = true;
		$this->init_form_fields();
		$this->init_settings();

		foreach ($this->settings as $setting_key => $value) {
			$this->$setting_key = $value;
		}


		if ($this->universal_channel == '0') {
			if ($this->display_logo =='mini'){
				$this->icon = plugins_url('assets/mini-fpx.png', __FILE__);
			} elseif ($this->display_logo =='horiz') {
				$this->icon = plugins_url('assets/hor-fpx.png', __FILE__);
			} else {
				$this->icon = plugins_url('assets/ver-fpx.png', __FILE__);
		    }
		} else {
    			if ($this->display_logo =='mini'){
    				$this->icon = plugins_url('assets/mini-all.png', __FILE__);
    			} elseif ($this->display_logo =='horiz') {
    				$this->icon = plugins_url('assets/hor-all.png', __FILE__);
    			} else {
    				$this->icon = plugins_url('assets/ver-all.png', __FILE__);
    		    }
	    }
		

		if (is_admin()) {
			add_action('woocommerce_update_options_payment_gateways_' . $this->id, array(
				$this,
				'process_admin_options'
			));
		}
	}


    private function define_constants() {
    $this->define( 'TP_PLUGIN_FILE',  __FILE__ );
    $this->define( 'TP_PLUGIN_URL', plugin_dir_url(TP_PLUGIN_FILE));
    $this->define( 'TP_PLUGIN_DIR',  dirname(TP_PLUGIN_FILE) );
    }
        
	# Build the administration fields for this specific Gateway
	public function init_form_fields()

	{

		$this->form_fields = array(
			'enabled'        => array(
				'title'   => __('Enable / Disable', 'toyyibPay'),
				'label'   => __('Enable this payment gateway', 'toyyibPay'),
				'type'    => 'checkbox',
				'default' => 'no',
			),

				'title'          => array(
				'title'    => __('Title', 'toyyibPay'),
				'type'     => 'text',
				'default'  => __('toyyibPay', 'toyyibPay'),
			),

			'description'    => array(
				'title'    => __('Description', 'toyyibPay'),
				'type'     => 'textarea',
				'default'  => __('Pay securely with toyyibPay.', 'toyyibPay'),
				'css'      => 'max-width:350px;',
			),

			'display_logo' => array(
      			'title' => __('Checkout Logo','toyyibPay'),
     				'default' => 'horiz',
      				'class' => 'wc-enhanced-select',
      				'type' => 'select',
      				'desc_tip' => false,
      				'options' => array(
        				'mini' => 'Minimal',
        				'horiz' => 'Horizontal',
        				'verti' => 'Vertical'),
    			),

			'secretkey_prod'      => array(
				'title'    => __('User SecretKey', 'toyyibPay'),
				'type'     => 'text',
				'desc_tip' => __('Required', 'toyyibPay'),
				'description' => __('Obtain your secret key from your toyyibPay dashboard.', 'toyyibPay'),
			),

			'universal_category_prod' => array(
				'title'    => __('Category Code', 'toyyibPay'),
				'type'     => 'text',
				'desc_tip' => __('Required', 'toyyibPay'),
				'description' => __('Create a category at your toyyibPay dashboard and fill in your category code here.', 'toyyibPay'),
			),
			'checkout' => array(
				'title' => __('Checkout Settings', 'toyyibPay'),
				'type' => 'title',
				'description' => '',
			),

			'universal_channel'        => array(
				'title'   => __('Payment Channel', 'toyyibPay'),
				'label'   => __('Payment Channel Options', 'toyyibPay'),
				'description' => 'Choose your preferred payment channel - FPX and/or credit cards.',
				'type'    => 'select',
				'options' => array(
					'0' => 'FPX only',
					'1' => 'Credit/Debit Card only',
					'2' => 'FPX and Credit/Debit Card'
				),
			),

			'universal_charge'        => array(
				'title'   => __('Transaction Charges', 'toyyibPay'),
				'label'   => __('Transaction Charges Options', 'toyyibPay'),
				'description' => __('Choose payer for transaction charges.', 'toyyibPay'),
				'type'    => 'select',
				'options' => array(
					'0' => 'Charge included in bill amount',
					'1' => 'Charge the FPX (online banking) charges to the customer',
					'2' => 'Charge the credit card charges to the customer',
					'3' => 'Charge both FPX and credit card charges to the customer'
				),
			),

			'content_email'    => array(
				'title'    => __('Extra e-mail content (Optional)', 'toyyibPay'),
				'type'     => 'textarea',
				'desc_tip' => __('', 'toyyibPay'),
				'description' => 'Content of additional e-mail to be sent to your customers (Optional - leave this blank if you are not sure what to write).',
				'default'  => __('', 'toyyibPay'),
				'css'      => 'max-width:350px;',
			),

			'split' => array(
				'title' => __('Split Payment', 'toyyibPay'),
				'type' => 'title',
				'description' => __('Enable this feature only if you wish to split the received payment amount from your customer to other toyyibPay account. Do not enable this if you are not sure or do not want to split the received amount.', 'toyyibPay'),
			),

			'enablesplit' => array(
				'title' => __('Enable/Disable ', 'toyyibPay'),
				'type' => 'checkbox',
				'label' => __('Enable Split Payment', 'toyyibPay'),
				'description' => 'By enabling Split Payment, The transaction amount will be splitted to another (1) toyyibPay account.',
				'default' => 'no',
			),

			'splitmethod' => array(
				'title'   => __('Split method', 'toyyibPay'),
				'label'   => __('Split Method Options', 'toyyibPay'),
				'description' => __('Choose to split by percentage or fix amount.', 'toyyibPay'),
				'type'    => 'select',
				'options' => array(
					'0' => 'Percentage',
					'1' => 'Fix amount'
				),
			),

			'splitusername'      => array(
				'title'    => __('Receiver Username', 'toyyibPay'),
				'description' => __('Username of the toyyibPay account (1 username only - not your account username).', 'toyyibPay'),
				'type'     => 'text',
			),

			'splitpercent' => array(
				'title'    => __('Split Percentage (%)', 'toyyibPay'),
				'description' => __('Enter the percentage to split (Numbers only between 1 to 90).', 'toyyibPay'),
				'type'     => 'number',
			),

			'splitfixamount' => array(
				'title'    => __('Split Fix Amount', 'toyyibPay'),
				'description' => __('Enter the fix amount to split (Numbers only, split will occur if this amount is less than the total checkout amount by customers).', 'toyyibPay'),
				'type'     => 'number',
			),

			'develop' => array(
				'title' => __('Development Mode', 'toyyibPay'),
				'type' => 'title',
				'description' => __('This is for testing purposes. Please create an account in <a href="https://dev.toyyibpay.com">dev.toyyibpay.com</a> if you does not have one.<br>Use these banks only for testing in sandbox<br><b>SBI Bank A for success payments.</b><br><b>SBI Bank B for fail payments.</b><br><b>SBI Bank C for random possibilities.</b><br>(Username: 1234, Password: 1234)', 'toyyibPay'),
			),

			'enabledev' => array(
				'title' => __('Enable/Disable ', 'toyyibPay'),
				'type' => 'checkbox',
				'label' => __('Enable Development Mode', 'toyyibPay'),
				'description' => 'By enabling development mode, you will redirect to dev.toyyibpay.com instead of toyyibpay.com.',
				'default' => 'no',
			),

			'secretkey_dev'      => array(
				'title'    => __('User Secret Key Dev', 'toyyibPay'),
				'description' => __('Fill in your development secret key here.', 'toyyibPay'),
				'type'     => 'text',
				'desc_tip' => __('Obtain your secret key from your development acccount.', 'toyyibPay'),
			),

			'universal_category_dev' => array(
				'title'    => __('Category Code', 'toyyibPay'),
				'description' => __('Fill in your development category code here.', 'toyyibPay'),
				'type'     => 'text',
				'desc_tip' => __('Obtain your category code from your development acccount.', 'toyyibPay'),
			),

			'splitusername_dev'      => array(
				'title'    => __('Split Receiver Username', 'toyyibPay'),
				'description' => __('Username of the toyyibPay sandbox account (1 username only - not your account username).', 'toyyibPay'),
				'type'     => 'text',
			)
		);
	}
	
	public function cron_requery($billCode, $OrderId){
		
		$order 			= wc_get_order($OrderId);
		$old_wc 		= version_compare(WC_VERSION, '3.0', '<');
		$order_id 		= $old_wc ? $order->id : $order->get_id();
		
		if ($this->enabledev == "no") {

			$requery 			= 'https://toyyibpay.com/index.php/api/getBillTransactions';

		} else {

			$requery 			= 'https://dev.toyyibpay.com/index.php/api/getBillTransactions';

		}
		
		$post_check = array(
			'body' => array(
				'billCode' 			=> $billCode,
				'billpaymentStatus' => '1'
			)
		);
		
		$request 	= wp_remote_post($requery, $post_check);
		$response 	= wp_remote_retrieve_body($request);
		$arr 		= json_decode($response, true);
		
		if($order->get_status() == "pending" && $arr[0]["billpaymentStatus"] == "1"){
			$order->payment_complete();
			$order->add_order_note('Payment successfully made via toyyibPay :)<br> 
			Ref. No: '. $arr[0]["billpaymentInvoiceNo"].'
			<br>Bill Code: '. $billCode .'
			<br>Order ID: '. $OrderId);
			
			return;
		}else{
			return;
		}
	}

	# Submit payment
	public function process_payment($order_id)

	{
		global $woocommerce, $wp_query;
		# Get this order's information so that we know who to charge and how much
		$customer_order = wc_get_order($order_id);

		# Prepare the data to send to toyyibPay

		$billName = "Order No " . $order_id;
		$description = "Payment for Order No " .  $order_id;
		$payChannel = $this->universal_channel;
		$extraEmail = $this->content_email;
		$callbackURL = add_query_arg(array('wc-api' => 'toyyibpay', 'order' => $order_data['id']), home_url('/'));

		if ($this->universal_charge == "0") {
			$billTransactionCharge = '';
		} else if ($this->universal_charge == "1") {
			$billTransactionCharge = '0';
		} else if ($this->universal_charge == "2") {
			$billTransactionCharge = '1';
		} else {
			$billTransactionCharge = '2';
		}

		$order_id = $customer_order->get_id();
		$amount   = $customer_order->get_total();
		$name     = $customer_order->get_billing_first_name() . ' ' . $customer_order->get_billing_last_name();
		$email    = $customer_order->get_billing_email();
		$phone    = $customer_order->get_billing_phone();
		$returnURL = wc_get_endpoint_url( 'order-received', '', wc_get_checkout_url() );

		# Create bill API from toyyibpay

		
		if(isset($wp_query->query['order-pay'])){
			$order = new WC_Order($wp_query->query['order-pay']);
			$items = $order->get_items( 'line_item' );
							if( !empty( $items ) ) {
								foreach( $items as $order_item_id => $item ) {
									$line_item = new WC_Order_Item_Product( $item );
									$product  = $line_item->get_product();
									$product_id = $line_item->get_product_id();
									$vendor_id  = wcfm_get_vendor_id_by_post( $product_id );
								}
							}
			$vendorinfo = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
			$this->secretkey_prod = $vendorinfo['payment']['toyyibPay']['_secretkey_prod'] ;
			$this->universal_category_prod = $vendorinfo['payment']['toyyibPay']['_universal_category_prod'] ;
		}

		if ($this->enabledev == "no") {

			$secretkey = $this->secretkey_prod;
			$categorycode = $this->universal_category_prod;
			$url = 'https://toyyibpay.com/index.php/api/createBill';
			$redirect = "https://toyyibpay.com/";

		} else {

			$secretkey = $this->secretkey_dev;
			$categorycode = $this->universal_category_dev;
			$url = 'https://dev.toyyibpay.com/index.php/api/createBill';
			$redirect = "https://dev.toyyibpay.com/";

		}

		if ($this->enablesplit == "no") {
			$enableSplit = '0';
		} else {
			$enableSplit = '1';
		}

		if ($enableSplit == '1') {

			if ($this->enabledev == "no") {
				$splitusername = $this->splitusername;	
			} else {
				$splitusername = $this->splitusername_dev;
			}

			if ($this->splitmethod == 0 || $this->splitmethod == '0') {
				$splitAmount = ($this->splitpercent/100)*$amount;
			} else {
				$splitAmount = $this->splitfixamount;
			}

			$splitArgs = '[{"id":"' . $splitusername .'","amount":"' . $splitAmount*100 .'"}]';

		} else {
			$splitArgs = '';
		}

		if($name == NULL || $phone == NULL || $email == NULL){
			wc_add_notice( 'Error! Please complete your details (Name, phone, and e-mail are compulsory).', 'error' );
			return;
		}

		$post_args = array(
			'body' => array(
				'userSecretKey' 			=> $secretkey,
				'categoryCode' 				=> $categorycode,
				'billName' 					=> $billName,
				'billDescription' 			=> $description,
				'billPriceSetting'			=>	1,
				'billPayorInfo'				=>	1,
				'billAmount'				=>	$amount*100,
				'billReturnUrl'				=>	$returnURL,
				'billCallbackUrl'			=>	$callbackURL,
				'billExternalReferenceNo' 	=>	$order_id,
				'billTo'					=>	$name,
				'billEmail'					=>	$email,
				'billPhone'					=>	$phone,
				'billSplitPayment'			=>	$enableSplit,
				'billSplitPaymentArgs'		=>	$splitArgs,
				'billPaymentChannel'		=>	$payChannel,
				'billDisplayMerchant'		=>	1,
				'billContentEmail'			=>	$extraEmail,
				'billChargeToCustomer'		=>	$billTransactionCharge,
				'billASPCode'				=>  'toyyibPay-V1-WCV1.3.0'
			)
		);

		$request 	= wp_remote_post($url, $post_args);
		$response 	= wp_remote_retrieve_body($request);
		$arr 		= json_decode($response, true);
		$billCode 	= $arr[0]['BillCode'];

		$order_note = wc_get_order($order_id);
		
		if ($billCode == NULL) {

			$arr = [json_decode($response, true)];
			$msg = $arr[0]['msg'];

			if ($msg == NULL) {
				wc_add_notice('Error!<br>Please check the following : ' . $response, 'error');
			} else {
				wc_add_notice('Error!<br>Please check the following : ' . $msg, 'error');
			}
			
			return;
		} else 
		{
			$arguments = array($billCode, $order_id);
			date_default_timezone_set("Asia/Kuala_Lumpur");
			
			wp_schedule_single_event( strtotime("+ 3 minutes"), 'bill_inquiry', $arguments);

			$order_note->add_order_note('Customer made a payment attempt via toyyibPay.<br>Bill Code : ' . $billCode . '<br>You can check the payment status of this bill in toyyibPay account.');

			return array(
				'result'   => 'success',
				'redirect' => $redirect . $billCode
			);
		
		}
	}
	

	public function check_toyyibpay_response()
	{

		if (isset($_REQUEST['status_id']) && isset($_REQUEST['billcode']) && isset($_REQUEST['order_id']) && isset($_REQUEST['msg']) && isset($_REQUEST['transaction_id']))
		
		{
			global $woocommerce;

			$is_callback = isset($_POST['order_id']) ? true : false;
			$order = wc_get_order($_REQUEST['order_id']);
			$old_wc = version_compare(WC_VERSION, '3.0', '<');
			$order_id = $old_wc ? $order->id : $order->get_id();

			if ($order && $order_id != 0) {

					if ($_REQUEST['status_id'] == 1 || $_REQUEST['status_id'] == '1') {

						if (strtolower($order->get_status()) == 'pending' || strtolower($order->get_status()) == 'processing') {
							# only update if order is pending
							if (strtolower($order->get_status()) == 'pending') {
								$order->add_order_note('Payment is successfully made through toyyibPay!<br> 
								Ref. No: '. $_REQUEST['transaction_id'].'
								<br>Bill Code: '. $_REQUEST['billcode'] .'
                                <br>Order ID: '. $order_id);
                                $order->payment_complete();
							}

							if ($is_callback) {
								echo 'OK';
							} else {
								wp_redirect($order->get_checkout_order_received_url());

							}
							exit();
						}

					} elseif ($_REQUEST['status_id'] == 3 || $_REQUEST['status_id'] == '3') {
                        if (strtolower($order->get_status()) == 'pending' || strtolower($order->get_status()) == 'processing') {
							# only update if order is pending
							if (strtolower($order->get_status()) == 'pending') {
								$order->add_order_note('Payment attempt was failed.<br> 
								Ref. No: '. $_REQUEST['transaction_id'].'
								<br>Bill Code: '. $_REQUEST['billcode'] .'
								<br>Order ID: '. $order_id .'
								<br>Reason: '. $_REQUEST['reason']);
							}

							if ($is_callback) {
								echo 'OK';
							} else {
								wp_redirect(wc_get_checkout_url());
								wc_add_notice('Payment was declined<br>Reason: Bank error / insuficient fund', 'error');
							}
							exit();
						}
					} else {
						if (strtolower($order->get_status()) == 'pending' || strtolower($order->get_status()) == 'processing') {
							if ($is_callback) {
								echo 'OK';
							} else {
								wp_redirect(wc_get_checkout_url());
								wc_add_notice('Payment was declined<br>Reason: Payment is pending, please contact site admin to get your payment status', 'error');

							}
							exit();
						}
					}
			}

			if ($is_callback) {
				echo 'OK';
				exit();
			}
		}
	}


	# Validate fields, do nothing for the moment
	public function validate_fields()

	{
		return true;

	}

	# Check if we are forcing SSL on checkout pages, Custom function not required by the Gateway for now
	public function do_ssl_check()
	{
		if ($this->enabled == "yes") {
			if (get_option('woocommerce_force_ssl_checkout') == "no") {
				echo "<div class=\"error\"><p>" . sprintf(__("<strong>%s</strong> is enabled and WooCommerce is not forcing the SSL certificate on your checkout page. Please ensure that you have a valid SSL certificate and that you are <a href=\"%s\">forcing the checkout pages to be secured.</a>"), $this->method_title, admin_url('admin.php?page=wc-settings&tab=checkout')) . "</p></div>";
			}

		}

	}


	/**

	 * Check if this gateway is enabled and available in the user's country.
	 * Note: Not used for the time being
	 * @return bool
	 */

	public function is_valid_for_use()
	{
		return in_array(get_woocommerce_currency(), array('MYR'));
	}

	public function check_toyyibpay_callback()
	{
		if (isset($_REQUEST['status']) && isset($_POST['billcode']) && isset($_REQUEST['order_id']) && isset($_REQUEST['reason']) && isset($_REQUEST['refno'])) {

			global $woocommerce;
			$is_callback = isset($_POST['order_id']) ? true : false;
			$order = wc_get_order($_REQUEST['order_id']);
			$old_wc = version_compare(WC_VERSION, '3.0', '<');
			$order_id = $old_wc ? $order->id : $order->get_id();
			if ($order && $order_id != 0) {

					if ($_REQUEST['status'] == 1 || $_REQUEST['status'] == '1') {
						if (strtolower($order->get_status()) == 'pending' || strtolower($order->get_status()) == 'processing') {
							# only update if order is pending
							if (strtolower($order->get_status()) == 'pending') {
								$order->add_order_note('Payment is successfully made through toyyibPay!<br> 
								Ref. No: '. $_REQUEST['refno'].'
								<br>Bill Code: '. $_REQUEST['billcode'] .'
                                <br>Order ID: '. $order_id);
                                $order->payment_complete();

							}

							if ($is_callback) {
								echo 'OK';
							} else {
								wp_redirect($order->get_checkout_order_received_url());

							}
							exit();
						}

					} elseif ($_REQUEST['status'] == 3 || $_REQUEST['status'] == '3') {
                        if (strtolower($order->get_status()) == 'pending' || strtolower($order->get_status()) == 'processing') {
							# only update if order is pending
							if (strtolower($order->get_status()) == 'pending') {
								$order->add_order_note('Payment attempt was failed.<br> 
								Ref. No: '. $_REQUEST['transaction_id'].'
								<br>Bill Code: '. $_REQUEST['billcode'] .'
								<br>Order ID: '. $order_id .'
								<br>Reason: '. $_REQUEST['reason']);
							}
						}
                    } else {
                        if (strtolower($order->get_status()) == 'pending') {
							if (!$is_callback) {
								if ($this->enabledev == "no") {
									$urlCheck = 'https://toyyibpay.com/index.php/api/getBillTransactions';
								} else {
									$urlCheck = 'https://dev.toyyibpay.com/index.php/api/getBillTransactions';
								}
								$post_check = array(
									'body' => array(
										'billCode' 			=> $_REQUEST['billcode'],
										'billpaymentStatus' => '1'
									)
                                );
                                
								$requestCheck = wp_remote_post($urlCheck, $post_check);
								$responseCheck = wp_remote_retrieve_body($requestCheck);
								$arrCheck = json_decode($responseCheck, true);
								$billpaymentStatus = $arrCheck[0]['billpaymentStatus'];

								if ($billpaymentStatus == 1 || $billpaymentStatus == "1") {
									$order->payment_complete();
									$order->add_order_note('Payment successfully made through toyyibPay!<br> 
									Ref. No: '. $_REQUEST['transaction_id'].'
									<br>Bill Code: '. $_REQUEST['billcode'] .'
									<br>Order ID: '. $order_id);

									if ($is_callback) {
										echo 'OK';
									} else {
										wp_redirect($order->get_checkout_order_received_url());
									}
									exit();
									
								} elseif ($billpaymentStatus == 3 || $billpaymentStatus == "3") {
									if (strtolower($order->get_status()) == 'pending' || strtolower($order->get_status()) == 'processing') {
                                        # only update if order is pending
                                        if (strtolower($order->get_status()) == 'pending') {
                                            $order->add_order_note('Payment attempt was failed.<br> 
                                            Ref. No: '. $_REQUEST['transaction_id'].'
                                            <br>Bill Code: '. $_REQUEST['billcode'] .'
                                            <br>Order ID: '. $order_id .'
                                            <br>Reason: '. $_REQUEST['reason']);
                                        }
            
                                        if ($is_callback) {
                                            echo 'OK';
                                        } else {
                                            wp_redirect(wc_get_checkout_url());
                                            wc_add_notice('Payment was declined<br>Reason: '. $_REQUEST['reason'], 'error');
            
                                        }
                                        exit();
                                    }
								} else {
                                    if (strtolower($order->get_status()) == 'pending' || strtolower($order->get_status()) == 'processing') {
                                        # only update if order is pending
                                        if (strtolower($order->get_status()) == 'pending') {
                                            $order->add_order_note('Payment status pending. Please check in your toyyibPay account for the latest status.<br> 
                                            Ref. No: '. $_REQUEST['transaction_id'].'
                                            <br>Bill Code: '. $_REQUEST['billcode'] .'
                                            <br>Order ID: '. $order_id .'
                                            <br>Reason: '. $_REQUEST['reason']);
                                        }
            
                                        if ($is_callback) {
                                            echo 'OK';
                                        } else {
                                            wp_redirect(wc_get_checkout_url());
                                            wc_add_notice('Payment status is pending<br>Reason: '. $_REQUEST['reason'] . '<br>Please contact site admin to confirm your payment status.', 'error');
            
                                        }
                                        exit();
                                    }
							    }
							}
						}
                    }
			}


			if ($is_callback) {
				echo 'OK';
				exit();
			}
		}
	}
	
}
?>