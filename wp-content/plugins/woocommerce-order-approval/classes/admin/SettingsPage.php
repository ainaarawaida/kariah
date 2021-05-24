<?php 
namespace WCOA\classes\admin;

class SettingsPage
{
	public function __construct()
	{
		
	}
	
	//rplc: woocommerce-order-approval, wcoa, WCOA
	public function render_page()
	{
		
		global $wcoa_option_model, $wcoa_product_model, $wcoa_user_model;
		
		//Assets
		wp_enqueue_style( 'wcoa-admin-common', WCOA_PLUGIN_PATH.'/css/admin-common.css');
		wp_enqueue_style( 'wcoa-admin-settings-page', WCOA_PLUGIN_PATH.'/css/admin-settings-page.css');
		wp_enqueue_style( 'wcoa-timepicker-core', WCOA_PLUGIN_PATH.'/css/vendor/datepicker/classic.css');
		wp_enqueue_style( 'wcoa-timepicker', WCOA_PLUGIN_PATH.'/css/vendor/datepicker/classic.time.css');
		wp_enqueue_style( 'select2', WCOA_PLUGIN_PATH.'/css/vendor/select2/select2.min.css');
		
		wp_enqueue_script( 'select2', WCOA_PLUGIN_PATH.'/js/vendor/select2/select2.min.js', array('jquery'));
		wp_enqueue_script( 'wcoa-datepicker-core', WCOA_PLUGIN_PATH.'/js/vendor/datepicker/picker.js', array('jquery'));
		wp_enqueue_script( 'wcoa-datepicker-time', WCOA_PLUGIN_PATH.'/js/vendor/datepicker/picker.time.js', array('jquery'));
		wp_enqueue_script( 'wcoa-admin-settings-page', WCOA_PLUGIN_PATH.'/js/admin-settings-page.js', array('jquery'));
		wp_enqueue_script( 'wcoa-admin-product-selector', WCOA_PLUGIN_PATH.'/js/admin-product-category-list-loader.js', array('jquery'));
		
		//Save
		if(isset($_POST['wcoa_options']))
		{
			$wcoa_option_model->save_options($_POST['wcoa_options']);
		}
		
		//Load
		$options = $wcoa_option_model->get_options();
		$allowed_tags = array('br' => array(), 'p' => array(), 'strong' => array());
		?>
		<?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
			<div class="notice notice-success is-dismissible">
				 <p><?php esc_html_e('Saved successfully!', 'woocommerce-order-approval'); ?></p>
			</div>
		<?php endif; ?>
		<div class="wrap white-box">
			<!-- <form action="options.php" method="post" > -->
				<form action="" method="post" >
				<?php //settings_fields('wcoa_options_group'); ?> 
					<h2 class="wcoa_section_title wcoa_no_margin_top"><?php esc_html_e('Options', 'woocommerce-order-approval');?></h2>
					
									
					<h3><?php esc_html_e('Time selector', 'woocommerce-order-approval');?></h3>
					<div class="wcoa_option_group">
						<label><?php esc_html_e("Display a time selector on checkout form", 'woocommerce-order-approval');?></label>
						<?php  $selected = wcoa_get_value_if_set($options, array('time_selector', 'display'), false) ? " checked='checked' " : " "; ?>
						<label class="switch">
						  <input type="checkbox" class="wcoa_toggle master_option" name="wcoa_options[time_selector][display]" data-related-id="time" value="true" <?php esc_html_e($selected); ?>>
						  <span class="slider"></span>
						</label>						
					</div>
					<div class="master_related_time wcoa_master_related">
						<div class="wcoa_option_group">
							<?php  $selected = wcoa_get_value_if_set($options, array('time_selector','as_soon_as_possible_option'), false) ? " checked='checked' " : " "; ?>
							<label><?php esc_html_e('Include "as soon as possible" option', 'woocommerce-order-approval');?></label>
							<label class="switch">
							  <input type="checkbox" class="wcoa_toggle" name="wcoa_options[time_selector][as_soon_as_possible_option]" value="true" <?php esc_html_e($selected); ?>>
							  <span class="slider"></span>
							</label>
						</div>
						<div class="wcoa_option_group">
							<?php  $selected = wcoa_get_value_if_set($options, array('time_selector','mandatory'), false) ? " checked='checked' " : " "; ?>
							<label><?php esc_html_e('Mandatory', 'woocommerce-order-approval');?></label>
							<label class="switch">
							  <input type="checkbox" class="wcoa_toggle" name="wcoa_options[time_selector][mandatory]" value="true" <?php esc_html_e($selected); ?>>
							  <span class="slider"></span>
							</label>
						</div>
						<div class="wcoa_option_group">
							<?php  $selected = wcoa_get_value_if_set($options, array('time_selector','time_format'), 'H:i'); ?>
							<label><?php esc_html_e('Time format', 'woocommerce-order-approval');?></label>
							<select name="wcoa_options[time_selector][time_format]" >	
								<option value="H:i" <?php  selected($selected, 'H:i'); ?>><?php esc_html_e('24-hour format', 'woocommerce-order-approval');?></option>
								<option value="h:i a" <?php  selected($selected, 'h:i a'); ?>><?php esc_html_e('12-hour format', 'woocommerce-order-approval');?></option>
							</select>
						</div>
						<h4><?php esc_html_e("Minimum and Maximum time", 'woocommerce-order-approval');?></h4>
						<div class="wcoa_option_group">
							<label class="wcoa_input_label"><?php esc_html_e('Min selectable time', 'woocommerce-order-approval');?></label>
							<?php  $value = wcoa_get_value_if_set($options, array('time_selector','minimum_time'), ""); ?>
							<div class="wcoa_checkbox_container">
								<input type="number" step="1" min="0" class="timepicker" name="wcoa_options[time_selector][minimum_time]"  value="<?php esc_attr_e($value); ?>"></input>
							</div>							
						</div>
						<div class="wcoa_option_group">
							<label class="wcoa_input_label"><?php esc_html_e('Max selectable time', 'woocommerce-order-approval');?></label>
							<?php  $value = wcoa_get_value_if_set($options, array('time_selector','maximum_time'), ""); ?>
							<div class="wcoa_checkbox_container">
								<input type="number" step="1" min="0" class="timepicker" name="wcoa_options[time_selector][maximum_time]"  value="<?php esc_attr_e($value); ?>"></input>
							</div>							
						</div>
						<div class="wcoa_option_group">
							<label class="wcoa_input_label"><?php esc_html_e('Minimum selectable time offset (minutes)', 'woocommerce-order-approval');?></label>
							<?php  $value = wcoa_get_value_if_set($options, array('time_selector','minimum_time_offset'), 0); ?>
							<div class="wcoa_checkbox_container">
								<input class="wcoa_input_number" type="number" step="1" min="0" name="wcoa_options[time_selector][minimum_time_offset]"  value="<?php esc_attr_e($value); ?>"></input>
							</div>							
						</div>
						<div class="wcoa_option_group">
							<?php  $selected = wcoa_get_value_if_set($options, array('time_selector','minimum_time_is_now'), false) ? " checked='checked' " : " "; ?>
							<label><?php esc_html_e('Minimum selectable time cannot be lesser than "now"', 'woocommerce-order-approval');?></label>
							<label class="switch">
							  <input type="checkbox" class="wcoa_toggle" name="wcoa_options[time_selector][minimum_time_is_now]" value="true" <?php esc_html_e($selected); ?>>
							  <span class="slider"></span>
							</label>
						</div>
						<div class="wcoa_info">
							<h3><?php esc_html_e('Info', 'woocommerce-order-approval');?></h3> 
							<p><?php echo sprintf( wp_kses(__("<strong>Minimum selectable time cannot be lesser than 'now'</strong> option is enabled, the minimum time is computed as current time plus the minimum time offset. For example, if current time is <strong>%s</strong> and the minimum time offset is set as <strong>30</strong> minutes the minimum selectable time will be <strong>%s</strong>.", 'woocommerce-order-approval'), 'strong'), date('H:i'), date('H:i', strtotime("+30 minutes")));?></p> 
						</div>
					</div>
					
					<h3><?php esc_html_e('Email notification', 'woocommerce-order-approval');?></h3>
					<div class="wcoa_option_group">
						<label><?php esc_html_e("Disable all email notifications", 'woocommerce-order-approval');?></label>
						<?php  $selected = wcoa_get_value_if_set($options, array('notification', 'disable_all'), false) ? " checked='checked' " : " "; ?>
						<label class="switch">
						  <input type="checkbox" class="wcoa_toggle" name="wcoa_options[notification][disable_all]" value="true" <?php esc_html_e($selected); ?>>
						  <span class="slider"></span>
						</label>						
					</div>	
					<div class="wcoa_option_group">
						<label><?php esc_html_e("Send the approved notification for automatic approved orders after the checkout has been completed", 'woocommerce-order-approval');?></label>
						<?php  $selected = wcoa_get_value_if_set($options, array('notification', 'send_to_automatically_approved_order'), false) ? " checked='checked' " : " "; ?>
						<label class="switch">
						  <input type="checkbox" class="wcoa_toggle" name="wcoa_options[notification][send_to_automatically_approved_order]" value="true" <?php esc_html_e($selected); ?>>
						  <span class="slider"></span>
						</label>						
					</div>	
					
						
					<h3><?php esc_html_e('Approval workflow', 'woocommerce-order-approval');?></h3>
					<div class="wcoa_option_group">
						<label class="wcoa_input_label"><?php esc_html_e('Type', 'woocommerce-order-approval');?></label>
						<p><?php esc_html_e("Select the approval workflow type", 'woocommerce-order-approval');?></p>
						<?php $workflow_type = wcoa_get_value_if_set($options, array('approval_workflow','type'), 'pay_and_wait'); ?>
						<select name="wcoa_options[approval_workflow][type]" >	
							<option value="pay_and_wait" <?php  selected($workflow_type, 'pay_and_wait'); ?>><?php esc_html_e('Pay and wait for the approval', 'woocommerce-order-approval');?></option>
							<option value="wait_and_pay" <?php  selected($workflow_type, 'wait_and_pay'); ?>><?php esc_html_e('Wait for the approval and pay', 'woocommerce-order-approval');?></option>
						</select>
					</div>
					
					<h4><?php esc_html_e('Order cancellation', 'woocommerce-order-approval');?></h4>
					<div class="wcoa_option_group wcoa_half">
						<label class="wcoa_input_label"><?php esc_html_e('Customer - Order cancellation time (minutes) - Customer:', 'woocommerce-order-approval');?></label>
						<p><?php esc_html_e("The customer can optionally cancel the order in X minutes after it has been approved, where X can be specified using this option.", 'woocommerce-order-approval');?> <strong><?php esc_html_e('Leave 0 to disable this option.', 'woocommerce-order-approval');?></strong></p>
						<?php  $value = wcoa_get_value_if_set($options, array('approval_workflow','cancellation_time'), 0); ?>
						<div class="wcoa_checkbox_container">
							<input class="wcoa_input_number" type="number" step="1" min="0" name="wcoa_options[approval_workflow][cancellation_time]"  value="<?php esc_attr_e($value); ?>" required="required"></input>
						</div>							
					</div>
					
					<div class="wcoa_option_group wcoa_half">
						<label class="wcoa_input_label"><?php esc_html_e('Automatic - Order cancellation time (minutes) if not paid:', 'woocommerce-order-approval');?></label>
						<p><?php esc_html_e("The system can optionally cancel the order in X minutes (use this option to define the X) after it has been approved and if it has still not been paid. The check is performed when the user accesses the order details page.", 'woocommerce-order-approval');?> <strong><?php esc_html_e('Leave 0 to disable this option.', 'woocommerce-order-approval');?></strong></p>
						<?php  $value = wcoa_get_value_if_set($options, array('approval_workflow','automatic_cancellation_time'), 0); ?>
						<div class="wcoa_checkbox_container">
							<input class="wcoa_input_number" type="number" step="1" min="0" name="wcoa_options[approval_workflow][automatic_cancellation_time]"  value="<?php esc_attr_e($value); ?>" required="required"></input>
						</div>							
					</div>
					<!--<h4><?php esc_html_e('Automatic rejection', 'woocommerce-order-approval');?></h4>
					<p><?php echo wp_kses(__("If the shop manager doesn't approve an order marked as <strong>waiting for approval</strong> in the given time, it will be automatically set as rejected.", 'woocommerce-order-approval'), array('strong'=> array()));?></p>
					<div class="wcoa_option_group wcoa_half">				
						<label class="wcoa_input_label"><?php esc_html_e('Time interval', 'woocommerce-order-approval');?></label>
						<?php  $value = wcoa_get_value_if_set($options, array('automatic_rejection','time_interval'), "never"); ?>
						<select name="wcoa_options[automatic_rejection][time_interval]">
							<option value="never"><?php esc_html_e('Ignore this option', 'woocommerce-order-approval');?></option>
							<option value="wcoa_5_minutes"><?php esc_html_e('Every 5 minutes', 'woocommerce-order-approval');?></option>
							<option value="wcoa_10_minutes"><?php esc_html_e('Every 10 minutes', 'woocommerce-order-approval');?></option>
							<option value="wcoa_15_minutes"><?php esc_html_e('Every 15 minutes', 'woocommerce-order-approval');?></option>
							<option value="wcoa_30_minutes"><?php esc_html_e('Every 30 minutes', 'woocommerce-order-approval');?></option>
							<option value="hourly"><?php esc_html_e('Hourly', 'woocommerce-order-approval');?></option>
							<option value="daily"><?php esc_html_e('Daily', 'woocommerce-order-approval');?></option>
						</select>
					</div>-->
					
					<h4><?php esc_html_e('Automatic approval', 'woocommerce-order-approval');?></h4>
					<p><?php echo wp_kses(__("If at least one of the following condition is verified, the order will be automatically approved.", 'woocommerce-order-approval'), array('strong'=> array()));?></p>
					
					<h5><?php esc_html_e('Payment gateway', 'woocommerce-order-approval');?></h5>
						<p><?php echo wp_kses(__("Select for which payment gateway the order will be automatically set as approved. <strong>Note:</strong> in case of automatic approval, no approval links will be embedded in the new order email.", 'woocommerce-order-approval'), array('strong'=> array()));?></p>
						<div class="wcoa_option_group">
						<?php  $payment_gateways = WC()->payment_gateways->get_available_payment_gateways();
								$value = wcoa_get_value_if_set($options, array('automatic_approval','payment_gateways'), array()); 
								foreach( (array)$payment_gateways as $gateway ) 
									if( $gateway->enabled == 'yes' ): ?>
									<div class="wcoa_checkbox_container">
										<label><?php echo $gateway->title; ?></label>
										<label class="switch">
										  <input type="checkbox" class="wcoa_toggle" name="wcoa_options[automatic_approval][payment_gateways][<?php echo $gateway->id; ?>]" value="true" <?php checked(isset($value[$gateway->id])) ?>  ?>>
										  <span class="slider"></span>
										</label>	
									</div>
								<?php endif;	?>
						</div>
					
				<div class="wcoa_info">
					<h3><?php esc_html_e('Info', 'woocommerce-order-approval');?></h3> 
					<p><?php echo wp_kses(__("In case of <strong>Wait and pay</strong> type selection, Orders marked as <strong>approved</strong> will be <strong>always</strong> considered as still to be paid. The approved order can be paid by the customer <strong>through the Order details page</strong>. ", 'woocommerce-order-approval'), $allowed_tags);?></p> 
				</div>	
				
				<h5><?php esc_html_e('Order subtotal', 'woocommerce-order-approval');?></h5>
				<p><?php echo wp_kses(__("If the order subtotal (the sum of products costs excluding taxes) is more that the set value, it will be automatically approved. <strong>Note:</strong> Leave emtpy or 0 to ignore this setting.", 'woocommerce-order-approval'), array('strong'=> array()));?></p>
				<div class="wcoa_option_group">
					<label class="wcoa_input_label"><?php esc_html_e('Amount', 'woocommerce-order-approval');?></label>
					<?php  $value = wcoa_get_value_if_set($options, array('automatic_approval','order_total'), ""); ?>
					<div class="wcoa_checkbox_container">
						<input type="number" step="0.001" min="0"  name="wcoa_options[automatic_approval][order_total]"  value="<?php esc_attr_e($value); ?>"></input>
					</div>							
				</div>
				
				<h5><?php esc_html_e('User role', 'woocommerce-order-approval');?></h5>
				<p><?php echo wp_kses(__("If the user belongs to one of the selected roles, the order will be approved. <strong>Note:</strong> Leave emtpy to ignore this setting.", 'woocommerce-order-approval'), array('strong'=> array()));?></p>
				<div class="wcoa_option_group wcoa_half">
					<label class="wcoa_input_label"><?php esc_html_e('Roles', 'woocommerce-order-approval');?></label>
					<?php $roles = $wcoa_user_model->get_roles_list(); 
						 // $roles['guest'] = array('name' => esc_html_e('Guest', 'woocommerce-order-approval');)
						  $selected_roles = wcoa_get_value_if_set($options, array('automatic_approval','user_role'), array());
					?>
					<select name="wcoa_options[automatic_approval][user_role][]" class="wcoa_select2 " multiple="multiple">
						<?php 
							
							foreach( $roles as $id => $role)
								{
									echo '<option value="'.$id.'" '.selected(in_array($id, $selected_roles), 1).'>'.$role['name'].'</option>';
								}
							?>
					</select>					
				</div>
				
				
				<h5><?php esc_html_e('Shipping methods', 'woocommerce-order-approval');?></h5>
				<p><?php echo wp_kses(__("Select for which shipping gateway the order will be automatically set as approved. <strong>Note:</strong> in case of automatic approval, no approval links will be embedded in the new order email.", 'woocommerce-order-approval'), array('strong'=> array()));?></p>
				<div class="wcoa_option_group">
				<?php 
				$zones = \WC_Shipping_Zones::get_zones();
				$value = wcoa_get_value_if_set($options, array('automatic_approval','shipping_method'), array()); 
				if ( ! empty( $zones ) )  
					foreach ( $zones as $zone_id => $zone_data ) 
					{
						$zone = \WC_Shipping_Zones::get_zone( $zone_id ); 
						$zone_methods = $zone->get_shipping_methods(); 
					
						if(isset($zone_methods))
							foreach($zone->get_shipping_methods() as $instance_id => $method)
							{	
								//1. Support to new Table Shipping Rating plugin rates (CodeCanyon)
								if(get_class($method) == 'BE_Table_Rate_Method')
								{
									continue; //No support
									$be_table_rates = get_option( $method->id . '_options-' . $method->instance_id );
									foreach($be_table_rates['settings'] as $be_rate)
									{
										$method_tile = $be_rate['title'];
										$shipping_rate_id = $instance_id."-".$be_rate['option_id'];
										$shipping_id = $method->id.":".$shipping_rate_id;
								?>
								<div class="wcoa_checkbox_container">
									<label><?php echo $zone->get_zone_name()." - ".esc_html( $method_tile ); ?></label>
									<label class="switch">
									  <input type="checkbox" class="wcoa_toggle" name="wcoa_options[automatic_approval][shipping_method][<?php echo $shipping_id; ?>]" value="true" <?php checked(isset($value[$shipping_id])) ?>>
									  <span class="slider"></span>
									</label>	
								</div>
								<?php
									}
								}
								//2. Support to Woo Table Shipping Rating plugin
								elseif(method_exists($method, 'get_shipping_rates'))
								{
									continue; //No support
									$shipping_rates = $method->get_shipping_rates();
									foreach($shipping_rates as $shipping_rate)
									{
											
											$method_tile = $zone_methods[$shipping_rate->shipping_method_id]->title; //$shipping_rate->rate_label;
											$method_sub_title = $shipping_rate->rate_label;
											$shipping_rate_id = $instance_id.":".$shipping_rate->rate_id;
											$shipping_id = $method->id.":".$shipping_rate_id;
									?>
									<div class="wcoa_checkbox_container">
										<label><?php echo $zone->get_zone_name()." - ".esc_html( $method_tile ); ?></label>
										<label class="switch">
										  <input type="checkbox" class="wcoa_toggle" name="wcoa_options[automatic_approval][shipping_method][<?php echo $shipping_id; ?>]" value="true" <?php checked(isset($value[$shipping_id])) ?>>
										  <span class="slider"></span>
										</label>	
									</div>
									<?php
									}
								}
								//3. Native WooCommerce methods
								else
								{
									$method_tile = $method->get_title();
									$shipping_id =  $method->id.":".$instance_id;
								?>
									<div class="wcoa_checkbox_container">
										<label><?php echo $zone->get_zone_name()." - ".esc_html( $method_tile ); ?></label>
										<label class="switch">
										  <input type="checkbox" class="wcoa_toggle" name="wcoa_options[automatic_approval][shipping_method][<?php echo $shipping_id; ?>]" value="true" <?php checked(isset($value[$shipping_id])) ?> >
										  <span class="slider"></span>
										</label>	
									</div>
								<?php
								}
							}
					}
				?>
				</div>
				
				<h5><?php esc_html_e('Product & Category', 'woocommerce-order-approval');?></h5>
				<div class="wcoa_option_group wcoa_half" >
					<label class="wcoa_input_label"><?php esc_html_e('Approval type', 'woocommerce-order-approval');?></label>
					<p><?php esc_html_e("The order will be automatically approved / requires manual approval if at least one of the following products is purchase.", 'woocommerce-order-approval');?></p>
					<?php $workflow_type = wcoa_get_value_if_set($options, array('automatic_approval','by_product', 'workflow_type'), 'automatic_approval'); ?>
					<select name="wcoa_options[automatic_approval][by_product][workflow_type]" >	
						<option value="automatic_approval" <?php  selected($workflow_type, 'automatic_approval'); ?>><?php esc_html_e('Automatically approved for selected products/categories', 'woocommerce-order-approval');?></option>
						<option value="manual_approval" <?php  selected($workflow_type, 'manual_approval'); ?>><?php esc_html_e('Manual approval only for selected products/categories', 'woocommerce-order-approval');?></option>
					</select>
				</div>
				<div class="wcoa_option_group wcoa_half" >
					<label class="wcoa_input_label"><?php esc_html_e('Consider also products belonging to subcategories of the selected categories?', 'woocommerce-order-approval');?></label>
					<?php  $selected = wcoa_get_value_if_set($options, array('automatic_approval','by_product', 'consider_subcategories'), true) ? " checked='checked' " : " "; ?>
					<label class="switch">
					  <input type="checkbox" class="wcoa_toggle" name="wcoa_options[automatic_approval][by_product][consider_subcategories]" data-related-id="time" value="true" <?php esc_html_e($selected); ?>>
					  <span class="slider"></span>
					</label>
				</div>
				<div class="wcoa_spacer"></div>
				<div class="wcoa_option_group wcoa_half">
				<label class="wcoa_input_label"><?php esc_html_e('Products', 'woocommerce-order-approval');?></label>
					<select name="wcoa_options[automatic_approval][by_product][product][]" class="wcoa_select2 wcoa_product_selector" multiple="multiple">
						<?php 
							$selected_products = wcoa_get_value_if_set($options, array('automatic_approval','by_product', 'product'), array());
							foreach( $selected_products as $product_id)
								{
									echo '<option value="'.$product_id.'" selected="selected" >'.$wcoa_product_model->get_product_name($product_id).'</option>';
								}
							?>
					</select>
				</div>
				<div class="wcoa_option_group wcoa_half">
				<label class="wcoa_input_label"><?php esc_html_e('Categories', 'woocommerce-order-approval');?></label>
					<select name="wcoa_options[automatic_approval][by_product][category][]" class="wcoa_select2 wcoa_category_selector" multiple="multiple">	
					<?php 
						$selected_categories = wcoa_get_value_if_set($options, array('automatic_approval','by_product', 'category'), array());
						foreach( $selected_categories as $category_id)
							{
								echo '<option value="'.$category_id.'" selected="selected" >'.$wcoa_product_model->get_product_category_name($category_id).'</option>';
							}
						?>
					</select>
				</div>
				<div class="wcoa_option_group wcoa_half">
				<label class="wcoa_input_label"><?php esc_html_e('Tags', 'woocommerce-order-approval');?></label>
				<select name="wcoa_options[automatic_approval][by_product][tag][]" class="wcoa_select2 wcoa_tag_selector" multiple="multiple">	
					<?php 
						$selected_tags = wcoa_get_value_if_set($options, array('automatic_approval','by_product', 'tag'), array());
						foreach( $selected_tags as $tag_id)
							{
								echo '<option value="'.$tag_id.'" selected="selected" >'.$wcoa_product_model->get_product_tag_name($tag_id).'</option>';
							}
						?>
					</select>
				</div>
				
				<div class="wcoa_info">
					<h3><?php esc_html_e('Info', 'woocommerce-order-approval');?></h3> 
					<p><?php echo wp_kses(__("<strong>Manual approval example:</strong> if the order contains <strong>none of the selected</strong> products or categories, it will be <strong>automatically approved</strong>", 'woocommerce-order-approval'), $allowed_tags);?></p> 
					<p><?php echo wp_kses(__("<strong>Automatical approve example:</strong> if the order contains at least one of the selected products or categories, the order will be automatically approved, otherwise it will require a manual approval by the shop manager.", 'woocommerce-order-approval'), $allowed_tags);?></p> 
				</div>	
				
				<p class="submit">
					<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save', 'woocommerce-order-approval'); ?>" />
				</p>
			</form>			
		</div>
		<?php 
	}
}
?>