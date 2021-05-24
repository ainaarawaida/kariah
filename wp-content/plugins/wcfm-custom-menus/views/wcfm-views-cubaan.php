<?php
global $WCFM, $wp_query;

?>

<div class="collapse wcfm-collapse" id="wcfm_build_listing">
	
	<div class="wcfm-page-headig">
		<span class="fa fa-cubes"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Cubaan List', 'wcfm-custom-menus' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		<?php do_action( 'before_wcfm_build' ); ?>
		
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php _e('Cubaan List', 'wcfm-custom-menus' ); ?></h2>
			<div class="wcfm-clearfix"></div>
	  </div>
	  <div class="wcfm-clearfix"></div><br />
		

		<div class="wcfm-container">
			<div id="wcfm_build_listing_expander" class="wcfm-content">
			
<!---- Add Content Here ----->
		<div class="wcfm-container">
			<div id="wwcfm_customers_expander" class="wcfm-content">
				<style>
					.wcfm-dashboard-page #wcfm-main-contentainer input[type="checkbox"] {
	
	-webkit-appearance: auto;
}
					</style>
<?php
//global $WCFM;
$order  = $WCFM->wcfm_vendor_support->wcfm_get_orders_by_vendor( get_current_user_id());
//deb($order);exit();



?>
				<table id="wcfm-cubaan" class="display" cellspacing="0" width="100%">
				<thead>
						<tr>
							<th></th>
							<th>Nama</th>
							<th>Email</th>
							<th>Telefon</th>
							<th><?php _e( 'Actions', 'wc-frontend-manager' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th></th>
							<th>Nama</th>
							<th>Email</th>
							<th>Telefon</th>
							<th><?php _e( 'Actions', 'wc-frontend-manager' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
<!---- End Add Content Here ----->


			
				<div class="wcfm-clearfix"></div>
			</div>
			<div class="wcfm-clearfix"></div>
		</div>
	
		<div class="wcfm-clearfix"></div>
		<?php
		do_action( 'after_wcfm_build' );
		?>
	</div>
</div>