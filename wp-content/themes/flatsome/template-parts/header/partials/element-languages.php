<?php
/**
 * Custom Languages dropdown
 */

$current_lang = 'Languages';
$flag         = null;
$languages    = null;

// Polylang elseif WMPL.
if ( function_exists( 'pll_the_languages' ) ) {
	$languages = pll_the_languages( array( 'raw' => 1 ) );
	foreach ( $languages as $lang ) {
		if ( $lang['current_lang'] ) {
			$flag         = '<i class="image-icon"><img src="' . $lang['flag'] . '" alt="' . $lang['name'] . '"/></i>';
			$current_lang = $lang['name'];
		}
	}
} elseif ( function_exists( 'icl_get_languages' ) ) {
	$languages = icl_get_languages();
	foreach ( $languages as $lang ) {
		if ( $lang['active'] ) {
			$flag         = '<i class="image-icon"><img src="' . $lang['country_flag_url'] . '" alt="' . $lang['native_name'] . '"/></i>';
			$current_lang = $lang['native_name'];
		}
	}
}
?>

	<?php echo do_shortcode('[gtranslate]'); ?>
	
	