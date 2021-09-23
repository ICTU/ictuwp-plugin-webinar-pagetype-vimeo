<?php

add_action( 'genesis_entry_content', 'gc_do_vimeo_embed', 11 );

add_action( 'wp_enqueue_scripts', 'gc_append_vimeo_scripts' );

remove_action( 'genesis_after_header', 'genesis_do_nav' );

//========================================================================================================

function gc_append_vimeo_scripts() {

	global $post;

	if ( ! is_admin() ) {

		$vimeourl = get_field( 'vimeo_url', $post->ID );

		if ( $vimeourl ):
			// if a vimeo URL is added, append the embed scripts
			wp_enqueue_script( 'vimeo-scripts', 'https://player.vimeo.com/api/player.js', '', '', true );

		endif;
	}
}

//========================================================================================================

function gc_do_vimeo_embed() {

	global $post;

	// get the ACF field value
	$vimeourl = get_field( 'vimeo_url', $post->ID );

	if ( $vimeourl ):

		// for example:https://vimeo.com/live/611413510/embed
		$vimeo_id     = 0;
		$vimeo_width  = 600;
		$vimeo_height = 600;

		$array_vimeo = explode( '/', $vimeourl );
		foreach ( $array_vimeo as $item ) {
			if ( is_numeric( $item ) ) {
				// retrieve the ID, this should be a number
				$vimeo_id = $item;
			}
		}


		if ( $vimeo_id ) {
			echo '<p><a href="' . $vimeourl . '">' . _x( 'Link naar vimeo', 'gebruikercentraal' ) . '</a></p>';

			echo '<div style="padding:56.25% 0 0 0;position:relative;border: 2px solid red;">';
			echo '<iframe src="https://vimeo.com/event/' . $vimeo_id . '/embed" ';
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

genesis();
