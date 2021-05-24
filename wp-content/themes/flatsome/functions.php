<?php
/**
 * Flatsome functions and definitions
 *
 * @package flatsome
 */

update_option( get_template() . '_wup_purchase_code', '*******' );
update_option( get_template() . '_wup_supported_until', '01.01.2030' );
update_option( get_template() . '_wup_buyer', 'Babiato' );


require get_template_directory() . '/inc/init.php';

/**
 * Note: It's not recommended to add any custom code here. Please use a child theme so that your customizations aren't lost during updates.
 * Learn more here: http://codex.wordpress.org/Child_Themes
 */
