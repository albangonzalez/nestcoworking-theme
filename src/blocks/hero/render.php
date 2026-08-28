<?php
/**
 * Server-side render for the nestcoworking/hero block.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Rendered inner blocks (unused, block has none).
 * @var WP_Block $block      Block instance.
 */

$image_id  = get_post_thumbnail_id();
$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';

if ( ! $image_url ) {
	return;
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'style' => 'background-image:url(' . esc_url( $image_url ) . ');',
	)
);
?>
<div <?php echo $wrapper_attributes; ?>>
	<h1 class="wp-block-nestcoworking-hero__title"><?php echo esc_html( get_the_title() ); ?></h1>
</div>
