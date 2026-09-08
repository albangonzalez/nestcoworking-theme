<?php

function nestcoworking_mp_catalog() {
	$catalog = array(
		'4-hours'    => array(
			'title'               => 'Coworking: 4 Hours',
			'unit_price'          => 200,
			'currency_id'         => 'MXN',
			'requires_start_date' => false,
		),
		'day-pass'   => array(
			'title'               => 'Coworking: Day Pass',
			'unit_price'          => 300,
			'currency_id'         => 'MXN',
			'requires_start_date' => false,
		),
		'week-pass'  => array(
			'title'               => 'Coworking: Week Pass',
			'unit_price'          => 1400,
			'currency_id'         => 'MXN',
			'requires_start_date' => true,
			'duration_days'       => 7,
		),
		'month-pass' => array(
			'title'               => 'Coworking: Month Pass',
			'unit_price'          => 3500,
			'currency_id'         => 'MXN',
			'requires_start_date' => true,
			'duration_days'       => 30,
		),
	);

	return apply_filters( 'nestcoworking_mp_catalog', $catalog );
}

function nestcoworking_mp_get_settings() {
	return wp_parse_args(
		get_option( 'mercadopago_settings', array() ),
		array(
			'access_token'     => '',
			'success_url'      => home_url( '/subscribe' ),
			'failure_url'      => home_url( '/subscribe' ),
			'pending_url'      => home_url( '/subscribe' ),
			'notification_url' => 'https://sys.nestcoworking.com.mx/api/v1/webhooks/mercadopago',
			'status_api_base'  => 'https://sys.nestcoworking.com.mx/api/v1/payments',
		)
	);
}

function nestcoworking_mp_seed_settings_option() {
	add_option(
		'mercadopago_settings',
		array(
			'access_token' => '',
		)
	);
}
add_action( 'after_setup_theme', 'nestcoworking_mp_seed_settings_option' );

function nestcoworking_mp_register_routes() {
	register_rest_route(
		'nestcoworking/v1',
		'/checkout',
		array(
			'methods'             => 'POST',
			'callback'            => 'nestcoworking_mp_create_preference',
			'permission_callback' => '__return_true',
			'args'                => array(
				'planId'    => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'startAt'   => array(
					'required'          => false,
					'type'              => array( 'string', 'null' ),
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		)
	);

	register_rest_route(
		'nestcoworking/v1',
		'/payment-status/(?P<payment_id>[\w.-]+)',
		array(
			'methods'             => 'GET',
			'callback'            => 'nestcoworking_mp_get_payment_status',
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'nestcoworking_mp_register_routes' );

/**
 * Proxies the NestSys payment status lookup so the browser never calls
 * sys.nestcoworking.com.mx directly (that endpoint has no auth yet).
 */
function nestcoworking_mp_get_payment_status( WP_REST_Request $request ) {
	$settings   = nestcoworking_mp_get_settings();
	$payment_id = $request->get_param( 'payment_id' );

	$response = wp_remote_get(
		trailingslashit( $settings['status_api_base'] ) . rawurlencode( $payment_id ),
		array( 'timeout' => 10 )
	);

	if ( is_wp_error( $response ) ) {
		return rest_ensure_response( array( 'status' => 'pending' ) );
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( ! is_array( $data ) || empty( $data['status'] ) ) {
		return rest_ensure_response( array( 'status' => 'pending' ) );
	}

	return rest_ensure_response( $data );
}

function nestcoworking_mp_create_preference( WP_REST_Request $request ) {
	$settings = nestcoworking_mp_get_settings();

	if ( empty( $settings['access_token'] ) ) {
		return new WP_Error(
			'nestcoworking_mp_not_configured',
			__( 'Mercado Pago no está configurado todavía.', 'nestcoworking' ),
			array( 'status' => 500 )
		);
	}

	$catalog = nestcoworking_mp_catalog();
	$plan_id = $request->get_param( 'planId' );

	if ( empty( $plan_id ) || ! isset( $catalog[ $plan_id ] ) ) {
		return new WP_Error(
			'nestcoworking_mp_invalid_plan',
			__( 'El plan seleccionado no es válido.', 'nestcoworking' ),
			array( 'status' => 400 )
		);
	}

	$plan     = $catalog[ $plan_id ];
	$start_at = $request->get_param( 'startAt' );
	$today    = wp_date( 'Y-m-d' );

	if ( $plan['requires_start_date'] ) {
		$is_valid_date = $start_at
			&& preg_match( '/^\d{4}-\d{2}-\d{2}$/', $start_at )
			&& false !== strtotime( $start_at )
			&& $start_at > $today;

		if ( ! $is_valid_date ) {
			return new WP_Error(
				'nestcoworking_mp_invalid_start_date',
				__( 'Elige una fecha de inicio válida.', 'nestcoworking' ),
				array( 'status' => 400 )
			);
		}
	}

	$item_title = $plan['title'];

	if ( $plan['requires_start_date'] ) {
		$end_at = gmdate( 'Y-m-d', strtotime( $start_at . ' +' . $plan['duration_days'] . ' days' ) );

		$item_title .= sprintf(
			' (from %s to %s)',
			gmdate( 'j M Y', strtotime( $start_at ) ),
			gmdate( 'j M Y', strtotime( $end_at ) )
		);
	}

	$body = array(
		'items'      => array(
			array(
				'title'       => $item_title,
				'quantity'    => 1,
				'currency_id' => $plan['currency_id'],
				'unit_price'  => $plan['unit_price'],
			),
		),
		'back_urls'  => array(
			'success' => $settings['success_url'],
			'failure' => $settings['failure_url'],
			'pending' => $settings['pending_url'],
		),
		'notification_url'   => $settings['notification_url'],
		'external_reference' => $plan_id . '-' . time(),
	);

	if ( 'https' === wp_parse_url( $settings['success_url'], PHP_URL_SCHEME ) ) {
		$body['auto_return'] = 'approved';
	}

	if ( $plan['requires_start_date'] ) {
		$body['metadata'] = array(
			'plan_id'  => $plan_id,
			'start_at' => $start_at,
			'end_at'   => $end_at,
		);
	}

	$response = wp_remote_post(
		'https://api.mercadopago.com/checkout/preferences',
		array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $settings['access_token'],
				'Content-Type'  => 'application/json',
			),
			'body'    => wp_json_encode( $body ),
			'timeout' => 15,
		)
	);

	if ( is_wp_error( $response ) ) {
		return new WP_Error(
			'nestcoworking_mp_request_failed',
			__( 'No se pudo contactar a Mercado Pago.', 'nestcoworking' ),
			array( 'status' => 502 )
		);
	}

	$status = wp_remote_retrieve_response_code( $response );
	$data   = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( $status < 200 || $status >= 300 || empty( $data['id'] ) ) {
		return new WP_Error(
			'nestcoworking_mp_preference_failed',
			__( 'No se pudo generar el pago. Intenta de nuevo.', 'nestcoworking' ),
			array( 'status' => 502 )
		);
	}

	$is_sandbox = 0 === strpos( $settings['access_token'], 'TEST-' );
	$init_point = $is_sandbox && ! empty( $data['sandbox_init_point'] )
		? $data['sandbox_init_point']
		: $data['init_point'];

	return rest_ensure_response(
		array(
			'init_point' => $init_point,
		)
	);
}
