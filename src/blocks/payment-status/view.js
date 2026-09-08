import { store, getContext } from '@wordpress/interactivity';

const wait = ( ms ) => new Promise( ( resolve ) => setTimeout( resolve, ms ) );

store( 'nestcoworking/payment-status', {
	callbacks: {
		async startPolling() {
			const context = getContext();

			for ( let attempt = 0; attempt < context.maxAttempts; attempt++ ) {
				let data = null;

				try {
					const response = await fetch(
						`/wp-json/nestcoworking/v1/payment-status/${ encodeURIComponent(
							context.paymentId
						) }`
					);
					data = await response.json();
				} catch ( error ) {
					data = null;
				}

				if ( data && 'processed' === data.status ) {
					context.isPending = false;
					context.isProcessed = true;
					context.internetCodes = data.internet_codes || [];
					return;
				}

				if ( data && ( 'failed' === data.status || 'rejected' === data.status ) ) {
					context.isPending = false;
					context.isFailed = true;
					context.message = data.message || '';
					return;
				}

				await wait( context.pollIntervalMs );
			}

			context.isPending = false;
			context.isTimedOut = true;
		},
	},
} );
