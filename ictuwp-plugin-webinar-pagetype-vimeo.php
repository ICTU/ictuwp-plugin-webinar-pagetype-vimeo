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

	$post_templates[ ICTUWP_VIMEO_EMBED_TEMPLATE_GENESIS ] = _x( 'Webinar embed (gc theme 2016)', "naam template", 'ictuwp-plugin-webinar' );
	$post_templates[ ICTUWP_VIMEO_EMBED_TEMPLATE ]         = _x( 'Webinar embed (gc theme 2020)', "naam template", 'ictuwp-plugin-webinar' );

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
 * Have the ACF (advanced custom fields) plugin read the settings from this plugin's acf-json folder
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

/**
 * Have the ACF (advanced custom fields) plugin save settings to a json file in this plugin's acf-json folder
 *
 * @since    1.0.0
 */
function ictuwp_vimeoembed_acf_json_save_point( $path ) {

	// update path
	$path = plugin_dir_path( __FILE__ ) . 'acf-json';

	// return
	return $path;

}

add_filter( 'acf/settings/save_json', 'ictuwp_vimeoembed_acf_json_save_point' );

//========================================================================================================

/**
 * Add JavaScript and stylesheets
 *
 * @global integer $post Post ID.
 *
 * @since    1.0.0
 */
function ictuwp_vimeoembed_append_vimeo_scripts() {

	global $post;

	if ( ! is_admin() && $post ) {

		$vimeourl = get_field( 'vimeo_url', $post->ID );

		if ( $vimeourl ):
			// if a Vimeo webinar URL is added, append the embed scripts
			wp_enqueue_script( 'vimeo-scripts', 'https://player.vimeo.com/api/player.js', '', '', true );

		endif;

		wp_register_style( 'pagetemplate-webinar', plugin_dir_url( __FILE__ ) . 'public/css/webinar-pagetype.css', array(), '' );
		wp_enqueue_style( 'pagetemplate-webinar' );



	}
}

//========================================================================================================

/**
 * Use the ACF field to embed a vimeo video object
 *
 * @global integer $post Post ID.
 *
 * @since    1.0.0
 */
function ictuwp_vimeoembed_do_embed() {

	global $post;

	// get the ACF field value
	$vimeourl = get_field( 'vimeo_url', $post->ID );

	if ( $vimeourl ):

		// for example:https://vimeo.com/live/611413510/embed
		$vimeo_id     = 0;
		$vimeo_width  = 600;
		$vimeo_height = 600;
		$attrs = array();

		$array_vimeo = explode( '/', $vimeourl );
		foreach ( $array_vimeo as $item ) {
			if ( is_numeric( $item ) ) {
				// retrieve the ID, this should be a number
				$vimeo_id = $item;
			}
		}

//		$extra = '?h=a3cc83fe36&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479';
		$extra = '?badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479';

		$embedurl = 'https://player.vimeo.com/video/' . $vimeo_id . $extra;
/*
 * <div style="padding:56.25% 0 0 0;position:relative;"><iframe src="https://player.vimeo.com/video/613626035?h=a3cc83fe36&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen style="position:absolute;top:0;left:0;width:100%;height:100%;" title="test van paul"></iframe></div><script src="https://player.vimeo.com/api/player.js"></script>
 */

		if ( $vimeo_id ) {
			echo '<p><a href="' . $vimeourl . '">' . _x( 'Link naar vimeo', 'gebruikercentraal' ) . '</a></p>';
			echo '<div style="padding:56.25% 0 0 0;position:relative;border:0.2rem solid #6E9CA5">';
			echo '<iframe src="' . $embedurl . '" ';
			echo 'frameborder="0" ';
			echo 'allow="autoplay; fullscreen; picture-in-picture" ';
			echo 'style="position:absolute;top:0;left:0;width:100%;height:100%;" ';
			echo 'webkitallowfullscreen mozallowfullscreen allowfullscreen';
			echo '></iframe>';
			echo '</div>';
		} else {

		}

	endif;

}

//========================================================================================================


