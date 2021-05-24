<?php 
namespace WCOA\classes\com;

class Cron
{
	public function __construct()
	{
		//add_action( 'wp_loaded', array(&$this,'check_schedule') );	//wp: fiered only when accessin frontend
		//add_action( 'wcoa_cron_update_rates', array(&$this, 'on_tick' ));
		add_action( 'cron_schedules', array(&$this, 'cron_schedules' ));
	}
	function cron_schedules($schedules)
	{
		if(!isset($schedules["wcoa_5_minutes"]))
		{
			$schedules["wcoa_5_minutes"] = array(
			'interval' => 5*60, 
			'display' => __('Once every 5 Minutes'));
		}
		if(!isset($schedules["wcoa_10_minutes"]))
		{
			$schedules["wcoa_15_minutes"] = array(
			'interval' => 10*60, 
			'display' => __('Once every 10 Minutes'));
		}
		if(!isset($schedules["wcoa_15_minutes"]))
		{
			$schedules["wcoa_15_minutes"] = array(
			'interval' => 15*60, //15 minutes
			'display' => __('Once every 15 Minutes'));
		}
		if(!isset($schedules["wcoa_30_minutes"]))
		{
			$schedules["wcoa_30_minutes"] = array(
			'interval' => 30*60, 
			'display' => __('Once every 30 Minutes'));
		}
		return $schedules;
	}
	function check_schedule() 
	{
		global $wcoa_option_model;
		
		$currency_options = $wcoa_option_model->get_currency_options();
		$update_frequency = wcoa_get_value_if_set($currency_options, array('frequency'), "manually");
		
		if($update_frequency != 'manually')
		{
			wp_next_scheduled( 'wcoa_cron_update_rates' );
			if ( !wp_next_scheduled( 'wcoa_cron_update_rates' ) ) 
			{
				wp_schedule_event( time(), $update_frequency, 'wcoa_cron_update_rates' ); //seconds
			}
		}
		else 
			wp_clear_scheduled_hook( 'wcoa_cron_update_rates' );
		
	}
	function on_tick()
	{
		global $wcoa_currency_model;
		
		//wcoa_var_dump("update_rates");
		//wcoa_write_log($wcoa_currency_model->exists_base_currency() ? 'true': 'false');
		if($wcoa_currency_model->exists_base_currency())
			$wcoa_currency_model->update_currency_rates( $wcoa_currency_model->get_base_currency(), true);
	}
}
?>