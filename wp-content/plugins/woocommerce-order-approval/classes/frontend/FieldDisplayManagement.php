<?php 
namespace WCOA\classes\frontend;

class FieldDisplayManagement
{
	public function __construct()
	{
		add_filter( 'woocommerce_billing_fields', array(&$this, 'add_checkout_fields') );
		add_action( 'woocommerce_before_checkout_billing_form', array(&$this, 'add_checkout_data') );
		
		//Validation
		add_action('woocommerce_checkout_process', array( &$this, 'validate_time_selection' )); 
		
		//Display on order details and emails 
		add_action('woocommerce_email_after_order_table',array(&$this, 'show_field_in_email'), 8 , 4);
		add_action('woocommerce_order_details_after_order_table',array(&$this, 'render_field_table'));
	}
	function validate_time_selection()
	{
		global $wcoa_option_model, $wcoa_wpml_model, $wcoa_time_model;
		$lang = $wcoa_wpml_model->get_current_language();
		$options = $wcoa_option_model->get_options();
		$texts = $wcoa_option_model->get_text_options();
		$mandatory = wcoa_get_value_if_set($options, array('time_selector','mandatory'), false);
		$error_msg = wcoa_get_value_if_set($texts , array('checkout', 'time_selection_error', $lang), "The order cannot be placed, no valid time is available");
				
		if($mandatory && isset($_POST['billing_wcoa_time']) && $_POST['billing_wcoa_time'] == "none")
		{
			wc_add_notice( $error_msg, 'error' );
		}
		
		
	}
	function add_checkout_fields($fields)
	{
		global $wcoa_option_model, $wcoa_wpml_model, $wcoa_time_model;
		$options = $wcoa_option_model->get_options();
		$display = wcoa_get_value_if_set($options, array('time_selector', 'display'), false);
		if(!$display)
			return $fields;
		
		$selected = wcoa_get_value_if_set($options, array('time_selector','mandatory'), false);
		$texts = $wcoa_option_model->get_text_options();
		$lang = $wcoa_wpml_model->get_current_language();
		
		//woocommerce_form_field		
		$fields['billing_wcoa_time'] = array(
        'label'        => $content = wcoa_get_value_if_set($texts , array('checkout', 'time_selector_label', $lang), "Time"),
        'type'        => 'select', //time
        'class'        => array( 'form-row-wide' ),
		'input_class' => array( 'wcoa_timepicker'),
        /* 'priority'     => 35, */
        'required'     => $selected,
		'options' 	   => $wcoa_time_model->get_time_selector_options(),
		'select2' 	   => true
		);
	
		return $fields;
	}
	function add_checkout_data()
	{
		wp_enqueue_style( 'wcoa-timepicker-core', WCOA_PLUGIN_PATH.'/css/vendor/datepicker/classic.css');
		wp_enqueue_style( 'wcoa-timepicker', WCOA_PLUGIN_PATH.'/css/vendor/datepicker/classic.time.css');
		
		wp_enqueue_script( 'wcoa-datepicker-core', WCOA_PLUGIN_PATH.'/js/vendor/datepicker/picker.js', array('jquery'));
		wp_enqueue_script( 'wcoa-datepicker-time', WCOA_PLUGIN_PATH.'/js/vendor/datepicker/picker.time.js', array('jquery'));
		wp_enqueue_script( 'wcoa-checkout-page', WCOA_PLUGIN_PATH.'/js/frontend-checkout-page.js', array('jquery'));
	}
	function show_field_in_email($order, $sent_to_admin, $plain_text, $email = null)
	{
		$this->render_field_table($order, true);
	}
	function render_field_table($order, $is_email = false)
	{
		global $wcoa_order_model;
		$text_align = is_rtl() ? 'right' : 'left';
		$i = 0;
			
		if(!$is_email)
			wp_enqueue_style('wcoa-order-details-page', WCOA_PLUGIN_PATH.'/css/frontend-order-details-page.css'); 
		
		//foreach($form_types  as $form_type => $form_type_label)
		{
			$exists_at_least_one_field = false;
			
			ob_start();
			?>
				<?php if(!$is_email): ?>
					<table class="woocommerce-table woocommerce-table--order-details shop_table order_details wcoa_fields_table" >
				<?php else: ?>
					<table class="td" cellspacing="0" cellpadding="6" style="margin-top:10px; margin-bottom:30px; width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
				<?php endif; ?>
				<tfoot>
				<?php 
			
/* 			if(isset($form_data[$form_type]))
				foreach($form_data[$form_type] as $field_id => $form_field) */
				{
					$time = $wcoa_order_model->get_time_field($order);
					$exists_at_least_one_field = $time ? true : false;
					$time = $time == 'as_soon_as_possible' ? esc_html__('As soon as possible','woocommerce-order-approval') : $time;
					
					?>
						<?php if(!$is_email):?>
						<tr>
							<th scope="row" class="wcoa_table_fields_th" ><?php esc_html_e('Time','woocommerce-order-approval');  ?></th>
							<td class="wcoa_table_fields_td" ><?php echo  $time //esc_html( $meta_value ); ?></td>
						</tr>
						<?php else:?>
						<tr>
							<th class="td" scope="row" colspan="2" style="text-align:<?php echo $text_align; ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php esc_html_e('Time','woocommerce-order-approval') ?></th>
							<td class="td" style="text-align:<?php echo $text_align; ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo $time; ?></td>
						</tr>
						<?php $i++; endif;?>
					<?php 
					$i++;
				}
			?>
			</tfoot>
			</table>
			<?php
			$html = ob_get_contents();
			ob_end_clean();
		
			if($exists_at_least_one_field)
			{
				//echo '<h2 class="woocommerce-order-details__title">'.$form_type_label.'</h2>';
				echo $html;
			}
		}
	}
}
?>