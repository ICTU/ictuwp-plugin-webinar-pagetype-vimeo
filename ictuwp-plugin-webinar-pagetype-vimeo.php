<?php
/**
 * @package           ictuwp-plugin-webinar-pagetype-vimeo
 *
 * @wordpress-plugin
 * Plugin Name:       ICTU / Gebruiker Centraal / Page template voor Vimeo webinars
 * Plugin URI:        https://github.com/ICTU/ICTU-Gebruiker-Centraal-Beelden-en-Brieven-CPTs-and-taxonomies
 * Description:       Plugin voor www.gebruikercentraal.nl e.a: embedden van een vimeowebinar
 * Version:           0.0.1
 * Version descr:     Eerste versie
 * Author:            Paul van Buuren
 * Author URI:        https://wbvb.nl/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ictuwp-plugin-webinar
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

//========================================================================================================
// naam van paginatemplate
if ( ! defined( 'ICTUWP_VIMEO_EMBED_TEMPLATE' ) ) {
	define( 'ICTUWP_VIMEO_EMBED_TEMPLATE', 'page_webinar.php' );
}
if ( ! defined( 'ICTUWP_VIMEO_EMBED_TEMPLATE_GENESIS' ) ) {
	define( 'ICTUWP_VIMEO_EMBED_TEMPLATE_GENESIS', 'page_webinar_genesis.php' );
}

// load translations
add_action( 'plugins_loaded', 'ictuwp_vimeoembed_load_plugin_textdomain' );

// add the page template to the templates list
add_filter( 'theme_page_templates', 'ictuwp_vimeoembed_add_page_template' );

// use the page template when necessary
add_filter( 'template_include', 'ictuwp_vimeoembed_use_page_template' );


//========================================================================================================

/**
 * Append a new page template for the embed page
 *
 * @since    1.0.0
 */
function ictuwp_vimeoembed_add_page_template( $post_templates ) {

	$post_templates[ ICTUWP_VIMEO_EMBED_TEMPLATE_GENESIS ] = _x( 'Webinar embed vimeo (2016)', "naam template", 'ictuwp-plugin-webinar' );
	$post_templates[ ICTUWP_VIMEO_EMBED_TEMPLATE ]         = _x( 'Webinar embed vimeo (2020)', "naam template", 'ictuwp-plugin-webinar' );

	return $post_templates;

}

//========================================================================================================

/**
 * Load the plugin text domain for translation.
 *
 * @since    1.0.0
 */
function ictuwp_vimeoembed_load_plugin_textdomain() {

	load_plugin_textdomain(
		'ictuwp-plugin-webinar',
		false,
		dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
	);

}

//========================================================================================================

/**
 * Use the page template when necessary
 *
 * @since    1.0.0
 */
function ictuwp_vimeoembed_use_page_template( $page_template ) {

	global $post;
	$checktemplate = get_post_meta( get_the_id(), '_wp_page_template', true );

	if ( ICTUWP_VIMEO_EMBED_TEMPLATE == $checktemplate ) {

		// TODO check in bouwen of Genesis actief of niet
		// use page template from plugin
		$page_template = dirname( __FILE__ ) . '/public/page_webinar_genesis.php';

	}

	return $page_template;
}

//========================================================================================================

/**
 * Use the page template when necessary
 *
 * @since    1.0.0
 */
function ictuwp_vimeoembed_add_acf_folder( $paths ) {

	// append path
	$paths[] = plugin_dir_path( __FILE__ ) . 'acf-json';

	// return
	return $paths;

}

add_filter( 'acf/settings/load_json', 'ictuwp_vimeoembed_add_acf_folder' );

//========================================================================================================

function ictuwp_vimeoembed_acf_json_save_point( $path ) {

	// update path
	$path = plugin_dir_path( __FILE__ ) . 'acf-json';

	// return
	return $path;

}

add_filter( 'acf/settings/save_json', 'ictuwp_vimeoembed_acf_json_save_point' );

//========================================================================================================


// HIERONDER ERFENIS

