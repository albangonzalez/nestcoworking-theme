import { store, getContext, getElement } from '@wordpress/interactivity';

store( 'nestcoworking/modal', {
	actions: {
		open() {
			getContext().isOpen = true;
		},
		close() {
			getContext().isOpen = false;
		},
		handleBackdropClick( event ) {
			// A click lands directly on the <dialog> element only when it
			// hits the ::backdrop area, since the content wrapper covers
			// the dialog box itself.
			const { ref } = getElement();
			if ( event.target === ref ) {
				getContext().isOpen = false;
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
