<?php
/**
 * Server-side render for the nestcoworking/modal block.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Rendered inner blocks (the modal body).
 * @var WP_Block $block      Block instance.
 */

$trigger_text = ! empty( $attributes['triggerText'] ) ? $attributes['triggerText'] : __( 'Abrir', 'nestcoworking' );

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'data-wp-interactive' => 'nestcoworking/modal',
	)
);
?>
<div <?php echo $wrapper_attributes; ?> <?php echo wp_interactivity_data_wp_context( array( 'isOpen' => false ) ); ?>>
	<button
		type="button"
		class="wp-block-nestcoworking-modal__trigger wp-element-button"
		aria-haspopup="dialog"
		data-wp-on--click="actions.open"
	><?php echo wp_kses_post( $trigger_text ); ?></button>

	<dialog
		class="wp-block-nestcoworking-modal__dialog"
		aria-label="<?php echo esc_attr( wp_strip_all_tags( $trigger_text ) ); ?>"
		data-wp-watch="callbacks.syncOpenState"
		data-wp-on--close="actions.close"
		data-wp-on--click="actions.handleBackdropClick"
	>
		<button
			type="button"
			class="wp-block-nestcoworking-modal__close"
			aria-label="<?php esc_attr_e( 'Cerrar', 'nestcoworking' ); ?>"
			data-wp-on--click="actions.close"
		>&times;</button>
		<div class="wp-block-nestcoworking-modal__content">
			<?php echo $content; ?>
		</div>
	</dialog>
</div>
