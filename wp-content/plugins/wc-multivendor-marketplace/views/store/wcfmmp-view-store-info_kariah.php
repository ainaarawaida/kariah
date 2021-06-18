<?php
/**
 * The Template for displaying all store reviews.
 *
 * @package WCfM Markeplace Views Store
 *
 * For edit coping this to yourtheme/wcfm/store 
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $WCFM, $WCFMmp, $post;

if( $post ) {
	$pagination_base = str_replace( $post->ID, '%#%', esc_url( get_pagenum_link( $post->ID ) ) );
} else {
	$pagination_base = str_replace( 1, '%#%', esc_url( get_pagenum_link( 1 ) ) );
}

$paged  = max( 1, get_query_var( 'paged' ) );
$length = 10;
$offset = ( $paged - 1 ) * $length;

$wcfm_review_categories = get_wcfm_marketplace_active_review_categories();

$total_review_count = $store_user->get_total_review_count();
$latest_reviews     = $store_user->get_lastest_reviews( $offset, $length );
?>

<div class="_area" id="info_kariah">

<?php do_action( 'luq_info_kariah_tab_page', $store_user->get_id() ); ?>
	
</div>