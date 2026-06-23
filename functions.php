<?php

function nestcoworking_enqueue_styles() {
	wp_enqueue_style( 'nestcoworking-style', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );
}

add_action( 'wp_enqueue_scripts', 'nestcoworking_enqueue_styles' );
