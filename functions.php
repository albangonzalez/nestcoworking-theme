<?php

function nestcoworking_register_blocks() {
	register_block_type( __DIR__ . '/build/blocks/hero' );
}

function nestcoworking_enqueue_styles() {
	wp_enqueue_style( 'nestcoworking-style', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );
}

add_action( 'init', 'nestcoworking_register_blocks' );
add_action( 'wp_enqueue_scripts', 'nestcoworking_enqueue_styles' );