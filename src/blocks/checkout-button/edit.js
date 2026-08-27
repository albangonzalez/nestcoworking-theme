import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl } from '@wordpress/components';

const PLANS = [
	{ value: '', label: __( 'Select a plan…', 'nestcoworking' ) },
	{ value: '4-hours', label: __( '4 Hours', 'nestcoworking' ) },
	{ value: 'day-pass', label: __( 'Day Pass', 'nestcoworking' ) },
	{ value: 'week-pass', label: __( 'Week Pass', 'nestcoworking' ) },
	{ value: 'month-pass', label: __( 'Month Pass', 'nestcoworking' ) },
];

export default function Edit( { attributes, setAttributes } ) {
	const { planId, buttonText, requiresStartDate } = attributes;
	const blockProps = useBlockProps();

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Mercado Pago', 'nestcoworking' ) }>
					<SelectControl
						label={ __( 'Plan', 'nestcoworking' ) }
						value={ planId }
						options={ PLANS }
						onChange={ ( value ) => setAttributes( { planId: value } ) }
					/>
					<ToggleControl
						label={ __( 'Ask for a start date before checkout', 'nestcoworking' ) }
						checked={ requiresStartDate }
						onChange={ ( value ) => setAttributes( { requiresStartDate: value } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<RichText
					tagName="span"
					className="wp-block-button__link wp-element-button"
					value={ buttonText }
					onChange={ ( value ) => setAttributes( { buttonText: value } ) }
					placeholder={ __( 'Add button text…', 'nestcoworking' ) }
					allowedFormats={ [] }
				/>
				{ ! planId && (
					<p className="wp-block-nestcoworking-checkout-button__hint">
						{ __( 'Choose a plan in the block settings.', 'nestcoworking' ) }
					</p>
				) }
			</div>
		</>
	);
}
