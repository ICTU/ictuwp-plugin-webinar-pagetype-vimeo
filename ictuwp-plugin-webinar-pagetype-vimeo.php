<?php
/**
 * @package           ictuwp-plugin-webinar-pagetype-vimeo
 *
 * @wordpress-plugin
 * Plugin Name:       ICTU / Gebruiker Centraal / Page template voor Vimeo webinars
 * Plugin URI:        https://github.com/ICTU/ICTU-Gebruiker-Centraal-Beelden-en-Brieven-CPTs-and-taxonomies
 * Description:       Plugin voor www.gebruikercentraal.nl e.a: embedden van een vimeowebinar
 * Version:           1.1.2
 * Version descr:     Updated embed code clean-up to 2023 standards.
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

// in dit bestand staan de veld-definities voor de ACF-velden
require plugin_dir_path( __FILE__ ) . 'includes/acf-definitions.php';

//========================================================================================================
// naam van paginatemplate
if ( ! defined( 'ICTUWP_VIMEO_EMBED_TEMPLATE' ) ) {
	define( 'ICTUWP_VIMEO_EMBED_TEMPLATE', 'page_webinar.php' );
}
if ( ! defined( 'ICTUWP_VIMEO_EMBED_TEMPLATE_GENESIS' ) ) {
	define( 'ICTUWP_VIMEO_EMBED_TEMPLATE_GENESIS', 'page_webinar_genesis.php' );
}
if ( ! defined( 'ICTUWP_VIMEO_EMBED_VERSION' ) ) {
	define( 'ICTUWP_VIMEO_EMBED_VERSION', '1.1.2' );
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

	$post_templates[ ICTUWP_VIMEO_EMBED_TEMPLATE_GENESIS ] = _x( 'Webinar embed', "naam template", 'ictuwp-plugin-webinar' );

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

		// use page template from plugin
		$page_template = dirname( __FILE__ ) . '/public/' . ICTUWP_VIMEO_EMBED_TEMPLATE;

	} elseif ( ICTUWP_VIMEO_EMBED_TEMPLATE_GENESIS == $checktemplate ) {

		// use page template from plugin
		$page_template = dirname( __FILE__ ) . '/public/' . ICTUWP_VIMEO_EMBED_TEMPLATE_GENESIS;

	}

//	echo '<h1>$checktemplate: ' . $checktemplate . ': ' . $page_template . '</h1>';

	return $page_template;
}

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


	$infooter = true;

	if ( ! is_admin() && $post ) {

		$vimeo_embed = get_field( 'vimeo_embed', $post->ID );

		if ( $vimeo_embed ):
			// if a Vimeo webinar URL is added, append the embed scripts
			wp_enqueue_script( 'vimeo-scripts', 'https://player.vimeo.com/api/player.js', '', ICTUWP_VIMEO_EMBED_VERSION, $infooter );

		endif;

		wp_register_style( 'pagetemplate-webinar', plugin_dir_url( __FILE__ ) . 'public/css/webinar-pagetype.css', array(), ICTUWP_VIMEO_EMBED_VERSION );
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
	$vimeo_embed    = get_field( 'vimeo_embed', $post->ID );
	$chat_embed     = get_field( 'chat_embed', $post->ID );
	$chat_embed_url = '';

	if ( $vimeo_embed ):
		$vimeo_id        = 0;
		$webinarpadding  = '56.25%';
		$videoparameters = array(
			'badge'     => '0',
			'autopause' => '0',
			'player_id' => '0'
		);

		// de embed codes zijn leuk, maar omdat we niet alle gebruikersinput vertrouwen gaan we de code opschonen en
		// alleen de video ID / chat ID ophalen. De rest zetten we er zelf bij
		$stripby     = 'video/';
		$iframe_tag  = esc_html( strip_tags( $vimeo_embed, '<iframe>' ) );
		$array_vimeo = explode( $stripby, $iframe_tag );
		$vimeo_id    = $array_vimeo[1];
		if ( strpos( $vimeo_id, '?' ) ) {
			$array_vimeo_id = explode( '?', $vimeo_id );
			$vimeo_id       = $array_vimeo_id[0];
			if ( ! is_numeric( $vimeo_id ) ) {
				$vimeo_id = 0;
			}
		} else {
			// geen ? in de waarde, ws alleen de iframe tag
			$embedcode = new DOMDocument();
			$embedcode->loadHTML( $vimeo_embed );
			$iframe = $embedcode->getElementsByTagName( 'iframe' );

			foreach ( $iframe as $element ) {
				$src_attr = $element->getAttribute( 'src' );
				if ( $src_attr ) {
					$replace        = "https://vimeo.com/";
					$embed_url      = str_replace( $replace, '', $src_attr );
					$array_vimeo_id = explode( '/', $embed_url );
					$vimeo_id       = $array_vimeo_id[1];
					if ( ! is_numeric( $vimeo_id ) ) {
						$vimeo_id = 0;
					}
				}
			}
		}

		if ( $chat_embed ) {

			$embedcode = new DOMDocument();
			$embedcode->loadHTML( $chat_embed );
			$iframe = $embedcode->getElementsByTagName( 'iframe' );

			foreach ( $iframe as $element ) {
				$src_attr = $element->getAttribute( 'src' );
				if ( $src_attr ) {
					$replace        = "https://vimeo.com/";
					$embed_url      = str_replace( $replace, '', $src_attr );
					$array_vimeo_id = explode( '/', $embed_url );
					$chatid         = $array_vimeo_id[1];
					if ( ! is_numeric( $vimeo_id ) ) {
						$chatid = 0;
					}
				}
			}

			if ( $chatid ) {
				$webinarpadding = '45%';
				$chat_embed_url = 'https://vimeo.com/event/' . $chatid . '/chat/';
			}
		}



		if ( $vimeo_id ) {
			$embedurl = 'https://vimeo.com/event/' . $vimeo_id . '/embed';
			echo '<p><a href="https://vimeo.com/event/' . $vimeo_id . '/">' . _x( 'Link naar vimeo', 'gebruikercentraal' ) . '</a></p>';
			echo '<div id="vimeoembed">';

			if ( $chat_embed_url ) {
				echo '<div id="vimeo_chat">';
				echo '<iframe src="' . $chat_embed_url . '" width="100%" height="100%" frameborder="0"></iframe>';
				echo '</div>'; // #vimeo_chat
			}

			echo '<div style="padding-top:' . $webinarpadding . ';position:relative;" id="vimeo_webinar">';
			echo '<iframe src="' . $embedurl . '" ';
			echo 'frameborder="0" ';
			echo 'allow="autoplay; fullscreen; picture-in-picture" ';
			echo 'style="position:absolute;top:0;left:0;width:100%;height:100%;" ';
			echo 'webkitallowfullscreen mozallowfullscreen allowfullscreen';
			echo '></iframe>';
			echo '</div>'; // #vimeo_webinar


			echo '</div>'; // #vimeoembed
		} else {
			echo '<p>Geen ID gevonden</p>';
		}

	endif;

}

//========================================================================================================


