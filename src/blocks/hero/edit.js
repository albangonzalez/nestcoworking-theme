import { useSelect } from '@wordpress/data';
import { useBlockProps } from '@wordpress/block-editor';

function getEditor( select ) {
	const editor = select( 'core/editor' );
	return editor && editor.getEditedPostAttribute ? editor : null;
}

export default function Edit() {
	const title = useSelect( ( select ) => {
		const editor = getEditor( select );
		return editor ? editor.getEditedPostAttribute( 'title' ) : '';
	}, [] );

	const imageUrl = useSelect( ( select ) => {
		const editor = getEditor( select );
		const featuredMediaId = editor ? editor.getEditedPostAttribute( 'featured_media' ) : 0;

		if ( ! featuredMediaId ) {
			return '';
		}

		const media = select( 'core' ).getMedia( featuredMediaId );
		return media ? media.source_url : '';
	}, [] );

	// Always preview the full hero, even without a featured image —
	// the editor doesn't need to mirror the front end's "render nothing" fallback.
	const blockProps = useBlockProps( {
		style: imageUrl
			? { backgroundImage: `url(${ imageUrl })` }
			: { backgroundColor: 'var(--wp--preset--color--primary)' },
	} );

	return (
		<div { ...blockProps }>
			<h1 className="wp-block-nestcoworking-hero__title">{ title || 'Page title' }</h1>
		</div>
	);
}
