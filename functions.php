<?php

function nestcoworking_enqueue_styles() {
	wp_enqueue_style( 'nestcoworking-style', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );
}

add_action( 'wp_enqueue_scripts', 'nestcoworking_enqueue_styles' );

function nestcoworking_register_blocks() {
	register_block_type( __DIR__ . '/build/blocks/modal' );
}

add_action( 'init', 'nestcoworking_register_blocks' );

function nestcoworking_block_categories( $categories ) {
	return array_merge(
		array(
			array(
				'slug'  => 'nestcoworking',
				'title' => __( 'Nest Coworking', 'nestcoworking' ),
			),
		),
		$categories
	);
}

add_filter( 'block_categories_all', 'nestcoworking_block_categories' );
