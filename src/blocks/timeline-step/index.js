import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import Edit from './edit';
import save from './save';
import metadata from './block.json';

const inlineIcon = (
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
        <rect x="2" y="7" width="5" height="5" />
        <rect x="7" y="8.75" width="15" height="1.5" />
        <rect x="2" y="15" width="14" height="1.5" />
    </svg>
);

registerBlockType(metadata.name, {
    icon: inlineIcon,
    edit: Edit,
    save
});
