import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

const TEMPLATE = [
	[ 'core/heading', { level: 3, placeholder: __( 'Modal title', 'nestcoworking' ) } ],
	[ 'core/paragraph', { placeholder: __( 'Modal content…', 'nestcoworking' ) } ],
];

export default function Edit( { attributes, setAttributes } ) {
	const { triggerText } = attributes;
	const blockProps = useBlockProps();
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'wp-block-nestcoworking-modal__content' },
		{ template: TEMPLATE }
	);

	return (
		<div { ...blockProps }>
			<RichText
				tagName="span"
				className="wp-block-nestcoworking-modal__trigger wp-element-button"
				value={ triggerText }
				onChange={ ( value ) => setAttributes( { triggerText: value } ) }
				placeholder={ __( 'Add trigger text…', 'nestcoworking' ) }
				allowedFormats={ [] }
			/>
			<p className="wp-block-nestcoworking-modal__hint">
				{ __( 'Modal content (revealed when the button above is clicked on the front end):', 'nestcoworking' ) }
			</p>
			<div { ...innerBlocksProps } />
		</div>
	);
}
