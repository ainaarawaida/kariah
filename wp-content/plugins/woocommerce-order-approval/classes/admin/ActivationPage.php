<?php 
namespace WCOA\classes\admin;

class ActivationPage
{
	var $page;
	var $page_slug;
	var $plugin_name;
	var $plugin_slug;
	var $plugin_id;
	var $plugin_path;
	
	//rplc: woocommerce-order-approval, wcoa_ , menu icon ('dashicons-images-alt2')
	public function __construct($page_slug, $plugin_name, $plugin_slug, $plugin_id, $plugin_path)
	{
		$this->page_slug = $page_slug;
		$this->plugin_name = $plugin_name;
		$this->plugin_slug = $plugin_slug;
		$this->plugin_id = $plugin_id;
		$this->plugin_path = $plugin_path;
		add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
		add_action( 'wp_ajax_vanquish_activation_'.$this->plugin_id, array($this, 'process_activation') );
		//add_filter('allowed_http_origins', array($this, 'add_allowed_origins'));
		//add_action('wp', array( &$this,'add_headers_meta'));
		
		$this->add_page();
	}
	function add_allowed_origins($origins) 
	{
		$origins[] = 'https://vanquishplugins.com';
		return $origins;
	}
	function add_headers_meta()
	{
		header("Access-Control-Allow-Origin: *");
	}
	public function process_activation()
	{
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		$domain = isset($_POST['domain']) ? $_POST['domain'] : 'none';
		if($id != 0 && $domain != 'none')
		{
			update_option("_".$id, md5($domain));
		}
		wp_die();
	}
	public function add_page($cap = "manage_woocommerce" )
	{
		if(defined('DOING_AJAX') && DOING_AJAX)
			return;
		
		global $wcoa_notice;
		
		if(!$wcoa_notice)
			$this->page = add_submenu_page( null,
											__($this->plugin_name.' Activator', 'woocommerce-order-approval'), 
											__($this->plugin_name.' Activator', 'woocommerce-order-approval'), 
											  $cap, 
											  $this->page_slug, 
											  array($this, 'render_page')); 
			
		else 
		{
			$place = wcoa_get_free_menu_position(59 , .1);
			$this->page = add_menu_page( $this->plugin_name, $this->plugin_name, 
											$cap, 
											$this->page_slug, 
											array($this, 'render_page'), 
											'dashicons-images-alt2', 
											(string)$place);
		}
		add_action('load-'.$this->page,  array($this,'page_actions'),9);
		add_action('admin_footer-'.$this->page,array($this,'footer_scripts'));
	}
	function footer_scripts(){
		?>
		<script> postboxes.add_postbox_toggles(pagenow);</script>
		<?php
	}
	
	function page_actions()
	{
		do_action('add_meta_boxes_'.$this->page, null);
		do_action('add_meta_boxes', $this->page, null);
	}
	public function render_page()
	{
		global $pagenow;
		
		add_screen_option('layout_columns', array('max' => 1, 'default' => 1) );
		
		wp_register_script('vanquish-activator', $this->plugin_path.'/js/vendor/vanquish/activator.js', array('jquery'));
		 $js_settings = array(
				'purchase_code_invalid' => esc_html__( 'Purchase code is invalid!', 'woocommerce-order-approval' ),
				'buyer_invalid' => esc_html__( 'Buyer name is invalid!', 'woocommerce-order-approval' ),
				'item_id_invalid' => esc_html__( 'Item id is invalid!', 'woocommerce-order-approval' ),
				'num_domain_reached' => esc_html__( 'Max number of domains reached! You have to purchase a new license. The current license has been activated in the following domains: ', 'woocommerce-order-approval' ),
				'status_default_message' => esc_html__( 'Verifing, please wait...', 'woocommerce-order-approval' ),
				'db_error' => esc_html__( 'There was an error while verifing the code. Please retry in few minutes!', 'woocommerce-order-approval' ),
				'purchase_code_valid' => esc_html__( 'Activation successfully completed!', 'woocommerce-order-approval' ),
				'empty_fields_error' => esc_html__( 'Buyer and Purchase code fields must be filled!', 'woocommerce-order-approval' ),
				'verifier_url' => 'https://vanquishplugins.com/activator/verify.php'
			);
		wp_localize_script( 'vanquish-activator', 'vanquish_activator_settings', $js_settings );
		wp_enqueue_script('vanquish-activator'); 
		wp_enqueue_script('postbox');
		
		
		wp_enqueue_style('vanquish-activator',  $this->plugin_path.'/css/vendor/vanquish/activator.css');
		
		?>
		<div class="wrap">
			 <?php //screen_icon(); ?>
			<h2><?php esc_html_e($this->plugin_name.' Activator','woocommerce-order-approval'); ?></h2>
	
			<form id="post"  method="post">
				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-<?php echo 1 /* 1 == get_current_screen()->get_columns() ? '1' : '2' */; ?>">
						<div id="post-body-content">
						</div>
						
						<div id="postbox-container-1" class="postbox-container">
							<?php do_meta_boxes($this->plugin_slug.'-activator','side',null); ?>
						</div>
						
						<div id="postbox-container-2" class="postbox-container">
							  <?php do_meta_boxes($this->plugin_slug.'-activator','normal',null); ?>
							  <?php do_meta_boxes($this->plugin_slug.'-activator','advanced',null); ?>
							  
						</div> 
					</div> <!-- #post-body -->
				</div> <!-- #poststuff -->
				
			</form>
		</div> <!-- .wrap -->
		<?php 
	}
	
	function add_meta_boxes()
	{
		
		 add_meta_box( 'vanquish_activation', 
					__('Activation','woocommerce-order-approval'), 
					array($this, 'render_product_fields_meta_box'), 
					$this->plugin_slug.'-activator', 
					'normal' //side
			); 
	}
	function render_product_fields_meta_box()
	{
		$domain = $_SERVER['SERVER_NAME'];
		$result = get_option("_".$this->plugin_id);
		$result = !$result || $result != md5($domain);
		?>
			<div id="activator_main_container">
				<?php if($result): ?>
					<div id="activation_fields_container">
						<p class="activatior_description">
							<?php esc_html_e( 'The plugin can be activate in only <strong>two</strong> domains and they cannot be unregistered. For each activated domain, you can reactivate <strong>unlimited</strong> times (including <strong>subdomains</strong> and <strong>subfolders</strong>). The "localhost" domain will not consume activations. Please enter the following data and hit the activation button', 'woocommerce-order-approval' ); ?>
						</p>
						<div class="fields_blocks_container">
							<div class="inline-container">
								<input type="hidden" id="domain" value="<?php esc_attr_e($domain);?>"></input>
								<input type="hidden" id="item_id" value="<?php esc_attr_e($this->plugin_id);?>"></input>
								<label><?php esc_html_e( 'Buyer', 'woocommerce-order-approval' ); ?></label>
								<p  class="field_description"><?php esc_html_e( 'Insert the Envato username used to purchase the plugin.', 'woocommerce-order-approval' ); ?></p>
								<input type="text" value="" id="input_buyer" class="input_field" placeholder="<?php esc_html_e( 'Example: vanquish', 'woocommerce-order-approval' ); ?>"></input>
							</div>
							<div class="inline-container">
								<label><?php esc_html_e( 'Purchase code', 'woocommerce-order-approval' ); ?></label>
								<p  class="field_description"><?php esc_html_e( 'Insert the purchase code. It can be downloaded from your CodeCanyon "Downloads" profile page.', 'woocommerce-order-approval' ); ?></p>
								<input type="text" value="" class="input_field" id="input_purchase_code" placeholder="<?php esc_html_e( 'Example: 7d7c3rt8-f512-227c-8c98-fc53c3b212fe', 'woocommerce-order-approval' ); ?>"></input>
							</div>
							<button class="button button-primary" id="activation_button"><?php esc_html_e( 'Activate', 'woocommerce-order-approval' ); ?></button>
						</div>
						<div id="status"><?php esc_html_e( 'Verifing, please wait...', 'woocommerce-order-approval' ); ?></div>
					</div>
				<?php else: ?>
					<p class="activatior_description"><?php esc_html_e( 'The plugin has been successfully activated!', 'woocommerce-order-approval' ); ?></p>
				<?php endif; ?>
			</div>
		<?php
	}
	
}
?>