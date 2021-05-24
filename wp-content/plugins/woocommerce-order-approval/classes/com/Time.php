<?php 
namespace WCOA\classes\com;

class Time 
{
	public function __construct()
	{
		
	}
	public function format_datetime($date)
	{
		if($date == "" || !isset($date))
			return "";
		
		$date = new \WC_DateTime($date);
		return $date->format(get_option('date_format')." ".get_option('time_format'));
	}
	public function get_time_selector_options()
	{
		global $wcoa_option_model;
		
		$options = $wcoa_option_model->get_options();
		
		//Time options
		$time_options = array();
		$minimum_time_offset = wcoa_get_value_if_set($options, array('time_selector','minimum_time_offset'), 10);
		$time_format = wcoa_get_value_if_set($options, array('time_selector','time_format'), 'H:i');
		$max_time = wcoa_get_value_if_set($options, array('time_selector','maximum_time'), "");
		$min_time = wcoa_get_value_if_set($options, array('time_selector','minimum_time'), "");
		$minimum_time_is_now = wcoa_get_value_if_set($options, array('time_selector','minimum_time_is_now'), false);
		if(wcoa_get_value_if_set($options, array('time_selector','as_soon_as_possible_option'), false))
			$time_options['as_soon_as_possible'] = esc_html__('As soon as possible', 'woocommerce-order-approval');
		
		//$now = new \DateTime(current_time($time_format));
		try{
			$start = new \DateTime($min_time != "" ?  $min_time : "00:00:00");
		}catch(\Exception $e){$start = new \DateTime( "00:00:00");}
		
		try{
			$end = new \DateTime($max_time != "" ? $max_time : "23:59:59");
		}catch(\Exception $e){$end = new \DateTime("23:59:59");}
		
		if($minimum_time_is_now)
			$now_with_offset = new \DateTime(current_time('mysql'));
		else if($min_time != "")
			$now_with_offset = new \DateTime($min_time);
		else 
			$now_with_offset = new \DateTime("00:00:00");
		$now_with_offset = $now_with_offset->modify("+{$minimum_time_offset} minutes");
		while($start < $end)
		{
			if($start >= $now_with_offset)
				$time_options[$start->format($time_format)] = $start->format($time_format);
			$start->modify("+10 minutes");
		}
		
		if(count($time_options) == 1 and isset($time_options['as_soon_as_possible']))
			$time_options = array("none" =>__('Unavailable', 'woocommerce-order-approval'));
		
		//wcoa_var_dump(new \DateTime(current_time($time_format)));
		return $time_options;
	}
	public function time_compare($time1, $offset)
	{
		//$time1_obj = new \DateTime($time1);
		$time2_obj = new \DateTime($time1);
		$time3_obj = $time2_obj->modify("+{$offset} minutes");
		$current_time = new \DateTime(current_time('mysql'));
		
		if($current_time > $time2_obj)
			return 1;
		if($current_time < $time2_obj)
			return 2;
		
		return 0;
	}
}
?>