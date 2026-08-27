<?php
/**
 * Server-side render for the nestcoworking/checkout-button block.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Rendered inner blocks (unused, block has none).
 * @var WP_Block $block      Block instance.
 */

$plan_id             = ! empty( $attributes['planId'] ) ? $attributes['planId'] : '';
$button_text         = ! empty( $attributes['buttonText'] ) ? $attributes['buttonText'] : __( 'Comprar en línea', 'nestcoworking' );
$requires_start_date = ! empty( $attributes['requiresStartDate'] );

$context = array(
	'planId'            => $plan_id,
	'requiresStartDate' => $requires_start_date,
	'nonce'             => wp_create_nonce( 'wp_rest' ),
	'isLoading'         => false,
	'isOpen'            => false,
	'startAt'           => '',
	'error'             => '',
);

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'data-wp-interactive' => 'nestcoworking/checkout-button',
	)
);
?>
<div <?php echo $wrapper_attributes; ?> <?php echo wp_interactivity_data_wp_context( $context ); ?>>
	<?php if ( $requires_start_date ) : ?>
		<div class="wp-block-buttons">
			<div class="wp-block-button">
				<button
					type="button"
					class="wp-block-button__link wp-element-button"
					aria-haspopup="dialog"
					data-wp-on--click="actions.open"
				><?php echo wp_kses_post( $button_text ); ?></button>
			</div>
		</div>

		<dialog
			class="wp-block-nestcoworking-checkout-button__dialog"
			aria-label="<?php echo esc_attr( wp_strip_all_tags( $button_text ) ); ?>"
			data-wp-watch="callbacks.syncOpenState"
			data-wp-on--close="actions.close"
			data-wp-on--click="actions.handleBackdropClick"
		>
			<button
				type="button"
				class="wp-block-nestcoworking-checkout-button__close"
				aria-label="<?php esc_attr_e( 'Cerrar', 'nestcoworking' ); ?>"
				data-wp-on--click="actions.close"
			>&times;</button>
			<div class="wp-block-nestcoworking-checkout-button__content">
				<label for="mp-start-date-<?php echo esc_attr( $plan_id ); ?>">
					<?php esc_html_e( 'Fecha de inicio', 'nestcoworking' ); ?>
				</label>
				<input
					type="date"
					id="mp-start-date-<?php echo esc_attr( $plan_id ); ?>"
					data-wp-bind--value="context.startAt"
					data-wp-on--input="actions.setStartAt"
				/>
				<p
					class="wp-block-nestcoworking-checkout-button__error"
					data-wp-bind--hidden="!context.error"
					data-wp-text="context.error"
				></p>
				<button
					type="button"
					class="wp-block-button__link wp-element-button"
					data-wp-on--click="actions.checkout"
					data-wp-bind--disabled="context.isLoading"
				><?php esc_html_e( 'Confirmar y pagar', 'nestcoworking' ); ?></button>
			</div>
		</dialog>
	<?php else : ?>
		<div class="wp-block-buttons">
			<div class="wp-block-button">
				<button
					type="button"
					class="wp-block-button__link wp-element-button"
					data-wp-on--click="actions.checkout"
					data-wp-bind--disabled="context.isLoading"
				><?php echo wp_kses_post( $button_text ); ?></button>
			</div>
		</div>
		<p
			class="wp-block-nestcoworking-checkout-button__error"
			data-wp-bind--hidden="!context.error"
			data-wp-text="context.error"
		></p>
	<?php endif; ?>
</div>
