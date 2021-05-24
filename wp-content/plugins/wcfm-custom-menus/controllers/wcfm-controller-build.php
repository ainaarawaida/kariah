<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Products Custom Menus Build Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmcsm/controllers
 * @version   1.0.0
 */

class WCFM_Build_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $WCFMu, $wpdb, $_POST;
		
	  echo '{ "status": true, "message": "' . __( 'Build updated.', 'wc-frontend-manager' ) . '" }';
	  
	  die;
	}
}