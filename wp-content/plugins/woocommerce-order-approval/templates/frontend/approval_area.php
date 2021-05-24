<?php 
/*
Version: 1.0
*/

$first_step_class = !$wcoa_order_model->is_approval_waiting($order) ? 'completed' : '';
$second_step_class = $wcoa_order_model->get_approval_status($order);
$second_step_title_class = $wcoa_order_model->is_approval_waiting($order) ? 'approval-waiting' : '';
$result_text =  $wcoa_order_model->is_approval_waiting($order) ?  wcoa_get_value_if_set($settings , array('approval_page', 'timeline', 'approved', $lang), "") :  
																  wcoa_get_value_if_set($settings , array('approval_page', 'timeline', $wcoa_order_model->get_approval_status($order), $lang), "");
?>
<?php wc_print_notices(); ?>
<div id="wcoa_approval_area">
<h2 id="wcoa_approval_area_title"><?php echo wcoa_get_value_if_set($settings, array('approval_page', 'area_title', $lang), ""); ?></h2>
<div class="history-tl-container">
  <ul class="tl">
	<li class="tl-item <?php echo $first_step_class; ?> first_step">
	  <div class="timestamp">
		 <?php echo $wcoa_order_model->get_creation_date($order); ?>
	  </div>
	  <div class="item-title"><?php echo wcoa_get_value_if_set($settings , array('approval_page', 'timeline', 'approval-waiting', $lang), "");?></div>
	  <!-- <div class="item-detail">Lorem ipsum</div> -->
	</li>
	<li class="tl-item <?php echo $second_step_class; ?>" >
	  <div class="timestamp">
		<?php if(!$wcoa_order_model->is_approval_waiting($order)): 
			echo $wcoa_order_model->get_approval_date($order);
		endif; ?>
	  </div>
	  <div class="item-title <?php echo $wcoa_order_model->get_approval_status($order); ?> "><?php echo $result_text;?></div>
	  <!-- <div class="item-detail">Lorem ipsum</div> -->
	</li>
	
  </ul>
</div>
  <?php if(!$wcoa_order_model->is_approval_waiting($order)): ?>
  <div id="wcoa_custom_message">
  <p>
	<?php 
		$approval_date_time =  wcoa_get_value_if_set($specific_approval_data , 'datetime', "");
		
		if($specific_approval_data['custom_message'] != "")
			$approval_message = $specific_approval_data['custom_message'];
		else 
			$approval_message = wcoa_get_value_if_set($settings , array('approval_page', 'message', $wcoa_order_model->get_approval_status($order), $lang), "");
  
		$approval_message = str_replace('[datetime]', $wcoa_time_model->format_datetime($approval_date_time), $approval_message);
		echo $approval_message;
	?></p>
  </div>
  <?php endif; ?>
  <div id="all_button_container">
	  <?php if($order->needs_payment()): ?>
	  <div id="button_container">
		<a id="payment_button" class="woocommerce-button button pay" href="<?php echo $order->get_checkout_payment_url()?>"><?php esc_html_e('Pay', 'woocommerce-order-approval'); ?></a> 
	  </div>
	  <?php endif;
		if($wcoa_order_model->can_be_cancelled($order)): ?>
		<div id="button_container">
			<a id="cancel_button" class="woocommerce-button button cancel" href="<?php echo $order->get_cancel_order_url($order_url)."&wcoa_cancel=1" ?>"><?php esc_html_e('Cancel', 'woocommerce-order-approval'); ?></a> 
		 </div>
		<?php endif; ?>
  </div>
</div>
