<?php
/**
 * Server-side render for the nestcoworking/payment-status block.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Rendered inner blocks (unused, block has none).
 * @var WP_Block $block      Block instance.
 */

$payment_id = isset( $_GET['payment_id'] ) ? sanitize_text_field( wp_unslash( $_GET['payment_id'] ) ) : '';

if ( empty( $payment_id ) ) :
	?>
	<div <?php echo get_block_wrapper_attributes(); ?>>
		<p class="wp-block-nestcoworking-payment-status__message">
			<?php esc_html_e( 'No encontramos información de tu pago. Si ya realizaste el pago, contáctanos y con gusto te ayudamos.', 'nestcoworking' ); ?>
		</p>
	</div>
	<?php
	return;
endif;

$context = array(
	'paymentId'      => $payment_id,
	'pollIntervalMs' => ! empty( $attributes['pollIntervalMs'] ) ? (int) $attributes['pollIntervalMs'] : 3000,
	'maxAttempts'    => ! empty( $attributes['maxAttempts'] ) ? (int) $attributes['maxAttempts'] : 15,
	'isPending'      => true,
	'isProcessed'    => false,
	'isFailed'       => false,
	'isTimedOut'     => false,
	'message'        => '',
	'internetCodes'  => array(),
);

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'data-wp-interactive' => 'nestcoworking/payment-status',
		'data-wp-init'        => 'callbacks.startPolling',
	)
);
?>
<div <?php echo $wrapper_attributes; ?> <?php echo wp_interactivity_data_wp_context( $context ); ?>>
	<div
		class="wp-block-nestcoworking-payment-status__pending"
		data-wp-bind--hidden="!context.isPending"
	>
		<span class="wp-block-nestcoworking-payment-status__spinner" aria-hidden="true"></span>
		<p><?php esc_html_e( 'Estamos confirmando tu pago…', 'nestcoworking' ); ?></p>
	</div>

	<div
		class="wp-block-nestcoworking-payment-status__processed"
		data-wp-bind--hidden="!context.isProcessed"
	>
		<p><?php esc_html_e( '¡Listo! Tu pago fue confirmado. Estos son tus datos de acceso a la red:', 'nestcoworking' ); ?></p>
		<template data-wp-each="context.internetCodes">
			<div class="wp-block-nestcoworking-payment-status__code">
				<p class="wp-block-nestcoworking-payment-status__code-plan" data-wp-text="context.item.plan"></p>
				<p>
					<?php esc_html_e( 'Usuario:', 'nestcoworking' ); ?>
					<strong data-wp-text="context.item.username"></strong>
				</p>
				<p>
					<?php esc_html_e( 'Contraseña:', 'nestcoworking' ); ?>
					<strong data-wp-text="context.item.password"></strong>
				</p>
				<p>
					<?php esc_html_e( 'Vigencia:', 'nestcoworking' ); ?>
					<span data-wp-text="context.item.start_date"></span> – <span data-wp-text="context.item.expiration_date"></span>
				</p>
			</div>
		</template>
		<p class="wp-block-nestcoworking-payment-status__hint">
			<?php esc_html_e( 'También te enviamos estos datos por correo electrónico.', 'nestcoworking' ); ?>
		</p>
	</div>

	<div
		class="wp-block-nestcoworking-payment-status__failed"
		data-wp-bind--hidden="!context.isFailed"
	>
		<p><?php esc_html_e( 'No pudimos confirmar tu pago. Si el cargo se realizó, contáctanos por WhatsApp o correo y con gusto te ayudamos; si no, puedes intentar tu compra de nuevo.', 'nestcoworking' ); ?></p>
	</div>

	<div
		class="wp-block-nestcoworking-payment-status__timeout"
		data-wp-bind--hidden="!context.isTimedOut"
	>
		<p><?php esc_html_e( 'Tu pago se sigue procesando. En cuanto se confirme recibirás tus datos de acceso por correo electrónico.', 'nestcoworking' ); ?></p>
	</div>
</div>
