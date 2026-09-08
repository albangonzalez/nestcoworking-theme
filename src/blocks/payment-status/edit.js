import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

export default function Edit() {
	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<p className="wp-block-nestcoworking-payment-status__hint">
				{ __(
					'Muestra el estado del pago (según ?payment_id= en la URL) al cargar la página. Colócalo en la página de agradecimiento de Mercado Pago.',
					'nestcoworking'
				) }
			</p>
		</div>
	);
}
