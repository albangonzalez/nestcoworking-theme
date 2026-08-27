import { store, getContext, getElement } from '@wordpress/interactivity';

store( 'nestcoworking/checkout-button', {
	actions: {
		open() {
			getContext().isOpen = true;
		},
		close() {
			getContext().isOpen = false;
		},
		handleBackdropClick( event ) {
			const { ref } = getElement();
			if ( event.target === ref ) {
				getContext().isOpen = false;
			}
		},
		setStartAt( event ) {
			getContext().startAt = event.target.value;
		},
		async checkout() {
			const context = getContext();

			if ( context.requiresStartDate && ! context.startAt ) {
				context.error = 'Elige una fecha de inicio antes de continuar.';
				return;
			}

			context.error = '';
			context.isLoading = true;

			try {
				const response = await fetch( '/wp-json/nestcoworking/v1/checkout', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-WP-Nonce': context.nonce,
					},
					body: JSON.stringify( {
						planId: context.planId,
						startAt: context.startAt || null,
					} ),
				} );

				const data = await response.json();

				if ( ! response.ok || ! data.init_point ) {
					throw new Error( data.message || 'No se pudo iniciar el pago.' );
				}

				window.location.assign( data.init_point );
			} catch ( error ) {
				context.isLoading = false;
				context.error = error.message;
			}
		},
	},
	callbacks: {
		syncOpenState() {
			const { ref } = getElement();
			const { isOpen } = getContext();

			if ( isOpen && ! ref.open ) {
				ref.showModal();
			} else if ( ! isOpen && ref.open ) {
				ref.close();
			}
		},
	},
} );
