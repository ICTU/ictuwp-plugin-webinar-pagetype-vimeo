<?php

add_action( 'genesis_entry_content', 'ictuwp_vimeoembed_do_embed', 11 );

add_action( 'wp_enqueue_scripts', 'ictuwp_vimeoembed_append_vimeo_scripts' );

//========================================================================================================

genesis();
