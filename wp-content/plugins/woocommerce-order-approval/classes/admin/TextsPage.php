<?php 
namespace WCOA\classes\admin;

class TextsPage
{
	public function __construct()
	{
		
	}
	
	//rplc: woocommerce-order-approval, wcoa, WCOA
	public function render_page()
	{
		
		global $wcoa_option_model, $wcoa_wpml_model;
		
		//Assets
		wp_enqueue_style( 'wcoa-admin-common', WCOA_PLUGIN_PATH.'/css/admin-common.css');
		wp_enqueue_style( 'wcoa-admin-text-page', WCOA_PLUGIN_PATH.'/css/admin-texts-page.css');
		
		
		wp_enqueue_script( 'wcoa-admin-texts-page', WCOA_PLUGIN_PATH.'/js/admin-texts-page.js', array('jquery'));
		
		
		//Save
		if(isset($_POST['wcoa_text_options']))
		{
			$wcoa_option_model->save_text_options($_POST['wcoa_text_options']);
		}
		
		//Load
		$options = $wcoa_option_model->get_text_options();
		$default_texts = $wcoa_option_model->get_default_texts();
		$langs =  $wcoa_wpml_model->get_langauges_list();
		$allowed_tags = array('br' => array(), 'p' => array(), 'strong' => array());
		$active_tab = wcoa_get_value_if_set($_POST, 'active_tab_ref', 'approval_area_tab');
		?>
		<?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
			<div class="notice notice-success is-dismissible">
				 <p><?php esc_html_e('Saved successfully!', 'woocommerce-order-approval'); ?></p>
			</div>
		<?php endif; ?>
		<div class="wrap white-box">
			<!-- <form action="options.php" method="post" > -->
			<form action="" method="post"  id="wcoa_text_settings_form">
				<?php //settings_fields('wcoa_options_group'); ?> 
					<div class="tab">
					  <button class="tablinks <?php if($active_tab == 'approval_area_tab') echo 'active'; ?>" data-tab="approval_area_tab" id="approval_area_tab_button"><?php esc_html_e('Approval area', 'woocommerce-order-approval'); ?></button>
					  <button class="tablinks <?php if($active_tab == 'emails_tab') echo 'active'; ?>" data-tab="emails_tab" id="emails_tab_button"><?php esc_html_e('Emails', 'woocommerce-order-approval'); ?></button>
					  <button class="tablinks <?php if($active_tab == 'checkout_tab') echo 'active'; ?>" data-tab="checkout_tab" id="checkout_tab_button"><?php esc_html_e('Checkout', 'woocommerce-order-approval'); ?></button>
					</div>
				<input type="hidden" id="active_tab_ref" name="active_tab_ref" value="<?php echo $active_tab; ?>" />	
				
				<div id="approval_area_tab" class="tabcontent" style="display: block;">
					<!-- <h2 class="wcoa_section_title wcoa_no_margin_top"><?php esc_html_e('Approval area', 'woocommerce-order-approval');?></h2>-->
					
					<h3><?php esc_html_e('Section title', 'woocommerce-order-approval');?></h3> 
					<div class="wcoa_option_group">
						<?php foreach($langs as $lang_data): ?>
									<div class="wcoa_message_container">
										<?php if($lang_data['country_flag_url'] != "none"): ?>
											<img src=<?php esc_attr_e($lang_data['country_flag_url']); ?> /><label class="wcoa_label"> <?php esc_attr_e($lang_data['language_code']); ?></label>
										<?php endif; 
										$content = wcoa_get_value_if_set($options , array('approval_page', 'area_title', $lang_data['language_code']), "");
										?>
										<input type="text" class="wcoa_input_text" value="<?php esc_html_e($content);?>" name="wcoa_text_options[approval_page][area_title][<?php esc_attr_e($lang_data['language_code']); ?>]"></input>
									</div>
							<?php endforeach; ?>
					</div>
					
					<h3><?php esc_html_e('Default message - Approved', 'woocommerce-order-approval');?></h3> 
					<p><?php esc_html_e('This message will be shown to give additional details about the approval. In addition you can: ', 'woocommerce-order-approval');?></p>
					<ol>
							<li><?php esc_html_e('Override this text through the admin order pages.', 'woocommerce-order-approval');?></li>
							<li><?php echo wp_kses(__('Use special <strong>[datetime]</strong> shortcode to display the datetime eventually set via the admin order page. If no time has been selected, that shortcode will display an empty string.', 'woocommerce-order-approval'),$allowed_tags);?></li>							
					</ol>
					<div class="wcoa_option_group">
						<?php foreach($langs as $lang_data): ?>
									<div class="wcoa_message_container">
										<?php if($lang_data['country_flag_url'] != "none"): ?>
											<img src=<?php esc_attr_e($lang_data['country_flag_url']); ?> /><label class="wcoa_label"> <?php esc_attr_e($lang_data['language_code']); ?></label>
										<?php endif; 
										$content = wcoa_get_value_if_set($options , array('approval_page', 'message', 'approved', $lang_data['language_code']), "");
										wp_editor( $content, "wcoa_message_approval_page_editor_".$lang_data['language_code'], array( 'media_buttons' => false,
																														   'textarea_rows' => 8,
																														   'tinymce' => true,
																														   "wpautop" => false,
																														   'editor_height' => 350,
																														   'textarea_name'=>"wcoa_text_options[approval_page][message][approved][".$lang_data['language_code']."]")); ?>
									</div>
							<?php endforeach; ?>
						</div>
						<h3><?php esc_html_e('Default message - Rejected', 'woocommerce-order-approval');?></h3> 
					<p><?php esc_html_e('This message will be shown to give additional details about the approval. You can override this text through each specific admin order page.', 'woocommerce-order-approval');?></p>
					<div class="wcoa_option_group">
						<?php foreach($langs as $lang_data): ?>
									<div class="wcoa_message_container">
										<?php if($lang_data['country_flag_url'] != "none"): ?>
											<img src=<?php esc_attr_e($lang_data['country_flag_url']); ?> /><label class="wcoa_label"> <?php esc_attr_e($lang_data['language_code']); ?></label>
										<?php endif; 
										$content = wcoa_get_value_if_set($options , array('approval_page', 'message', 'rejected', $lang_data['language_code']), "");
										wp_editor( $content, "wcoa_message_rejected_page_editor_".$lang_data['language_code'], array( 'media_buttons' => false,
																														   'textarea_rows' => 8,
																														   'tinymce' => true,
																														   "wpautop" => false,
																														   'editor_height' => 350,
																														   'textarea_name'=>"wcoa_text_options[approval_page][message][rejected][".$lang_data['language_code']."]")); ?>
									</div>
							<?php endforeach; ?>
						</div>
						
						
						<h3><?php esc_html_e('Timeline - Waiting for approval step', 'woocommerce-order-approval');?></h3> 
						<div class="wcoa_option_group">
						<?php foreach($langs as $lang_data): ?>
									<div class="wcoa_message_container">
										<?php if($lang_data['country_flag_url'] != "none"): ?>
											<img src=<?php esc_attr_e($lang_data['country_flag_url']); ?> /><label class="wcoa_label"> <?php esc_attr_e($lang_data['language_code']); ?></label>
										<?php endif; 
										$content = wcoa_get_value_if_set($options , array('approval_page', 'timeline', 'approval-waiting', $lang_data['language_code']), "");
										?>
										<input type="text" class="wcoa_input_text" value="<?php esc_html_e($content);?>" name="wcoa_text_options[approval_page][timeline][approval-waiting][<?php esc_attr_e($lang_data['language_code']); ?>]"></input>
									</div>
							<?php endforeach; ?>
						</div>
						<h3><?php esc_html_e('Timeline - Order approved step', 'woocommerce-order-approval');?></h3> 
						<div class="wcoa_option_group">
						<?php foreach($langs as $lang_data): ?>
									<div class="wcoa_message_container">
										<?php if($lang_data['country_flag_url'] != "none"): ?>
											<img src=<?php esc_attr_e($lang_data['country_flag_url']); ?> /><label class="wcoa_label"> <?php esc_attr_e($lang_data['language_code']); ?></label>
										<?php endif; 
										$content = wcoa_get_value_if_set($options , array('approval_page', 'timeline', 'approved', $lang_data['language_code']), "");
										?>
										<input type="text" class="wcoa_input_text" value="<?php esc_html_e($content);?>" name="wcoa_text_options[approval_page][timeline][approved][<?php esc_attr_e($lang_data['language_code']); ?>]"></input>
									</div>
							<?php endforeach; ?>
						</div>
						<h3><?php esc_html_e('Timeline - Order rejected step', 'woocommerce-order-approval');?></h3> 
						<div class="wcoa_option_group">
						<?php foreach($langs as $lang_data): ?>
									<div class="wcoa_message_container">
										<?php if($lang_data['country_flag_url'] != "none"): ?>
											<img src=<?php esc_attr_e($lang_data['country_flag_url']); ?> /><label class="wcoa_label"> <?php esc_attr_e($lang_data['language_code']); ?></label>
										<?php endif; 
										$content = wcoa_get_value_if_set($options , array('approval_page', 'timeline', 'rejected', $lang_data['language_code']), "");
										?>
										<input type="text" class="wcoa_input_text" value="<?php esc_html_e($content);?>" name="wcoa_text_options[approval_page][timeline][rejected][<?php esc_attr_e($lang_data['language_code']); ?>]"></input>
									</div>
							<?php endforeach; ?>
						</div>
				</div>
				<div id="emails_tab" class="tabcontent">
					<div class="wcoa_shortcode_container">
						<p><?php esc_html_e('You can use the following shortcodes:', 'woocommerce-order-approval');?></p>
						<ol>
							<li><label><?php esc_html_e('Order data', 'woocommerce-order-approval');?></label> [order_id], [order_total], [order_date], [order_page_url]</li>
							<li><label><?php esc_html_e('Billing data', 'woocommerce-order-approval');?></label> [billing_first_name], [billing_last_name], [billing_email], [billing_company], [billing_company], [billing_phone], [billing_country], [billing_state], [billing_city], [billing_post_code], [billing_address_1], [billing_address_2], [formatted_billing_address]</li>
							<li><label><?php esc_html_e('Shipping data', 'woocommerce-order-approval');?></label> [shipping_first_name], [shipping_last_name], [shipping_phone], [shipping_country], [shipping_state], [shipping_city], [shipping_post_code], [shipping_address_1], [shipping_address_2], [formatted_shipping_address]</li>
							<li><label><?php esc_html_e('Payment page', 'woocommerce-order-approval');?></label> [payment_page_url]</li>
							<li><label><?php esc_html_e('Order custom approval message (this can be set through the order details page)', 'woocommerce-order-approval');?></label> [custom_approval_message]</li>
						</ol>
					</div>								
					
					
					<h2 class="wcoa_section_title"><?php esc_html_e('Approved order notification email', 'woocommerce-order-approval');?></h2>
					
					<h3><?php esc_html_e('Heading', 'woocommerce-order-approval');?></h3> 
						<div class="wcoa_option_group">
						<?php foreach($langs as $lang_data): ?>
									<div class="wcoa_message_container">
										<?php if($lang_data['country_flag_url'] != "none"): ?>
											<img src=<?php esc_attr_e($lang_data['country_flag_url']); ?> /><label class="wcoa_label"> <?php esc_attr_e($lang_data['language_code']); ?></label>
										<?php endif; 
										$content = wcoa_get_value_if_set($options , array('email', 'approved','heading', $lang_data['language_code']), "");
										?>
										<input type="text" class="wcoa_input_text" value="<?php esc_html_e($content);?>" name="wcoa_text_options[email][approved][heading][<?php esc_attr_e($lang_data['language_code']); ?>]"></input>
									</div>
							<?php endforeach; ?>
						</div>	
						
					<h3><?php esc_html_e('Subject', 'woocommerce-order-approval');?></h3> 
						<div class="wcoa_option_group">
						<?php foreach($langs as $lang_data): ?>
									<div class="wcoa_message_container">
										<?php if($lang_data['country_flag_url'] != "none"): ?>
											<img src=<?php esc_attr_e($lang_data['country_flag_url']); ?> /><label class="wcoa_label"> <?php esc_attr_e($lang_data['language_code']); ?></label>
										<?php endif; 
										$content = wcoa_get_value_if_set($options , array('email', 'approved', 'subject', $lang_data['language_code']), "");
										?>
										<input type="text" class="wcoa_input_text" value="<?php esc_html_e($content);?>" name="wcoa_text_options[email][approved][subject][<?php esc_attr_e($lang_data['language_code']); ?>]"></input>
									</div>
							<?php endforeach; ?>
						</div>	
						
					<h3><?php esc_html_e('Body', 'woocommerce-order-approval');?></h3> 
					
					<div class="wcoa_option_group">
						<div id="wcoa_messages_container">
						<?php	
							
							foreach($langs as $lang_data): ?>
									<div class="wcoa_message_container">
										<?php if($lang_data['country_flag_url'] != "none"): ?>
											<img src=<?php esc_attr_e($lang_data['country_flag_url']); ?> /><label class="wcoa_label"> <?php esc_attr_e($lang_data['language_code']); ?></label>
										<?php endif; 
										$content = wcoa_get_value_if_set($options , array('email', 'approved','body',  $lang_data['language_code']), "");
										wp_editor( $content, "wcoa_message_approval_email_editor_".$lang_data['language_code'], array( 'media_buttons' => false,
																														   'textarea_rows' => 8,
																														   'tinymce' => true,
																														   "wpautop" => false,
																														   'editor_height' => 350,
																														   'textarea_name'=>"wcoa_text_options[email][approved][body][".$lang_data['language_code']."]")); ?>
									</div>
							<?php endforeach; ?>
						</div>	
					</div>
					
					<h2 class="wcoa_section_title"><?php esc_html_e('Rejected order notification email', 'woocommerce-order-approval');?></h2>
					<h3><?php esc_html_e('Heading', 'woocommerce-order-approval');?></h3> 
						<div class="wcoa_option_group">
						<?php foreach($langs as $lang_data): ?>
									<div class="wcoa_message_container">
										<?php if($lang_data['country_flag_url'] != "none"): ?>
											<img src=<?php esc_attr_e($lang_data['country_flag_url']); ?> /><label class="wcoa_label"> <?php esc_attr_e($lang_data['language_code']); ?></label>
										<?php endif; 
										$content = wcoa_get_value_if_set($options , array('email', 'rejected','heading', $lang_data['language_code']), "");
										?>
										<input type="text" class="wcoa_input_text" value="<?php esc_html_e($content);?>" name="wcoa_text_options[email][rejected][heading][<?php esc_attr_e($lang_data['language_code']); ?>]"></input>
									</div>
							<?php endforeach; ?>
						</div>	
						
						<h3><?php esc_html_e('Subject', 'woocommerce-order-approval');?></h3> 
						<div class="wcoa_option_group">
						<?php foreach($langs as $lang_data): ?>
									<div class="wcoa_message_container">
										<?php if($lang_data['country_flag_url'] != "none"): ?>
											<img src=<?php esc_attr_e($lang_data['country_flag_url']); ?> /><label class="wcoa_label"> <?php esc_attr_e($lang_data['language_code']); ?></label>
										<?php endif; 
										$content = wcoa_get_value_if_set($options , array('email', 'rejected', 'subject', $lang_data['language_code']), "");
										?>
										<input type="text" class="wcoa_input_text" value="<?php esc_html_e($content);?>" name="wcoa_text_options[email][rejected][subject][<?php esc_attr_e($lang_data['language_code']); ?>]"></input>
									</div>
							<?php endforeach; ?>
						</div>	
						
					<h3><?php esc_html_e('Body', 'woocommerce-order-approval');?></h3> 
					
					<div class="wcoa_option_group">
						<div id="wcoa_messages_container">
						<?php	
							
							foreach($langs as $lang_data): ?>
									<div class="wcoa_message_container">
										<?php if($lang_data['country_flag_url'] != "none"): ?>
											<img src=<?php esc_attr_e($lang_data['country_flag_url']); ?> /><label class="wcoa_label"> <?php esc_attr_e($lang_data['language_code']); ?></label>
										<?php endif; 
										$content = wcoa_get_value_if_set($options , array('email', 'rejected','body',  $lang_data['language_code']), "");
										wp_editor( $content, "wcoa_message_rejected_email_editor_".$lang_data['language_code'], array( 'media_buttons' => false,
																														   'textarea_rows' => 8,
																														   'tinymce' => true,
																														   "wpautop" => false,
																														   'editor_height' => 350,
																														   'textarea_name'=>"wcoa_text_options[email][rejected][body][".$lang_data['language_code']."]")); ?>
									</div>
							<?php endforeach; ?>
						</div>	
					</div>
					<h2 class="wcoa_section_title"><?php esc_html_e('New order email sent to the Admin', 'woocommerce-order-approval');?></h2>
					<h3><?php esc_html_e('Approval links to embed on email body', 'woocommerce-order-approval');?></h3> 
					<div class="wcoa_shortcode_container">
						<p><?php esc_html_e('You can use the following shortcodes:', 'woocommerce-order-approval');?></p>
						<ol>
							<li><strong>[approve_order_link]</strong>: <?php esc_html_e('It will generate an URL to directly approve the order', 'woocommerce-order-approval');?></li>
							<li><strong>[reject_order_link]</strong>: <?php esc_html_e('It will generate an URL to directly reject the order', 'woocommerce-order-approval');?></li>
						</ol>
					</div>				
						<div class="wcoa_option_group">
							<div id="wcoa_messages_container">
							<?php	
								
								foreach($langs as $lang_data): ?>
										<div class="wcoa_message_container">
											<?php if($lang_data['country_flag_url'] != "none"): ?>
												<img src=<?php esc_attr_e($lang_data['country_flag_url']); ?> /><label class="wcoa_label"> <?php esc_attr_e($lang_data['language_code']); ?></label>
											<?php endif; 
											$content = wcoa_get_value_if_set($options , array('email', 'new_order_admin','text_to_embed',  $lang_data['language_code']), $default_texts['text_to_embed_on_new_order_email_admin']);
											wp_editor( $content, "wcoa_admin_new_order_email_editor_".$lang_data['language_code'], array( 'media_buttons' => false,
																															   'textarea_rows' => 8,
																															   'tinymce' => true,
																															   'wpautop' => false,
																															   'editor_height' => 350,
																															   'textarea_name'=>"wcoa_text_options[email][new_order_admin][text_to_embed][".$lang_data['language_code']."]")); ?>
										</div>
								<?php endforeach; ?>
							</div>	
						</div>	
				</div>
				<div id="checkout_tab" class="tabcontent">
					<h3><?php esc_html_e('Time selector label', 'woocommerce-order-approval');?></h3> 
						<div class="wcoa_option_group">
						<?php foreach($langs as $lang_data): ?>
									<div class="wcoa_message_container">
										<?php if($lang_data['country_flag_url'] != "none"): ?>
											<img src=<?php esc_attr_e($lang_data['country_flag_url']); ?> /><label class="wcoa_label"> <?php esc_attr_e($lang_data['language_code']); ?></label>
										<?php endif; 
										$content = wcoa_get_value_if_set($options , array('checkout', 'time_selector_label', $lang_data['language_code']), "Time");
										?>
										<input type="text" class="wcoa_input_text" value="<?php esc_html_e($content);?>" name="wcoa_text_options[checkout][time_selector_label][<?php esc_attr_e($lang_data['language_code']); ?>]"></input>
									</div>
							<?php endforeach; ?>
						</div>	
						
						<h3><?php esc_html_e('Time selection error', 'woocommerce-order-approval');?></h3> 
						<div class="wcoa_option_group">
						<?php foreach($langs as $lang_data): ?>
									<div class="wcoa_message_container">
										<?php if($lang_data['country_flag_url'] != "none"): ?>
											<img src=<?php esc_attr_e($lang_data['country_flag_url']); ?> /><label class="wcoa_label"> <?php esc_attr_e($lang_data['language_code']); ?></label>
										<?php endif; 
										$content = wcoa_get_value_if_set($options , array('checkout', 'time_selection_error', $lang_data['language_code']), "The order cannot be placed, no valid time is available");
										?>
										<input type="text" class="wcoa_input_text" value="<?php esc_html_e($content);?>" name="wcoa_text_options[checkout][time_selection_error][<?php esc_attr_e($lang_data['language_code']); ?>]"></input>
									</div>
							<?php endforeach; ?>
						</div>	
				</div>
				
					
				<p class="submit">
					<input name="Submit" type="submit" id="submit" class="button-primary" value="<?php esc_attr_e('Save', 'woocommerce-order-approval'); ?>" />
				</p>
			</form>			
		</div>
		<?php 
	}
}
?>